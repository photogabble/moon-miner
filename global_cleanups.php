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

if (preg_match("/global_funcs.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

// Benchmarking - start before anything else.
$BenchmarkTimer = new c_Timer;
$BenchmarkTimer->start(); // Start benchmarking immediately

global $ADODB_CRYPT_KEY;
global $db_type, $dbhost, $dbuname, $dbpass, $dbname;

$ADODB_SESSION_TBL     = "bnt_sessions";
$ADODB_SESSION_DRIVER=$db_type;
$ADODB_SESSION_CONNECT=$dbhost;
$ADODB_SESSION_USER =$dbuname;
$ADODB_SESSION_PWD =$dbpass;
$ADODB_SESSION_DB =$dbname;

// Adodb handles database abstraction. We also use clob sessions, so that pgsql can be
// supported in the future, and cryptsessions, so the session data itself is encrypted.
include_once ("$ADOdbpath" . "/adodb-perf.inc.php");
include_once ("$ADOdbpath" . "/session/adodb-session.php");

// We explicitly use encrypted sessions, but this adds compression as well.
ADODB_Session::encryptionKey($ADODB_CRYPT_KEY);

// The data field name "data" violates SQL reserved words - switch it to SESSDATA
ADODB_Session::dataFieldName('SESSDATA');

global $db,$dbtables;
connectdb();

session_start();

// reg_global_fix,0.1.1,22-09-2004,BNT DevTeam
if(!defined('reg_global_fix'))define('reg_global_fix', True, TRUE);

if (1==1)
{
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
}

if (!isset($userpass))
{
    $userpass = '';
}

if ($userpass != '' and $userpass != '+')
{
    $username = substr($userpass, 0, strpos($userpass, "+"));
    $password = substr($userpass, strpos($userpass, "+")+1);
}

// Ensure lang is set
$found = 0;
if(!empty($lang))
{
  if(!preg_match("/^[\w]+$/", $lang))
  {
     $lang = $default_lang;

  }
  foreach($avail_lang as $key => $value)
  {
    if($lang == $value['file'])
    {
      setcookie("lang",$lang,time()+(3600*24)*365,$gamepath,$gamedomain);
      $found = 1;
      break;
    }
  }

  if($found == 0)
    $lang = $default_lang;
}

if (!isset($lang) || empty($lang))
  $lang = $default_lang;
$lang = $lang . ".inc";
// Log constants

define('LOG_LOGIN', 1);
define('LOG_LOGOUT', 2);
define('LOG_ATTACK_OUTMAN', 3);           //sent to target when better engines
define('LOG_ATTACK_OUTSCAN', 4);          //sent to target when better cloak
define('LOG_ATTACK_EWD', 5);              //sent to target when EWD engaged
define('LOG_ATTACK_EWDFAIL', 6);          //sent to target when EWD failed
define('LOG_ATTACK_LOSE', 7);             //sent to target when he lost
define('LOG_ATTACKED_WIN', 8);            //sent to target when he won
define('LOG_TOLL_PAID', 9);               //sent when paid a toll
define('LOG_HIT_MINES', 10);              //sent when hit mines
define('LOG_SHIP_DESTROYED_MINES', 11);   //sent when destroyed by mines
define('LOG_PLANET_DEFEATED_D', 12);      //sent when one of your defeated planets is destroyed instead of captured
define('LOG_PLANET_DEFEATED', 13);        //sent when a planet is defeated
define('LOG_PLANET_NOT_DEFEATED', 14);    //sent when a planet survives
define('LOG_RAW', 15);                    //this log is sent as-is
define('LOG_TOLL_RECV', 16);              //sent when you receive toll money
define('LOG_DEFS_DESTROYED', 17);         //sent for destroyed sector defenses
define('LOG_PLANET_EJECT', 18);           //sent when ejected from a planet due to team switch
define('LOG_BADLOGIN', 19);               //sent when bad login
define('LOG_PLANET_SCAN', 20);            //sent when a planet has been scanned
define('LOG_PLANET_SCAN_FAIL', 21);       //sent when a planet scan failed
define('LOG_PLANET_CAPTURE', 22);         //sent when a planet is captured
define('LOG_SHIP_SCAN', 23);              //sent when a ship is scanned
define('LOG_SHIP_SCAN_FAIL', 24);         //sent when a ship scan fails
define('LOG_Xenobe_ATTACK', 25);        //xenobes send this to themselves
define('LOG_STARVATION', 26);             //sent when colonists are starving... Is this actually used in the game?
define('LOG_TOW', 27);                    //sent when a player is towed
define('LOG_DEFS_DESTROYED_F', 28);       //sent when a player destroys fighters
define('LOG_DEFS_KABOOM', 29);            //sent when sector fighters destroy you
define('LOG_HARAKIRI', 30);               //sent when self-destructed
define('LOG_TEAM_REJECT', 31);            //sent when player refuses invitation
define('LOG_TEAM_RENAME', 32);            //sent when renaming a team
define('LOG_TEAM_M_RENAME', 33);          //sent to members on team rename
define('LOG_TEAM_KICK', 34);              //sent to booted player
define('LOG_TEAM_CREATE', 35);            //sent when created a team
define('LOG_TEAM_LEAVE', 36);             //sent when leaving a team
define('LOG_TEAM_NEWLEAD', 37);           //sent when leaving a team, appointing a new leader
define('LOG_TEAM_LEAD', 38);              //sent to the new team leader
define('LOG_TEAM_JOIN', 39);              //sent when joining a team
define('LOG_TEAM_NEWMEMBER', 40);         //sent to leader on join
define('LOG_TEAM_INVITE', 41);            //sent to invited player
define('LOG_TEAM_NOT_LEAVE', 42);         //sent to leader on leave
define('LOG_ADMIN_HARAKIRI', 43);         //sent to admin on self-destruct
define('LOG_ADMIN_PLANETDEL', 44);        //sent to admin on planet destruction instead of capture
define('LOG_DEFENCE_DEGRADE', 45);        //sent sector fighters have no supporting planet
define('LOG_PLANET_CAPTURED', 46);            //sent to player when he captures a planet
define('LOG_BOUNTY_CLAIMED', 47);            //sent to player when they claim a bounty
define('LOG_BOUNTY_PAID', 48);            //sent to player when their bounty on someone is paid
define('LOG_BOUNTY_CANCELLED', 49);            //sent to player when their bounty is refunded
define('LOG_SPACE_PLAGUE', 50);            // sent when space plague attacks a planet
define('LOG_PLASMA_STORM', 51);           // sent when a plasma storm attacks a planet
define('LOG_BOUNTY_FEDBOUNTY', 52);       // Sent when the federation places a bounty on a player
define('LOG_PLANET_BOMBED', 53);     //Sent after bombing a planet
define('LOG_ADMIN_ILLEGVALUE', 54);        //sent to admin on planet destruction instead of capture
// Database tables variables
$dbtables['ibank_accounts'] = "{$db_prefix}ibank_accounts";
$dbtables['links'] = "{$db_prefix}links";
$dbtables['planets'] = "{$db_prefix}planets";
$dbtables['traderoutes'] = "{$db_prefix}traderoutes";
$dbtables['news'] = "{$db_prefix}news";
$dbtables['ships'] = "{$db_prefix}ships";
$dbtables['teams'] = "{$db_prefix}teams";
$dbtables['universe'] = "{$db_prefix}universe";
$dbtables['zones'] = "{$db_prefix}zones";
$dbtables['messages'] = "{$db_prefix}messages";
$dbtables['xenobe'] = "{$db_prefix}xenobe";
$dbtables['sector_defence'] = "{$db_prefix}sector_defence";
$dbtables['scheduler'] = "{$db_prefix}scheduler";
$dbtables['ip_bans'] = "{$db_prefix}ip_bans";
$dbtables['IGB_transfers'] = "{$db_prefix}IGB_transfers";
$dbtables['logs'] = "{$db_prefix}logs";
$dbtables['bounty'] = "{$db_prefix}bounty";
$dbtables['movement_log'] = "{$db_prefix}movement_log";
$dbtables['sessions'] = "{$db_prefix}sessions";
spl_autoload_register('bnt_autoload');
?>
