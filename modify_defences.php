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
// File: modify_defences.php

include './global_includes.php';
include_once './includes/explode_mines.php';

if (check_login ($db, $lang, $langvars)) // Checks player login, sets playerinfo
{
    die ();
}

// New database driven language entries
load_languages ($db, $lang, array ('modify_defences', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars);

$title = $l_md_title;
include './header.php';

if (!isset ($defence_id))
{
    echo $l_md_invalid . "<br><br>";
    TEXT_GOTOMAIN ();
    include './footer.php';
    die ();
}

$response = null;
if (array_key_exists ('response', $_REQUEST) == true)
{
    $response = $_REQUEST['response'];
}

$res = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
db_op_result ($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;

$res = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id = ?;", array ($playerinfo['sector']));
db_op_result ($db, $res, __LINE__, __FILE__);
$sectorinfo = $res->fields;

if ($playerinfo['turns'] < 1)
{
    echo $l_md_noturn . "<br><br>";
    TEXT_GOTOMAIN ();
    include './footer.php';
    die ();
}

$result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE defence_id = ?;", array ($defence_id));
db_op_result ($db, $result3, __LINE__, __FILE__);
// Put the defence information into the array "defenceinfo"

if (!$result3 instanceof ADORecordSet) // Not too sure, may need more checks on this.
{
   echo $l_md_nolonger . "<br>";
   TEXT_GOTOMAIN ();
   die ();
}

$defenceinfo = $result3->fields;
if ($defenceinfo['sector_id'] != $playerinfo['sector'])
{
   echo $l_md_nothere . "<br><br>";
   TEXT_GOTOMAIN ();
   include './footer.php';
   die ();
}

if ($defenceinfo['ship_id'] == $playerinfo['ship_id'])
{
    $defence_owner = $l_md_you;
}
else
{
    $defence_ship_id = $defenceinfo['ship_id'];
    $resulta = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id = ?;", array ($defence_ship_id));
    $ownerinfo = $resulta->fields;
    $defence_owner = $ownerinfo['character_name'];
}

$defence_type = $defenceinfo['defence_type'] == 'F' ? $l_fighters : $l_mines;
$qty = $defenceinfo['quantity'];
if ($defenceinfo['fm_setting'] == 'attack')
{
    $set_attack = 'CHECKED';
    $set_toll = '';
}
else
{
    $set_attack = '';
    $set_toll = 'CHECKED';
}

switch ($response)
{
   case "fight":
      echo "<h1>" . $title . "</h1>\n";
      if ($defenceinfo['ship_id'] == $playerinfo['ship_id'])
      {
         echo "$l_md_yours<br><br>";
         TEXT_GOTOMAIN ();
         include './footer.php';
         die ();
      }
      $sector = $playerinfo['sector'] ;
      if ($defenceinfo['defence_type'] == 'F')
      {
         $countres = $db->Execute ("SELECT SUM(quantity) AS totalfighters FROM {$db->prefix}sector_defence WHERE sector_id = ? AND defence_type = 'F';", array ($sector));
         $ttl = $countres->fields;
         $total_sector_fighters = $ttl['totalfighters'];
         $calledfrom = "modify_defences.php";
         include_once './sector_fighters.php';
      }
      else
      {
          // Attack mines goes here
         $countres = $db->Execute ("SELECT SUM(quantity) AS totalmines FROM {$db->prefix}sector_defence WHERE sector_id = ? AND defence_type = 'M';", array ($sector));
         $ttl = $countres->fields;
         $total_sector_mines = $ttl['totalmines'];
         $playerbeams = NUM_BEAMS ($playerinfo['beams']);
         if ($playerbeams > $playerinfo['ship_energy'])
         {
             $playerbeams = $playerinfo['ship_energy'];
         }
         if ($playerbeams > $total_sector_mines)
         {
             $playerbeams = $total_sector_mines;
         }
         echo "$l_md_bmines $playerbeams $l_mines<br>";
         $update4b = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy = ship_energy - ? WHERE ship_id = ?;", array ($playerbeams, $playerinfo['ship_id']));
         explode_mines ($db, $sector, $playerbeams);
         $char_name = $playerinfo['character_name'];
         $l_md_msgdownerb=str_replace ("[sector]", $sector, $l_md_msgdownerb);
         $l_md_msgdownerb=str_replace ("[mines]", $playerbeams, $l_md_msgdownerb);
         $l_md_msgdownerb=str_replace ("[name]", $char_name, $l_md_msgdownerb);
         \bnt\sector_defense::message_defense_owner ($db, $sector,"$l_md_msgdownerb");
         TEXT_GOTOMAIN ();
         die ();
      }
      break;
   case "retrieve":
      if ($defenceinfo['ship_id'] != $playerinfo['ship_id'])
      {
         echo "$l_md_notyours<br><br>";
         TEXT_GOTOMAIN ();
         include './footer.php';
         die();
      }
      $quantity = preg_replace('/[^0-9]/', '', $quantity);
      if ($quantity < 0) $quantity = 0;
      if ($quantity > $defenceinfo['quantity'])
      {
         $quantity = $defenceinfo['quantity'];
      }
      $torpedo_max = NUM_TORPEDOES ($playerinfo['torp_launchers']) - $playerinfo['torps'];
      $fighter_max = NUM_FIGHTERS ($playerinfo['computer']) - $playerinfo['ship_fighters'];
      if ($defenceinfo['defence_type'] == 'F')
      {
         if ($quantity > $fighter_max)
         {
            $quantity = $fighter_max;
         }
      }
      if ($defenceinfo['defence_type'] == 'M')
      {
         if ($quantity > $torpedo_max)
         {
            $quantity = $torpedo_max;
         }
      }
      if ($quantity > 0)
      {
         $db->Execute ("UPDATE {$db->prefix}sector_defence SET quantity=quantity - ? WHERE defence_id = ?", array ($quantity, $defence_id));
         if ($defenceinfo['defence_type'] == 'M')
         {
            $db->Execute ("UPDATE {$db->prefix}ships SET torps=torps + ? WHERE ship_id = ?", array ($quantity, $playerinfo['ship_id']));
         }
         else
         {
            $db->Execute ("UPDATE {$db->prefix}ships SET ship_fighters = ship_fighters + ? WHERE ship_id = ?", array ($quantity, $playerinfo['ship_id']));
         }
         $db->Execute ("DELETE FROM {$db->prefix}sector_defence WHERE quantity <= 0");
      }
      $stamp = date ("Y-m-d H-i-s");

      $db->Execute ("UPDATE {$db->prefix}ships SET last_login = ?,turns = turns - 1, turns_used = turns_used + 1, sector = ? WHERE ship_id = ?;", array ($stamp, $playerinfo['sector'], $playerinfo['ship_id']));
      echo "<h1>" . $title . "</h1>\n";
      echo "$l_md_retr $quantity $defence_type.<br>";
      TEXT_GOTOMAIN ();
      die ();
      break;
   case "change":
      echo "<h1>" . $title . "</h1>\n";
      if ($defenceinfo['ship_id'] != $playerinfo['ship_id'])
      {
         echo "$l_md_notyours<br><br>";
         TEXT_GOTOMAIN ();
         include './footer.php';
         die ();
      }
      $db->Execute ("UPDATE {$db->prefix}sector_defence SET fm_setting = ? WHERE defence_id = ?", array ($mode, $defence_id));
      $stamp = date ("Y-m-d H-i-s");
      $db->Execute ("UPDATE {$db->prefix}ships SET last_login = ?, turns = turns - 1, turns_used = turns_used + 1, sector = ? WHERE ship_id = ?;", array ($stamp, $playerinfo['sector'], $playerinfo['ship_id']));
      if ($mode == 'attack')
      {
        $mode = $l_md_attack;
      }
      else
      {
        $mode = $l_md_toll;
      }

      $l_md_mode=str_replace ("[mode]",$mode,$l_md_mode);
      echo "$l_md_mode<br>";
      TEXT_GOTOMAIN ();
      die ();
      break;
   default:
      echo "<h1>" . $title . "</h1>\n";
      $l_md_consist = str_replace ("[qty]", $qty, $l_md_consist);
      $l_md_consist = str_replace ("[type]", $defence_type, $l_md_consist);
      $l_md_consist = str_replace ("[owner]", $defence_owner, $l_md_consist);
      echo "$l_md_consist<br>";

      if ($defenceinfo['ship_id'] == $playerinfo['ship_id'])
      {
         echo "$l_md_youcan:<br>";
         echo "<form action=modify_defences.php method=post>";
         echo "$l_md_retrieve <input type=test name=quantity size=10 maxlength=10 value=0></input> $defence_type<br>";
         echo "<input type=hidden name=response value=retrieve>";
         echo "<input type=hidden name=defence_id value=$defence_id>";
         echo "<input type=submit value=$l_submit><br><br>";
         echo "</form>";
         if ($defenceinfo['defence_type'] == 'F')
         {
            echo "$l_md_change:<br>";
            echo "<form action=modify_defences.php method=post>";
            echo "$l_md_cmode <input type=radio name=mode $set_attack value=attack>$l_md_attack</input>";
            echo "<input type=radio name=mode $set_toll value=toll>$l_md_toll</input><br>";
            echo "<input type=submit value=$l_submit><br><br>";
            echo "<input type=hidden name=response value=change>";
            echo "<input type=hidden name=defence_id value=$defence_id>";
            echo "</form>";
         }
      }
      else
      {
         $result2 = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id = ?;", array ($defenceinfo['ship_id']));
         $fighters_owner = $result2->fields;

         if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
         {
            echo "$l_md_youcan:<br>";
            echo "<form action=modify_defences.php method=post>";
            echo "$l_md_attdef<br><input type=submit value=$l_md_attack></input><br>";
            echo "<input type=hidden name=response value=fight>";
            echo "<input type=hidden name=defence_id value=$defence_id>";
            echo "</form>";
         }
      }
      TEXT_GOTOMAIN ();
      die ();
      break;
}

TEXT_GOTOMAIN ();
include './footer.php';
?>
