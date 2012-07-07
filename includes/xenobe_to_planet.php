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
// File: includes/xenobetoplanet.php

function xenobetoplanet($planet_id)
{
  //
  // Xenobe Planet Attack Code
  //

  //
  // SETUP GENERAL VARIABLES
  //
  global $playerinfo;
  global $planetinfo;

  global $torp_dmg_rate;
  global $level_factor;
  global $rating_combat_factor;
  global $upgrade_cost;
  global $upgrade_factor;
  global $sector_max;
  global $xenobeisdead;
  global $db, $db_logging;

  // LOCKING TABLES
  $resh = $db->Execute("LOCK TABLES {$db->prefix}ships WRITE, {$db->prefix}universe WRITE, {$db->prefix}planets WRITE, {$db->prefix}news WRITE, {$db->prefix}logs WRITE");
  db_op_result ($db, $resh, __LINE__, __FILE__, $db_logging);

  //
  // LOOKUP PLANET DETAILS
  //
  $resultp = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE planet_id='$planet_id'");
  db_op_result ($db, $resultp, __LINE__, __FILE__, $db_logging);
  $planetinfo=$resultp->fields;

  //
  // LOOKUP OWNER DETAILS
  //
  $resulto = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id='$planetinfo[owner]'");
  db_op_result ($db, $resulto, __LINE__, __FILE__, $db_logging);
  $ownerinfo=$resulto->fields;

  //
  // SETUP PLANETARY VARIABLES
  //
  $base_factor = ($planetinfo[base] == 'Y') ? $basedefense : 0;

  // PLANET BEAMS
  $targetbeams = NUM_BEAMS($ownerinfo[beams] + $base_factor);
  if ($targetbeams > $planetinfo[energy]) $targetbeams = $planetinfo[energy];
  $planetinfo[energy] -= $targetbeams;

  // PLANET SHIELDS
  $targetshields = NUM_SHIELDS($ownerinfo[shields] + $base_factor);
  if ($targetshields > $planetinfo[energy]) $targetshields = $planetinfo[energy];
  $planetinfo[energy] -= $targetshields;

  // PLANET TORPS
  $torp_launchers = round(pow ($level_factor, ($ownerinfo[torp_launchers])+ $base_factor)) * 10;
  $torps = $planetinfo[torps];
  $targettorps = $torp_launchers;
  if ($torp_launchers > $torps) $targettorps = $torps;
  $planetinfo[torps] -= $targettorps;
  $targettorpdmg = $torp_dmg_rate * $targettorps;

  // PLANET FIGHTERS
  $targetfighters = $planetinfo[fighters];

  //
  // SETUP ATTACKER VARIABLES
  //

  // ATTACKER BEAMS
  $attackerbeams = NUM_BEAMS($playerinfo[beams]);
  if ($attackerbeams > $playerinfo[ship_energy]) $attackerbeams = $playerinfo[ship_energy];
  $playerinfo[ship_energy] -= $attackerbeams;

  // ATTACKER SHIELDS
  $attackershields = NUM_SHIELDS($playerinfo[shields]);
  if ($attackershields > $playerinfo[ship_energy]) $attackershields = $playerinfo[ship_energy];
  $playerinfo[ship_energy] -= $attackershields;

  // ATTACKER TORPS
  $attackertorps = round(pow ($level_factor, $playerinfo[torp_launchers])) * 2;
  if ($attackertorps > $playerinfo[torps]) $attackertorps = $playerinfo[torps];
  $playerinfo[torps] -= $attackertorps;
  $attackertorpdamage = $torp_dmg_rate * $attackertorps;

  // ATTACKER FIGHTERS
  $attackerfighters = $playerinfo[ship_fighters];

  // ATTACKER ARMOR
  $attackerarmor = $playerinfo[armor_pts];

  //
  // BEGIN COMBAT PROCEDURES
  //
  if ($attackerbeams > 0 && $targetfighters > 0)
  {                         // ATTACKER HAS BEAMS - TARGET HAS FIGHTERS - BEAMS VS FIGHTERS
    if ($attackerbeams > $targetfighters)
    {                                  // ATTACKER BEAMS GT TARGET FIGHTERS
      $lost = $targetfighters;
      $targetfighters = 0;                                     // T LOOSES ALL FIGHTERS
      $attackerbeams = $attackerbeams-$lost;                   // A LOOSES BEAMS EQ TO T FIGHTERS
    } else
    {                                  // ATTACKER BEAMS LE TARGET FIGHTERS
      $targetfighters = $targetfighters-$attackerbeams;        // T LOOSES FIGHTERS EQ TO A BEAMS
      $attackerbeams = 0;                                      // A LOOSES ALL BEAMS
    }
  }
  if ($attackerfighters > 0 && $targetbeams > 0)
  {                         // TARGET HAS BEAMS - ATTACKER HAS FIGHTERS - BEAMS VS FIGHTERS
    if ($targetbeams > round($attackerfighters / 2))
    {                                  // TARGET BEAMS GT HALF ATTACKER FIGHTERS
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;               // A LOOSES HALF ALL FIGHTERS
      $targetbeams=$targetbeams-$lost;                         // T LOOSES BEAMS EQ TO HALF A FIGHTERS
    } else
    {                                  // TARGET BEAMS LE HALF ATTACKER FIGHTERS
      $attackerfighters=$attackerfighters-$targetbeams;        // A LOOSES FIGHTERS EQ TO T BEAMS
      $targetbeams=0;                                          // T LOOSES ALL BEAMS
    }
  }
  if ($attackerbeams > 0)
  {                         // ATTACKER HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS
    if ($attackerbeams > $targetshields)
    {                                  // ATTACKER BEAMS GT TARGET SHIELDS
      $attackerbeams=$attackerbeams-$targetshields;            // A LOOSES BEAMS EQ TO T SHIELDS
      $targetshields=0;                                        // T LOOSES ALL SHIELDS
    } else
    {                                  // ATTACKER BEAMS LE TARGET SHIELDS
      $targetshields=$targetshields-$attackerbeams;            // T LOOSES SHIELDS EQ TO A BEAMS
      $attackerbeams=0;                                        // A LOOSES ALL BEAMS
    }
  }
  if ($targetbeams > 0)
  {                         // TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS
    if ($targetbeams > $attackershields)
    {                                  // TARGET BEAMS GT ATTACKER SHIELDS
      $targetbeams=$targetbeams-$attackershields;              // T LOOSES BEAMS EQ TO A SHIELDS
      $attackershields=0;                                      // A LOOSES ALL SHIELDS
    } else
    {                                  // TARGET BEAMS LE ATTACKER SHIELDS
      $attackershields=$attackershields-$targetbeams;          // A LOOSES SHIELDS EQ TO T BEAMS
      $targetbeams=0;                                          // T LOOSES ALL BEAMS
    }
  }
  if ($targetbeams > 0)
  {                        // TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS ARMOR
    if ($targetbeams > $attackerarmor)
    {                                 // TARGET BEAMS GT ATTACKER ARMOR
      $targetbeams=$targetbeams-$attackerarmor;                // T LOOSES BEAMS EQ TO A ARMOR
      $attackerarmor=0;                                        // A LOOSES ALL ARMOR (A DESTROYED)
    } else
    {                                 // TARGET BEAMS LE ATTACKER ARMOR
      $attackerarmor=$attackerarmor-$targetbeams;              // A LOOSES ARMOR EQ TO T BEAMS
      $targetbeams=0;                                          // T LOOSES ALL BEAMS
    }
  }
  if ($targetfighters > 0 && $attackertorpdamage > 0)
  {                        // ATTACKER FIRES TORPS - TARGET HAS FIGHTERS - TORPS VS FIGHTERS
    if ($attackertorpdamage > $targetfighters)
    {                                 // ATTACKER FIRED TORPS GT TARGET FIGHTERS
      $lost=$targetfighters;
      $targetfighters=0;                                       // T LOOSES ALL FIGHTERS
      $attackertorpdamage=$attackertorpdamage-$lost;           // A LOOSES FIRED TORPS EQ TO T FIGHTERS
    } else
    {                                 // ATTACKER FIRED TORPS LE HALF TARGET FIGHTERS
      $targetfighters=$targetfighters-$attackertorpdamage;     // T LOOSES FIGHTERS EQ TO A TORPS FIRED
      $attackertorpdamage=0;                                   // A LOOSES ALL TORPS FIRED
    }
  }
  if ($attackerfighters > 0 && $targettorpdmg > 0)
  {                        // TARGET FIRES TORPS - ATTACKER HAS FIGHTERS - TORPS VS FIGHTERS
    if ($targettorpdmg > round($attackerfighters / 2))
    {                                 // TARGET FIRED TORPS GT HALF ATTACKER FIGHTERS
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;               // A LOOSES HALF ALL FIGHTERS
      $targettorpdmg=$targettorpdmg-$lost;                     // T LOOSES FIRED TORPS EQ TO HALF A FIGHTERS
    } else
    {                                 // TARGET FIRED TORPS LE HALF ATTACKER FIGHTERS
      $attackerfighters=$attackerfighters-$targettorpdmg;      // A LOOSES FIGHTERS EQ TO T TORPS FIRED
      $targettorpdmg=0;                                        // T LOOSES ALL TORPS FIRED
    }
  }
  if ($targettorpdmg > 0)
  {                        // TARGET FIRES TORPS - CONTINUE COMBAT - TORPS VS ARMOR
    if ($targettorpdmg > $attackerarmor)
    {                                 // TARGET FIRED TORPS GT HALF ATTACKER ARMOR
      $targettorpdmg=$targettorpdmg-$attackerarmor;            // T LOOSES FIRED TORPS EQ TO A ARMOR
      $attackerarmor=0;                                        // A LOOSES ALL ARMOR (A DESTROYED)
    } else
    {                                 // TARGET FIRED TORPS LE HALF ATTACKER ARMOR
      $attackerarmor=$attackerarmor-$targettorpdmg;            // A LOOSES ARMOR EQ TO T TORPS FIRED
      $targettorpdmg=0;                                        // T LOOSES ALL TORPS FIRED
    }
  }
  if ($attackerfighters > 0 && $targetfighters > 0)
  {                        // ATTACKER HAS FIGHTERS - TARGET HAS FIGHTERS - FIGHTERS VS FIGHTERS
    if ($attackerfighters > $targetfighters)
    {                                 // ATTACKER FIGHTERS GT TARGET FIGHTERS
      $temptargfighters=0;                                     // T WILL LOOSE ALL FIGHTERS
    } else
    {                                 // ATTACKER FIGHTERS LE TARGET FIGHTERS
      $temptargfighters=$targetfighters-$attackerfighters;     // T WILL LOOSE FIGHTERS EQ TO A FIGHTERS
    }
    if ($targetfighters > $attackerfighters)
    {                                 // TARGET FIGHTERS GT ATTACKER FIGHTERS
      $tempplayfighters=0;                                     // A WILL LOOSE ALL FIGHTERS
    } else
    {                                 // TARGET FIGHTERS LE ATTACKER FIGHTERS
      $tempplayfighters=$attackerfighters-$targetfighters;     // A WILL LOOSE FIGHTERS EQ TO T FIGHTERS
    }
    $attackerfighters=$tempplayfighters;
    $targetfighters=$temptargfighters;
  }
  if ($targetfighters > 0)
  {                        // TARGET HAS FIGHTERS - CONTINUE COMBAT - FIGHTERS VS ARMOR
    if ($targetfighters > $attackerarmor)
    {                                 // TARGET FIGHTERS GT ATTACKER ARMOR
      $attackerarmor=0;                                        // A LOOSES ALL ARMOR (A DESTROYED)
    } else
    {                                 // TARGET FIGHTERS LE ATTACKER ARMOR
      $attackerarmor=$attackerarmor-$targetfighters;           // A LOOSES ARMOR EQ TO T FIGHTERS
    }
  }

  //
  // FIX NEGATIVE VALUE VARS
  //
  if ($attackerfighters < 0) $attackerfighters = 0;
  if ($attackertorps    < 0) $attackertorps = 0;
  if ($attackershields  < 0) $attackershields = 0;
  if ($attackerbeams    < 0) $attackerbeams = 0;
  if ($attackerarmor    < 0) $attackerarmor = 0;
  if ($targetfighters   < 0) $targetfighters = 0;
  if ($targettorps      < 0) $targettorps = 0;
  if ($targetshields    < 0) $targetshields = 0;
  if ($targetbeams      < 0) $targetbeams = 0;


  // CHECK IF ATTACKER SHIP DESTROYED

  if (!$attackerarmor>0)
  {
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Ship destroyed by planetary defenses on planet $planetinfo[name]");
    db_kill_player($playerinfo['ship_id']);
    $xenobeisdead = 1;

    $free_ore = round($playerinfo[ship_ore]/2);
    $free_organics = round($playerinfo[ship_organics]/2);
    $free_goods = round($playerinfo[ship_goods]/2);
    $ship_value=$upgrade_cost*(round(pow ($upgrade_factor, $playerinfo[hull]))+round(pow ($upgrade_factor, $playerinfo[engines]))+round(pow ($upgrade_factor, $playerinfo[power]))+round(pow ($upgrade_factor, $playerinfo[computer]))+round(pow ($upgrade_factor, $playerinfo[sensors]))+round(pow ($upgrade_factor, $playerinfo[beams]))+round(pow ($upgrade_factor, $playerinfo[torp_launchers]))+round(pow ($upgrade_factor, $playerinfo[shields]))+round(pow ($upgrade_factor, $playerinfo[armor]))+round(pow ($upgrade_factor, $playerinfo[cloak])));
    $ship_salvage_rate=mt_rand(10,20);
    $ship_salvage=$ship_value*$ship_salvage_rate/100;
    $fighters_lost = $planetinfo[fighters] - $targetfighters;

    // LOG ATTACK TO PLANET OWNER
    playerlog ($db, $planetinfo[owner], LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|Xenobe $playerinfo[character_name]|$free_ore|$free_organics|$free_goods|$ship_salvage_rate|$ship_salvage");

    // UPDATE PLANET
    $resi = $db->Execute("UPDATE {$db->prefix}planets SET energy=$planetinfo[energy],fighters=fighters-$fighters_lost, torps=torps-$targettorps, ore=ore+$free_ore, goods=goods+$free_goods, organics=organics+$free_organics, credits=credits+$ship_salvage WHERE planet_id=$planetinfo[planet_id]");
    db_op_result ($db, $resi, __LINE__, __FILE__, $db_logging);

  }

  // MUST HAVE MADE IT PAST PLANET DEFENSES

  else
  {
    $armor_lost = $playerinfo[armor_pts] - $attackerarmor;
    $fighters_lost = $playerinfo[ship_fighters] - $attackerfighters;
    $target_fighters_lost = $planetinfo[ship_fighters] - $targetfighters;
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Made it past defenses on planet $planetinfo[name]");

    // UPDATE ATTACKER
    $resj = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy=$playerinfo[ship_energy], ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps, armor_pts=armor_pts-$armor_lost WHERE ship_id=$playerinfo[ship_id]");
    db_op_result ($db, $resj, __LINE__, __FILE__, $db_logging);
    $playerinfo[ship_fighters] = $attackerfighters;
    $playerinfo[torps] = $attackertorps;
    $playerinfo[armor_pts] = $attackerarmor;


    // UPDATE PLANET
    $resk = $db->Execute ("UPDATE {$db->prefix}planets SET energy=$planetinfo[energy], fighters=$targetfighters, torps=torps-$targettorps WHERE planet_id=$planetinfo[planet_id]");
    db_op_result ($db, $resk, __LINE__, __FILE__, $db_logging);
    $planetinfo[fighters] = $targetfighters;
    $planetinfo[torps] = $targettorps;

    // NOW WE MUST ATTACK ALL SHIPS ON THE PLANET ONE BY ONE
    $resultps = $db->Execute("SELECT ship_id,ship_name FROM {$db->prefix}ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
    db_op_result ($db, $resultps, __LINE__, __FILE__, $db_logging);
    $shipsonplanet = $resultps->RecordCount();
    if ($shipsonplanet > 0)
    {
      while (!$resultps->EOF && $xenobeisdead < 1)
      {
        $onplanet = $resultps->fields;
        xenobetoship($onplanet[ship_id]);
        $resultps->MoveNext();
      }
    }
    $resultps = $db->Execute("SELECT ship_id,ship_name FROM {$db->prefix}ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
    db_op_result ($db, $resultps, __LINE__, __FILE__, $db_logging);
    $shipsonplanet = $resultps->RecordCount();
    if ($shipsonplanet == 0 && $xenobeisdead < 1)
    {
      // MUST HAVE KILLED ALL SHIPS ON PLANET
      playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Defeated all ships on planet $planetinfo[name]");
      // LOG ATTACK TO PLANET OWNER
      playerlog ($db, $planetinfo[owner], LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");

      // UPDATE PLANET
      $resl = $db->Execute("UPDATE {$db->prefix}planets SET fighters=0, torps=0, base='N', owner=0, corp=0 WHERE planet_id=$planetinfo[planet_id]");
      db_op_result ($db, $resl, __LINE__, __FILE__, $db_logging);
      calc_ownership($planetinfo[sector_id]);

    } else {
      // MUST HAVE DIED TRYING
      playerlog ($db, $playerinfo[ship_id], LOG_RAW, "We were KILLED by ships defending planet $planetinfo[name]");
      // LOG ATTACK TO PLANET OWNER
      playerlog ($db, $planetinfo[owner], LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|Xenobe $playerinfo[character_name]|0|0|0|0|0");

      // NO SALVAGE FOR PLANET BECAUSE WENT TO SHIP WHO WON
    }

  }


  // END OF Xenobe PLANET ATTACK CODE
  $resx = $db->Execute("UNLOCK TABLES");
  db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
}
?>
