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

include "config.php";
updatecookie();
include "languages/$lang";
$title = $l_md_title;
include "header.php";

if (checklogin())
{
    die();
}

if (!isset($defence_id))
{
    echo "$l_md_invalid<br>";
    TEXT_GOTOMAIN();
    die();
}

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;
$res = $db->Execute("SELECT * from $dbtables[universe] WHERE sector_id=$playerinfo[sector]");
$sectorinfo = $res->fields;

if ($playerinfo[turns]<1)
{
    echo $l_md_noturn . "<br><br>";
    TEXT_GOTOMAIN();
    include "footer.php";
    die();
}

$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE defence_id=$defence_id ");
//Put the defence information into the array "defenceinfo"

if ($result3 == 0)
{
   echo "$l_md_nolonger<br>";
   TEXT_GOTOMAIN();
   die();
}
$defenceinfo = $result3->fields;
if ($defenceinfo['sector_id'] <> $playerinfo['sector'])
{
   echo "$l_md_nothere<br><br>";
   TEXT_GOTOMAIN();
   include "footer.php";
   die();
}
if ($defenceinfo['ship_id'] == $playerinfo['ship_id'])
{
   $defence_owner = $l_md_you;
}
else
{
   $defence_ship_id = $defenceinfo['ship_id'];
   $resulta = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE ship_id = $defence_ship_id ");
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

switch ($response) {
   case "fight":
      bigtitle();
      if ($defenceinfo['ship_id'] == $playerinfo['ship_id'])
      {
         echo "$l_md_yours<br><br>";
         TEXT_GOTOMAIN();
         include "footer.php";
         die();
      }
      $sector = $playerinfo[sector] ;
      if ($defenceinfo['defence_type'] == 'F')
      {
         $countres = $db->Execute("SELECT SUM(quantity) as totalfighters FROM $dbtables[sector_defence] where sector_id = $sector and defence_type = 'F'");
         $ttl = $countres->fields;
         $total_sector_fighters = $ttl['totalfighters'];
         include "sector_fighters.php";
      }
      else
      {
          // Attack mines goes here
         $countres = $db->Execute("SELECT SUM(quantity) as totalmines FROM $dbtables[sector_defence] where sector_id = $sector and defence_type = 'M'");
         $ttl = $countres->fields;
         $total_sector_mines = $ttl['totalmines'];
         $playerbeams = NUM_BEAMS($playerinfo[beams]);
         if ($playerbeams>$playerinfo[ship_energy])
         {
            $playerbeams=$playerinfo[ship_energy];
         }
         if ($playerbeams>$total_sector_mines)
         {
            $playerbeams=$total_sector_mines;
         }
         echo "$l_md_bmines $playerbeams $l_mines<br>";
         $update4b = $db->Execute ("UPDATE $dbtables[ships] SET ship_energy=ship_energy-$playerbeams WHERE ship_id=$playerinfo[ship_id]");
         explode_mines($sector,$playerbeams);
         $char_name = $playerinfo['character_name'];
         $l_md_msgdownerb=str_replace("[sector]",$sector,$l_md_msgdownerb);
         $l_md_msgdownerb=str_replace("[mines]",$playerbeams,$l_md_msgdownerb);
         $l_md_msgdownerb=str_replace("[name]",$char_name,$l_md_msgdownerb);
         message_defence_owner($sector,"$l_md_msgdownerb");
         TEXT_GOTOMAIN();
         die();
      }
      break;
   case "retrieve":
      if ($defenceinfo['ship_id'] <> $playerinfo['ship_id'])
      {
         echo "$l_md_notyours<br><br>";
         TEXT_GOTOMAIN();
         include "footer.php";
         die();
      }
      $quantity = stripnum($quantity);
      if ($quantity < 0) $quantity = 0;
      if ($quantity > $defenceinfo['quantity'])
      {
         $quantity = $defenceinfo['quantity'];
      }
      $torpedo_max = NUM_TORPEDOES($playerinfo[torp_launchers]) - $playerinfo[torps];
      $fighter_max = NUM_FIGHTERS($playerinfo[computer]) - $playerinfo[ship_fighters];
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
      $ship_id = $playerinfo[ship_id];
      if ($quantity > 0)
      {
         $db->Execute("UPDATE $dbtables[sector_defence] SET quantity=quantity - $quantity WHERE defence_id = $defence_id");
         if ($defenceinfo['defence_type'] == 'M')
         {
            $db->Execute("UPDATE $dbtables[ships] set torps=torps + $quantity where ship_id = $ship_id");
         }
         else
         {
            $db->Execute("UPDATE $dbtables[ships] set ship_fighters=ship_fighters + $quantity where ship_id = $ship_id");
         }
         $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE quantity <= 0");
      }
      $stamp = date("Y-m-d H-i-s");

      $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
      bigtitle();
      echo "$l_md_retr $quantity $defence_type.<br>";
      TEXT_GOTOMAIN();
      die();
      break;
   case "change":
      bigtitle();
      if ($defenceinfo['ship_id'] <> $playerinfo['ship_id'])
      {
         echo "$l_md_notyours<br><br>";
         TEXT_GOTOMAIN();
         include "footer.php";
         die();
      }
      $db->Execute("UPDATE $dbtables[sector_defence] SET fm_setting = '$mode' where defence_id = $defence_id");
      $stamp = date("Y-m-d H-i-s");
      $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
      if ($mode == 'attack')
        $mode = $l_md_attack;
      else
        $mode = $l_md_toll;
      $l_md_mode=str_replace("[mode]",$mode,$l_md_mode);
      echo "$l_md_mode<br>";
      TEXT_GOTOMAIN();
      die();
      break;
   default:
      bigtitle();
      $l_md_consist=str_replace("[qty]",$qty,$l_md_consist);
      $l_md_consist=str_replace("[type]",$defence_type,$l_md_consist);
      $l_md_consist=str_replace("[owner]",$defence_owner,$l_md_consist);
      echo "$l_md_consist<br>";

      if ($defenceinfo['ship_id'] == $playerinfo['ship_id'])
      {
         echo "$l_md_youcan:<br>";
         echo "<FORM ACTION=modify_defences.php METHOD=POST>";
         echo "$l_md_retrieve <INPUT TYPE=TEST NAME=quantity SIZE=10 MAXLENGTH=10 VALUE=0></INPUT> $defence_type<br>";
         echo "<input type=hidden name=response value=retrieve>";
         echo "<input type=hidden name=defence_id value=$defence_id>";
         echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><br><br>";
         echo "</FORM>";
         if ($defenceinfo['defence_type'] == 'F')
         {
            echo "$l_md_change:<br>";
            echo "<FORM ACTION=modify_defences.php METHOD=POST>";
            echo "$l_md_cmode <INPUT TYPE=RADIO NAME=mode $set_attack VALUE=attack>$l_md_attack</INPUT>";
            echo "<INPUT TYPE=RADIO NAME=mode $set_toll VALUE=toll>$l_md_toll</INPUT><br>";
            echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><br><br>";
            echo "<input type=hidden name=response value=change>";
            echo "<input type=hidden name=defence_id value=$defence_id>";
            echo "</FORM>";
         }
      }
      else
      {
         $ship_id = $defenceinfo['ship_id'];
         $result2 = $db->Execute("SELECT * from $dbtables[ships] where ship_id=$ship_id");
         $fighters_owner = $result2->fields;

         if ($fighters_owner[team] != $playerinfo[team] || $playerinfo[team] == 0)
         {
            echo "$l_youcan:<br>";
            echo "<FORM ACTION=modify_defences.php METHOD=POST>";
            echo "$l_md_attdef<br><INPUT TYPE=SUBMIT VALUE=$l_md_attack></INPUT><br>";
            echo "<input type=hidden name=response value=fight>";
            echo "<input type=hidden name=defence_id value=$defence_id>";
            echo "</FORM>";
         }
      }
      TEXT_GOTOMAIN();
      die();
      break;
}

TEXT_GOTOMAIN();
include "footer.php";
?>
