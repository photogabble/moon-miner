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
// File: includes/xenobehunter.php

function xenobehunter()
{
  //
  // SETUP GENERAL VARIABLES
  //
  global $playerinfo;
  global $targetlink;
  global $xenobeisdead;
  global $db, $db_logging;

  $rescount = $db->Execute("SELECT COUNT(*) AS num_players FROM {$db->prefix}ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' and ship_id > 1");
  db_op_result ($db, $rescount, __LINE__, __FILE__, $db_logging);
  $rowcount = $rescount->fields;
  $topnum = min(10,$rowcount[num_players]);

  // IF WE HAVE KILLED ALL THE PLAYERS IN THE GAME THEN THERE IS LITTLE POINT IN PROCEEDING
  if ($topnum<1) return;

  $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' and ship_id > 1 ORDER BY score DESC LIMIT $topnum");
  db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);

  // LETS CHOOSE A TARGET FROM THE TOP PLAYER LIST
  $i=1;
  $targetnum=mt_rand(1,$topnum);
  while (!$res->EOF)
  {
    if ($i==$targetnum)
    {
    $targetinfo=$res->fields;
    }
    $i++;
    $res->MoveNext();
  }

  // Make sure we have a target
  if (!$targetinfo)
  {
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Hunt Failed: No Target ");

    return;
  }

  //
  // WORM HOLE TO TARGET SECTOR
  //
  $sectres = $db->Execute ("SELECT sector_id,zone_id FROM {$db->prefix}universe WHERE sector_id='$targetinfo[sector]'");
  db_op_result ($db, $sectres, __LINE__, __FILE__, $db_logging);
  $sectrow = $sectres->fields;
  $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM {$db->prefix}zones WHERE zone_id=$sectrow[zone_id]");
  db_op_result ($db, $zoneres, __LINE__, __FILE__, $db_logging);
  $zonerow = $zoneres->fields;
  // ONLY WORM HOLM TO TARGET IF WE CAN ATTACK IN TARGET SECTOR
  if ($zonerow[allow_attack]=="Y")
  {
    $stamp = date("Y-m-d H-i-s");
    $query="UPDATE {$db->prefix}ships SET last_login='$stamp', turns_used=turns_used+1, sector=$targetinfo[sector] where ship_id=$playerinfo[ship_id]";
    $move_result = $db->Execute ("$query");
    db_op_result ($db, $move_result, __LINE__, __FILE__, $db_logging);
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe used a wormhole to warp to sector $targetinfo[sector] where he is hunting player $targetinfo[character_name].");
    if (!$move_result)
    {
      $error = $db->ErrorMsg();
      playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Move failed with error: $error ");

      return;
    }
  //
  // CHECK FOR SECTOR DEFENCE
  //
    $resultf = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=$targetinfo[sector] and defence_type ='F' ORDER BY quantity DESC");
    db_op_result ($db, $resultf, __LINE__, __FILE__, $db_logging);
    $i = 0;
    $total_sector_fighters = 0;
    if ($resultf > 0)
    {
      while (!$resultf->EOF)
      {
        $defences[$i] = $resultf->fields;
        $total_sector_fighters += $defences[$i]['quantity'];
        $i++;
        $resultf->MoveNext();
      }
    }
    $resultm = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=$targetinfo[sector] and defence_type ='M'");
    db_op_result ($db, $resultm, __LINE__, __FILE__, $db_logging);
    $i = 0;
    $total_sector_mines = 0;
    if ($resultm > 0)
    {
      while (!$resultm->EOF)
      {
        $defences[$i] = $resultm->fields;
        $total_sector_mines += $defences[$i]['quantity'];
        $i++;
        $resultm->MoveNext();
      }
    }

    if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
    // DEST LINK HAS DEFENCES
    {
      // ATTACK SECTOR DEFENCES
      $targetlink = $targetinfo[sector];
      xenobetosecdef();
    }
    if ($xenobeisdead>0) {
      // SECTOR DEFENSES KILLED US
      return;
    }

    // TIME TO ATTACK THE TARGET
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe launching an attack on $targetinfo[character_name].");

    // SEE IF TARGET IS ON A PLANET
    if ($targetinfo[planet_id]>0) {
      // ON A PLANET
      xenobetoplanet($targetinfo[planet_id]);
    } else {
      // NOT ON A PLANET
      xenobetoship($targetinfo[ship_id]);
    }
  } else
  {
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe hunt failed, target $targetinfo[character_name] was in a no attack zone (sector $targetinfo[sector]).");
  }
}
?>
