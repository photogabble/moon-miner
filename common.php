<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: common.php

if (strpos($_SERVER['PHP_SELF'], 'common.php')) // Prevent direct access to this file
{
    die('Blacknova Traders error: You cannot access this file directly.');
}

require_once './vendor/autoload.php';              // Load the auto-loader
require_once './global_defines.php';               // Defines used in many places
ini_set('include_path', '.');                      // This seems to be a problem on a few platforms, so we manually set it to avoid those problems.
ini_set('session.use_only_cookies', 1);            // Ensure that sessions will only be stored in a cookie
ini_set('session.cookie_httponly', 1);             // Make the session cookie HTTP only, a flag that helps ensure that javascript cannot tamper with the session cookie
ini_set('session.use_trans_sid', 0);               // Prevent session ID from being put in URLs
ini_set('session.entropy_file', '/dev/urandom');   // Use urandom as entropy source, to help the random number generator
ini_set('session.entropy_length', '512');          // Increase the length of entropy gathered
ini_set('session.hash_function', 'sha512');        // We are going to switch this to sha512 for release, it brings far improved reduction for session collision
ini_set('url_rewriter.tags', '');                  // Ensure that the session id is *not* passed on the url - this is a possible security hole for logins - including admin.

if (file_exists('dev'))                            // Create/touch a file named dev in the main game directory to activate development mode
{
    ini_set('error_reporting', -1);                // During development, output all errors, even notices
    ini_set('display_errors', 1);                  // During development, display all errors
}
else
{
    ini_set('error_reporting', 0);                 // Do not report errors
    ini_set('display_errors', 0);                  // Do not display errors
}

date_default_timezone_set('UTC');                  // Set to your server's local time zone - PHP throws a notice if this is not set.
if (extension_loaded('mbstring'))                  // Ensure that we don't trigger an error if the mbstring extension is not loaded
{
    mb_http_output('UTF-8');                       // Specify that our output should be served in UTF-8, even if the PHP file served from isn't correctly saved in UTF-8.
    mb_internal_encoding('UTF-8');                 // On many systems, this defaults to ISO-8859-1. We are explicitly a UTF-8 code base, with Unicode language variables. So set it manually.
}
                                                   // Since header is now temlate driven, these weren't being passed along except on old
                                                   // crusty pages. Now everthing gets them!
header('Content-type: text/html; charset=utf-8');  // Set character set to utf-8, and using HTML as our content type
header('X-UA-Compatible: IE=Edge, chrome=1');      // Tell IE to use the latest version of the rendering engine, and to use chrome if it is available. This is not needed after IE11.
header('Cache-Control: public');                   // Tell the browser (and any caches) that this information can be stored in public caches.
header('Connection: Keep-Alive');                  // Tell the browser to keep going until it gets all data, please.
header('Vary: Accept-Encoding, Accept-Language');  // Tell CDN's or proxies to keep a separate version of the page in various encodings - compressed or not, in english or french for example.
header('Keep-Alive: timeout=15, max=100');         // Ask for persistent HTTP connections (15sec), which give better per-client performance, but can be worse (for a server) for many.
ob_start(array('Bnt\Compress', 'compress'));       // Start a buffer, and when it closes (at the end of a request), call the callback function "bnt\Compress" to properly handle detection of compression.

$pdo_db = new Bnt\Db;
$pdo_db = $pdo_db->initDb('pdo');                  // Connect to db using pdo
$db = new Bnt\Db;
$db = $db->initDb('adodb');                        // Connect to db using adodb also - for now - to be eliminated!

$bntreg = new Bnt\Reg($pdo_db);                    // Initalize the BNT Registry object, for passing the most common variables in game through classes
$bntreg->bnttimer = new Bnt\Timer;                 // We want benchmarking data for all activities, so create a benchmark timer object
$bntreg->bnttimer->start();                        // Start benchmarking immediately
$langvars = null;                                  // We need language variables in every page, set them to a null value first.
$template = new \Bnt\Template();                   // Template API.
$template->setTheme($bntreg->default_template);    // We set the name of the theme, temporary until we have a theme picker

if (!isset($index_page))
{
    $index_page = false;
    // Ensure that we do not start sessions on the index page (or pages likely to have no db),
    // until the player chooses to allow them or until the db exists.
    if (!isset($_SESSION))
    {
        session_start();
    }
}

if (isset($bntreg->default_lang))
{
    $lang = $bntreg->default_lang;
}

if (Bnt\Db::isActive($pdo_db))
{
    if (empty($_SESSION['username']))              // If the user has not logged in
    {
        if (array_key_exists('lang', $_GET))       // And the user has chosen a language on index.php
        {
            $lang = $_GET['lang'];                 // Set $lang to the language the user has chosen
        }
    }
    else // The user has logged in, so use his preference from the database
    {
        $sql = "SELECT lang FROM {$pdo_db->prefix}ships WHERE email =:email";
        $stmt = $pdo_db->prepare($sql);
        $stmt->bindParam(':email', $_SESSION['username']);
        $res = $stmt->execute();
        Bnt\Db::logDbErrors($pdo_db, $res, __LINE__, __FILE__);
        $res = $stmt->fetch();
        $playerinfo['lang'] = $res['lang'];
        $lang = $playerinfo['lang'];
    }
}

// Initialize the Plugin System.
Bnt\PluginSystem::initialize($pdo_db);

// Load all Plugins.
Bnt\PluginSystem::loadPlugins();

// Ok, here we raise EVENT_TICK which is called every page load, this saves us from having to add new lines to support new features.
// This is used for ingame stuff and Plug-ins that need to be called on every page load.
// May need to change array(time()) to have extra info, but the current suits us fine for now.
Bnt\PluginSystem::raiseEvent(EVENT_TICK, array (time()));
?>
