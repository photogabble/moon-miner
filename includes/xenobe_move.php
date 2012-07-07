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
// File: includes/xenobemove.php

function xenobemove()
{
  //
  // SETUP GENERAL VARIABLES
  //
  global $playerinfo;
  global $sector_max;
  global $targetlink;
  global $xenobeisdead;
  global $db, $db_logging;

  //
  // OBTAIN A TARGET LINK
  //
  if ($targetlink==$playerinfo[sector]) $targetlink=0;
  $linkres = $db->Execute ("SELECT * FROM {$db->prefix}links WHERE link_start='$playerinfo[sector]'");
  db_op_result ($db, $linkres, __LINE__, __FILE__, $db_logging);
  if ($linkres>0)
  {
    while (!$linkres->EOF)
    {
      $row = $linkres->fields;
      // OBTAIN SECTOR INFORMATION
      $sectres = $db->Execute ("SELECT sector_id,zone_id FROM {$db->prefix}universe WHERE sector_id='$row[link_dest]'");
      db_op_result ($db, $sectres, __LINE__, __FILE__, $db_logging);
      $sectrow = $sectres->fields;
      $zoneres = $db->Execute("SELECT zone_id,allow_attack FROM {$db->prefix}zones WHERE zone_id=$sectrow[zone_id]");
      db_op_result ($db, $zoneres, __LINE__, __FILE__, $db_logging);
      $zonerow = $zoneres->fields;
      if ($zonerow[allow_attack]=="Y")                        // DEST LINK MUST ALLOW ATTACKING
      {
        $setlink=mt_rand(0,2);                        // 33% CHANCE OF REPLACING DEST LINK WITH THIS ONE
        if ($setlink==0 || !$targetlink>0)          // UNLESS THERE IS NO DEST LINK, CHHOSE THIS ONE
        {
          $targetlink=$row[link_dest];
        }
      }
      $linkres->MoveNext();
    }
  }

  // IF NO ACCEPTABLE LINK, TIME TO USE A WORM HOLE
  if (!$targetlink>0)
  {
    // GENERATE A RANDOM SECTOR NUMBER
    $wormto=mt_rand(1,($sector_max-15));
    $limitloop=1;                        // LIMIT THE NUMBER OF LOOPS
    while (!$targetlink>0 && $limitloop<15)
    {
      // OBTAIN SECTOR INFORMATION
      $sectres = $db->Execute ("SELECT sector_id,zone_id FROM {$db->prefix}universe WHERE sector_id='$wormto'");
      db_op_result ($db, $sectres, __LINE__, __FILE__, $db_logging);
      $sectrow = $sectres->fields;
      $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM {$db->prefix}zones WHERE zone_id=$sectrow[zone_id]");
      db_op_result ($db, $zoneres, __LINE__, __FILE__, $db_logging);
      $zonerow = $zoneres->fields;
      if ($zonerow[allow_attack]=="Y")
      {
        $targetlink=$wormto;
        playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Used a wormhole to warp to a zone where attacks are allowed.");
      }
      $wormto++;
      $wormto++;
      $limitloop++;
    }
  }

  //
  // CHECK FOR SECTOR DEFENCE
  //
  if ($targetlink>0)
  {
    $resultf = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
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
    $resultm = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id='$targetlink' and defence_type ='M'");
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
      if ($playerinfo[aggression] == 2 || $playerinfo[aggression] == 1) {
        // ATTACK SECTOR DEFENCES
        xenobetosecdef();

        return;
      } else {
        playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Move failed, the sector is defended by $total_sector_fighters fighters and $total_sector_mines mines.");

        return;
      }
    }
  }


  // DO MOVE TO TARGET LINK
  if ($targetlink>0)
  {
    $stamp = date("Y-m-d H-i-s");
    $query="UPDATE {$db->prefix}ships SET last_login='$stamp', turns_used=turns_used+1, sector=$targetlink where ship_id=$playerinfo[ship_id]";
    $move_result = $db->Execute ("$query");
    db_op_result ($db, $move_result, __LINE__, __FILE__, $db_logging);
    if (!$move_result)
    {
      $error = $db->ErrorMsg();
      playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Move failed with error: $error ");
    } else
    {
      // playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Moved to $targetlink without incident.");
    }
  } else
  {                                            // WE HAVE NO TARGET LINK FOR SOME REASON
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Move failed due to lack of target link.");
    $targetlink = $playerinfo[sector];         // RESET TARGET LINK SO IT IS NOT ZERO
  }
}
?>
