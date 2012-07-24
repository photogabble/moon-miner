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
// File: global_cleanups.php

if (preg_match("/global_cleanups.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

if (!ob_start("ob_gzhandler")) ob_start(); // If the server will support gzip compression, use it. Otherwise, start buffering.
//ob_start();

// Benchmarking - start before anything else.
$BenchmarkTimer = new c_Timer;
$BenchmarkTimer->start(); // Start benchmarking immediately

global $ADODB_CRYPT_KEY;
global $ADODB_SESSION_CONNECT, $ADODB_SESSION_USER, $ADODB_SESSION_DB;

$ADODB_SESS_CONN = '';
$ADODB_SESSION_TBL = $db_prefix . "sessions";

// We explicitly use encrypted sessions, but this adds compression as well.
ADODB_Session::encryptionKey($ADODB_CRYPT_KEY);

// The data field name "data" violates SQL reserved words - switch it to SESSDATA
ADODB_Session::dataFieldName('SESSDATA');

global $db;
connectdb ();
$db->prefix = $db_prefix;
$db->logging = $db_logging;

if ($db_logging)
{
    adodb_perf::table("{$db->prefix}adodb_logsql");
    $db->LogSQL(); // Turn on adodb performance logging
}

if (!isset($index_page))
{
    $index_page = false;
}

if (!$index_page)
{
    // Ensure that we do not set cookies on the index page, until the player chooses to allow them.
    session_start ();
}

// reg_global_fix,0.1.1,22-09-2004,BNT DevTeam
if (!defined('reg_global_fix'))define('reg_global_fix', True, TRUE);

foreach ($_POST as $k=>$v)
{
    if (!isset($GLOBALS[$k]))
    {
        ${$k}=$v;
    }
}
foreach ($_GET as $k=>$v)
{
    if (!isset($GLOBALS[$k]))
    {
        ${$k}=$v;
    }
}
foreach ($_COOKIE as $k=>$v)
{
    if (!isset($GLOBALS[$k]))
    {
        ${$k}=$v;
    }
}

if (!isset($userpass))
{
    $userpass = '';
}

if ($userpass != '' and $userpass != '+')
{
    $username = substr ($userpass, 0, strpos ($userpass, "+"));
    $password = substr ($userpass, strpos ($userpass, "+")+1);
}

// Ensure lang is set
$found = 0;

if (!$index_page)
{
    if (isset($_SESSION['lang']))
    {
        $lang = $_SESSION['lang'];
    }
}

if (!empty($lang))
{
    if (!preg_match("/^[\w]+$/", $lang))
    {
        $lang = $default_lang;
    }

    foreach ($avail_lang as $key => $value)
    {
        if ($lang == $value['file'])
        {
            $_SESSION['lang'] = $lang;
            $found = 1;
            break;
        }
    }

    if ($found == 0)
    {
        $lang = $default_lang;
    }
}

if (!isset($lang) || empty($lang))
{
    $lang = $default_lang;
}

$langsh = $lang;
// $lang = $lang . ".inc"; // eliminated these files

if (empty($link_forums))
{
    $link_forums = "http://forums.blacknova.net";
}
$l = new bnt_translation();
?>
