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
// File: sched_xenobe.php

if (preg_match("/sched_xenobe.php/i", $_SERVER['PHP_SELF']))
{
    echo "You can not access this file directly!";
    die();
}

// Xenobe TURN UPDATES
echo "<br><strong>Xenobe TURNS</strong><br><br>";

// INCLUDE FUNCTIONS
include_once "xenobe_funcs.php";
include_once "languages/$lang";
global $targetlink;
global $xenobeisdead;

// MAKE Xenobe SELECTION
$furcount = $furcount0 = $furcount0a = $furcount1 = $furcount1a = $furcount2 = $furcount2a = $furcount3 = $furcount3a = $furcount3h = 0;

// Lock the tables.
$db->Execute("LOCK TABLES {$db->prefix}xenobe WRITE, {$db->prefix}ships WRITE;");

$res = $db->Execute("SELECT * FROM {$db->prefix}ships JOIN {$db->prefix}xenobe WHERE email=xenobe_id and active='Y' and ship_destroyed='N' ORDER BY ship_id;");
while (!$res->EOF)
{
    $xenobeisdead = 0;
    $playerinfo = $res->fields;
    // *********************************
    // ****** REGENERATE/BUY STATS *****
    // *********************************
    xenoberegen();
    // *********************************
    // ****** RUN THROUGH ORDERS *******
    // *********************************
    $furcount++;
    if (mt_rand(1,5) > 1)                                 // ****** 20% CHANCE OF NOT MOVING AT ALL ******
    {
      // *********************************
      // ****** ORDERS = 0 SENTINEL ******
      // *********************************
      if ($playerinfo[orders] == 0)
      {
        $furcount0++;
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF, NOT ON A PLANET ******

        $SQL = "SELECT * FROM {$db->prefix}ships WHERE sector=$playerinfo[sector] AND email!='$playerinfo[email]' AND email NOT LIKE '%@xenobe' AND planet_id=0 AND ship_id > 1";
        $reso0 = $db->Execute($SQL);
        if (!$reso0->EOF)
        {
          $rowo0 = $reso0->fields;
          if ($playerinfo[aggression] == 0)            // ****** O = 0 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Sit As A Target Himself
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 0 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Xenobe's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[ship_fighters] > $rowo0[ship_fighters])
            {
              $furcount0a++;
              playerlog ($db, $playerinfo[ship_id], LOG_Xenobe_ATTACK, "$rowo0[character_name]");
              xenobetoship($rowo0[ship_id]);
              if ($xenobeisdead>0) {
                $res->MoveNext();
                continue;
              }
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 0 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount0a++;
            playerlog ($db, $playerinfo[ship_id], LOG_Xenobe_ATTACK, "$rowo0[character_name]");
            xenobetoship($rowo0[ship_id]);
            if ($xenobeisdead>0) {
              $res->MoveNext();
              continue;
            }
          }
        }
      }
      // *********************************
      // ******** ORDERS = 1 ROAM ********
      // *********************************
      elseif ($playerinfo[orders] == 1)
      {
        $furcount1++;
        // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
        $targetlink = $playerinfo[sector];
        xenobemove();
        if ($xenobeisdead>0) {
          $res->MoveNext();
          continue;
        }
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF ******
        $reso1 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE sector=$targetlink and email!='$playerinfo[email]' and ship_id > 1");
        if (!$reso1->EOF)
        {
          $rowo1= $reso1->fields;
          if ($playerinfo[aggression] == 0)            // ****** O = 1 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Roam Around As A Target Himself
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 1 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Xenobe's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[ship_fighters] > $rowo1[ship_fighters] && $rowo1[planet_id] == 0)
            {
              $furcount1a++;
              playerlog ($db, $playerinfo[ship_id], LOG_Xenobe_ATTACK, "$rowo1[character_name]");
              xenobetoship($rowo1[ship_id]);
              if ($xenobeisdead>0) {
                $res->MoveNext();
                continue;
              }
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 1 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount1a++;
            playerlog ($db, $playerinfo[ship_id], LOG_Xenobe_ATTACK, "$rowo1[character_name]");
            if (!$rowo1[planet_id] == 0) {              // *** IS ON PLANET ***
              xenobetoplanet($rowo1[planet_id]);
            } else {
              xenobetoship($rowo1[ship_id]);
            }
            if ($xenobeisdead>0) {
              $res->MoveNext();
              continue;
            }
          }
        }
      }
      // *********************************
      // *** ORDERS = 2 ROAM AND TRADE ***
      // *********************************
      elseif ($playerinfo[orders] == 2)
      {
        $furcount2++;
        // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
        $targetlink = $playerinfo[sector];
        xenobemove();
        if ($xenobeisdead>0) {
          $res->MoveNext();
          continue;
        }
        // ****** NOW TRADE BEFORE WE DO ANY AGGRESSION CHECKS ******
        xenobetrade();
        // ****** FIND A TARGET ******
        // ****** IN MY SECTOR, NOT MYSELF ******
        $reso2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE sector=$targetlink and email!='$playerinfo[email]' and ship_id > 1");
        if (!$reso2->EOF)
        {
          $rowo2=$reso2->fields;
          if ($playerinfo[aggression] == 0)            // ****** O = 2 & AGRESSION = 0 PEACEFUL ******
          {
            // This Guy Does Nothing But Roam And Trade
          }
          elseif ($playerinfo[aggression] == 1)        // ****** O = 2 & AGRESSION = 1 ATTACK SOMETIMES ******
          {
            // Xenobe's only compare number of fighters when determining if they have an attack advantage
            if ($playerinfo[ship_fighters] > $rowo2[ship_fighters] && $rowo2[planet_id] == 0)
            {
              $furcount2a++;
              playerlog ($db, $playerinfo[ship_id], LOG_Xenobe_ATTACK, "$rowo2[character_name]");
              xenobetoship($rowo2[ship_id]);
              if ($xenobeisdead>0) {
                $res->MoveNext();
                continue;
              }
            }
          }
          elseif ($playerinfo[aggression] == 2)        // ****** O = 2 & AGRESSION = 2 ATTACK ALLWAYS ******
          {
            $furcount2a++;
            playerlog ($db, $playerinfo[ship_id], LOG_Xenobe_ATTACK, "$rowo2[character_name]");
            if (!$rowo2[planet_id] == 0) {              // *** IS ON PLANET ***
              xenobetoplanet($rowo2[planet_id]);
            } else {
              xenobetoship($rowo2[ship_id]);
            }
            if ($xenobeisdead>0) {
              $res->MoveNext();
              continue;
            }
          }
        }
      }
      // *********************************
      // *** ORDERS = 3 ROAM AND HUNT  ***
      // *********************************
      elseif ($playerinfo[orders] == 3)
      {
        $furcount3++;
        // ****** LET SEE IF WE GO HUNTING THIS ROUND BEFORE WE DO ANYTHING ELSE ******
        $hunt=mt_rand(0,3);                               // *** 25% CHANCE OF HUNTING ***
        // Uncomment below for Debugging
        //$hunt=0;
        if ($hunt==0)
        {
        $furcount3h++;
        xenobehunter();
        if ($xenobeisdead>0) {
          $res->MoveNext();
          continue;
        }
        } else
        {
          // ****** ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE ******
          xenobemove();
          if ($xenobeisdead>0) {
            $res->MoveNext();
            continue;
          }
          // ****** FIND A TARGET ******
          // ****** IN MY SECTOR, NOT MYSELF ******
          $reso3 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE sector=$playerinfo[sector] and email!='$playerinfo[email]' and ship_id > 1");
          if (!$reso3->EOF)
          {
            $rowo3=$reso3->fields;
            if ($playerinfo[aggression] == 0)            // ****** O = 3 & AGRESSION = 0 PEACEFUL ******
            {
              // This Guy Does Nothing But Roam Around As A Target Himself
            }
            elseif ($playerinfo[aggression] == 1)        // ****** O = 3 & AGRESSION = 1 ATTACK SOMETIMES ******
            {
              // Xenobe's only compare number of fighters when determining if they have an attack advantage
              if ($playerinfo[ship_fighters] > $rowo3[ship_fighters] && $rowo3[planet_id] == 0)
              {
                $furcount3a++;
                playerlog ($db, $playerinfo[ship_id], LOG_Xenobe_ATTACK, "$rowo3[character_name]");
                xenobetoship($rowo3[ship_id]);
                if ($xenobeisdead>0) {
                  $res->MoveNext();
                  continue;
                }
              }
            }
            elseif ($playerinfo[aggression] == 2)        // ****** O = 3 & AGRESSION = 2 ATTACK ALLWAYS ******
            {
              $furcount3a++;
              playerlog ($db, $playerinfo[ship_id], LOG_Xenobe_ATTACK, "$rowo3[character_name]");
              if (!$rowo3[planet_id] == 0) {              // *** IS ON PLANET ***
                xenobetoplanet($rowo3[planet_id]);
              } else {
                xenobetoship($rowo3[ship_id]);
              }
              if ($xenobeisdead>0) {
                $res->MoveNext();
                continue;
              }
            }
          }
        }
      }
    }
    $res->MoveNext();
  }
  $res->_close();

  $furnonmove = $furcount - ($furcount0 + $furcount1 + $furcount2 + $furcount3);
  echo "Counted $furcount Xenobe players that are ACTIVE with working ships.<br>";
  echo "$furnonmove Xenobe players did not do anything this round. <br>";
  echo "$furcount0 Xenobe players had SENTINEL orders of which $furcount0a launched attacks. <br>";
  echo "$furcount1 Xenobe players had ROAM orders of which $furcount1a launched attacks. <br>";
  echo "$furcount2 Xenobe players had ROAM AND TRADE orders of which $furcount2a launched attacks. <br>";
  echo "$furcount3 Xenobe players had ROAM AND HUNT orders of which $furcount3a launched attacks and $furcount3h went hunting. <br>";
  echo "Xenobe TURNS COMPLETE. <br>";
  echo "<br>";
  // *********************************
  // ***** END OF Xenobe TURNS *****
  // *********************************

// Unlock the tables.
$db->Execute("UNLOCK TABLES;");

?>
