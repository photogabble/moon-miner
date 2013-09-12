<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
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
//
// This file must not contain any include/require type actions (other than error) - those must occur in global_includes instead.

if (strpos ($_SERVER['PHP_SELF'], 'common.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

ini_set ("include_path", ".");                    // This seems to be a problem on a few platforms, so we manually set it to avoid those problems.
ini_set ('session.use_only_cookies', 1);          // Ensure that sessions will only be stored in a cookie
ini_set ('session.cookie_httponly', 1);           // Make the session cookie HTTP only, a flag that helps ensure that javascript cannot tamper with the session cookie
ini_set ('session.entropy_file', '/dev/urandom'); // Use urandom as entropy source, to help the random number generator
ini_set ('session.entropy_length', '32');         // Increase the length of entropy gathered
ini_set ('session.hash_function', 'sha1');        // We are going to switch this to sha512 for release, it brings far improved reduction for session collision
ini_set ('url_rewriter.tags', '');                // Ensure that the session id is *not* passed on the url - this is a possible security hole for logins - including admin.
date_default_timezone_set ('UTC');                // Set to your server's local time zone - PHP throws a notice if this is not set.
if (extension_loaded ('mbstring'))                // Ensure that we don't trigger an error if the mbstring extension is not loaded
{
    mb_http_output ("UTF-8");                     // Specify that our output should be served in UTF-8, even if the PHP file served from isn't correctly saved in UTF-8.
    mb_internal_encoding ("UTF-8");               // On many systems, this defaults to ISO-8859-1. We are explicitly a UTF-8 code base, with Unicode language variables. So set it manually.
}

$bntreg = new BntRegistry ();
$BenchmarkTimer = new Timer;
$BenchmarkTimer->start(); // Start benchmarking immediately
$ADODB_SESS_CONN = null;
$ADODB_SESSION_TBL = $db_prefix . "sessions";
ob_start (array('BntCompress', 'compress')); // Start a buffer, and when it closes (at the end of a request), call the callback function "bntCompress" (in includes/) to properly handle detection of compression.

// The data field name "data" violates SQL reserved words - switch it to SESSDATA
ADODB_Session::dataFieldName ('SESSDATA');

// Add MD5 encryption for sessions, and then compress it before storing it in the database
//ADODB_Session::filter (new ADODB_Encrypt_Mcrypt ());
//ADODB_Session::filter (new ADODB_Compress_Gzip ());

// If there is a $dbport variable set, use it in the connection method
if (!empty ($dbport))
{
    $ADODB_SESSION_CONNECT.= ":$dbport";
}

// Attempt to connect to the database
try
{
    $db = NewADOConnection($ADODB_SESSION_DRIVER);
    $db_init_result = @$db->Connect ("$ADODB_SESSION_CONNECT", "$ADODB_SESSION_USER", "$ADODB_SESSION_PWD", "$ADODB_SESSION_DB");
    if ($db_init_result === false)
    {
        throw new Exception;
    }
    else
    {
        // We have connected successfully. Now set our character set to utf-8
        $db->Execute ("SET NAMES 'utf8'");

        // Set the fetch mode for database calls to be associative by default
        $db->SetFetchMode (ADODB_FETCH_ASSOC);
    }
}
catch (exception $e)
{
    // We need to display the error message onto the screen.
    $err_msg = "Unable to connect to the " . $ADODB_SESSION_DRIVER . " Database.<br>\n Database Error: ". $db->ErrorNo () .": ". $db->ErrorMsg () ."<br>\n";
    die ($err_msg);
}

$db->prefix = $db_prefix;

$no_langs_yet = false;
// Get the config_values from the DB
$debug_query = $db->Execute ("SELECT name,value FROM {$db->prefix}gameconfig");

if (($debug_query instanceof ADORecordSet) && ($debug_query != false)) // Before DB is installed, debug_query will give false.
{
    DbOp::dbResult ($db, $debug_query, __LINE__, __FILE__);
    $db->inactive = false; // The database is active!

    if ($debug_query->EOF)
    {
        $no_langs_yet = true;
    }

    while (!$debug_query->EOF)
    {
        $row = $debug_query->fields;
        if ($row !== null)
        {
            $bntreg->set ($row['name'], $row['value']);
            $$row['name'] = $row['value'];
            $debug_query->MoveNext();
        }
    }
}
else
{
    $no_langs_yet = true;
}

if ($no_langs_yet)
{
    $db->inactive = true; // The database does not exist yet, or is inactive, so set a property warning us not to do DB activities.

    // Slurp in config variables from the ini file directly
    $ini_file = 'config/configset_classic.ini.php'; // This is hard-coded for now, but when we get multiple game support, we may need to change this.
    $ini_keys = parse_ini_file ($ini_file, true);
    foreach ($ini_keys as $config_category=>$config_line)
    {
        foreach ($config_line as $config_key=>$config_value)
        {
            $$config_key = $config_value;
            $bntreg->set ($config_key, $config_value);
        }
    }
}

// Create/touch a file named dev in the main game directory to activate development mode
if (file_exists ("dev"))
{
    ini_set ('error_reporting', -1); // During development, output all errors, even notices
    ini_set ('display_errors', '1'); // During development, *display* all errors
    adodb_perf::table ("{$db->prefix}adodb_logsql");
    if (!$db->inactive)
    {
        $db->LogSQL (); // Turn on adodb performance logging
    }
}
else
{
    ini_set ('error_reporting', 0);  // No errors
    ini_set ('display_errors', '0'); // Don't show them
}

if (!isset ($index_page))
{
    $index_page = false;
}

if (!$index_page)
{
    // Ensure that we do not set cookies on the index page, until the player chooses to allow them.
    if (!isset ($_SESSION))
    {
        session_start ();
    }
}


// reg_global_fix,0.1.1,22-09-2004,BNT DevTeam
if (!defined ('reg_global_fix')) define ('reg_global_fix', True, TRUE);

// Add logging in these two functions to identify where we are using post and get, and start migrating away from them both needing to be globals.

foreach ($_POST as $k=>$v)
{
    if (!isset ($GLOBALS[$k]))
    {
        ${$k} = $v;
    }
}

foreach ($_GET as $k=>$v)
{
    if (!isset ($GLOBALS[$k]))
    {
        ${$k} = $v;
    }
}

if (isset ($default_lang))
{
    $lang = $default_lang;
}

if ($db->inactive != true) // Before DB is installed, don't try to setup userinfo
{
    if (empty ($_SESSION['username']))  // If the user has not logged in
    {
        if (array_key_exists ('lang', $_GET)) // And the user has chosen a language on index.php
        {
            $lang = $_GET['lang'];  // Set $lang to the language the user has chosen
        }
    }
    else // The user has logged in, so use his preference from the database
    {
        $res = $db->Execute ("SELECT lang FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
        if ($res)
        {
            $playerinfo['lang'] = $res->fields['lang'];
            $lang = $playerinfo['lang'];
        }
    }
}

// Initialize the Plugin System.
PluginSystem::Initialize ($db);

// Load all Plugins.
PluginSystem::LoadPlugins ();

// Ok, here we raise EVENT_TICK which is called every page load, this saves us from having to add new lines to support new features.
// This is used for ingame stuff and Plug-ins that need to be called on every page load.
// May need to change array(time()) to have extra info, but the current suits us fine for now.
PluginSystem::RaiseEvent (EVENT_TICK, array (time ()));

// We need language variables in every page, and a language setting for them.
global $lang, $langvars;

$admin_list = array ();
$ip = $_SERVER['REMOTE_ADDR'];
$gamepath = SetPaths::setGamepath ();
$gamedomain = SetPaths::setGamedomain ();
$template = new Template (); // Template API.
$bntreg->set ("bnttimer", $BenchmarkTimer);
$bntreg->set ("db", $db);
$bntreg->set ("langvars", $langvars);
$template->SetTheme ("classic"); // We set the name of the theme.
?>
