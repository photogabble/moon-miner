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
// File: includes/xenoberegen.php

function xenoberegen()
{
  //
  // SETUP GENERAL VARIABLES
  //
  global $playerinfo;
  global $xen_unemployment;
  global $xenobeisdead;
  global $db, $db_logging;

  // Xenobe Unempoyment Check

  $playerinfo[credits] = $playerinfo[credits] + $xen_unemployment;
  //
  // LETS REGENERATE ENERGY
  //
  $maxenergy = NUM_ENERGY($playerinfo[power]);
  if ($playerinfo[ship_energy] <= ($maxenergy - 50))  // STOP REGEN WHEN WITHIN 50 OF MAX
  {                                                   // REGEN HALF OF REMAINING ENERGY
    $playerinfo[ship_energy] = $playerinfo[ship_energy] + round(($maxenergy - $playerinfo[ship_energy])/2);
    $gene = "regenerated Energy to $playerinfo[ship_energy] units,";
  }

  //
  // LETS REGENERATE ARMOR
  //
  $maxarmor = NUM_ARMOR($playerinfo[armor]);
  if ($playerinfo[armor_pts] <= ($maxarmor - 50))  // STOP REGEN WHEN WITHIN 50 OF MAX
  {                                                  // REGEN HALF OF REMAINING ARMOR
    $playerinfo[armor_pts] = $playerinfo[armor_pts] + round(($maxarmor - $playerinfo[armor_pts])/2);
    $gena = "regenerated Armor to $playerinfo[armor_pts] points,";
  }

  //
  // LETS BUY FIGHTERS/TORPS
  //

  //
  // Xenobe PAY 6/FIGHTER
  //
  $available_fighters = NUM_FIGHTERS($playerinfo[computer]) - $playerinfo[ship_fighters];
  if (($playerinfo[credits]>5) && ($available_fighters>0))
  {
    if (round($playerinfo[credits]/6)>$available_fighters)
    {
      $purchase = ($available_fighters*6);
      $playerinfo[credits] = $playerinfo[credits] - $purchase;
      $playerinfo[ship_fighters] = $playerinfo[ship_fighters] + $available_fighters;
      $genf = "purchased $available_fighters fighters for $purchase credits,";
    }
    if (round($playerinfo[credits]/6)<=$available_fighters)
    {
      $purchase = (round($playerinfo[credits]/6));
      $playerinfo[ship_fighters] = $playerinfo[ship_fighters] + $purchase;
      $genf = "purchased $purchase fighters for $playerinfo[credits] credits,";
      $playerinfo[credits] = 0;
    }
  }

  //
  // Xenobe PAY 3/TORPEDO
  //
  $available_torpedoes = NUM_TORPEDOES($playerinfo[torp_launchers]) - $playerinfo[torps];
  if (($playerinfo[credits]>2) && ($available_torpedoes>0))
  {
    if (round($playerinfo[credits]/3)>$available_torpedoes)
    {
      $purchase = ($available_torpedoes*3);
      $playerinfo[credits] = $playerinfo[credits] - $purchase;
      $playerinfo[torps] = $playerinfo[torps] + $available_torpedoes;
      $gent = "purchased $available_torpedoes torpedoes for $purchase credits,";
    }
    if (round($playerinfo[credits]/3)<=$available_torpedoes)
    {
      $purchase = (round($playerinfo[credits]/3));
      $playerinfo[torps] = $playerinfo[torps] + $purchase;
      $gent = "purchased $purchase torpedoes for $playerinfo[credits] credits,";
      $playerinfo[credits] = 0;
    }
  }

  //
  // UPDATE Xenobe RECORD
  //
  $resg = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy=$playerinfo[ship_energy], armor_pts=$playerinfo[armor_pts], ship_fighters=$playerinfo[ship_fighters], torps=$playerinfo[torps], credits=$playerinfo[credits] WHERE ship_id=$playerinfo[ship_id]");
  db_op_result ($db, $resg, __LINE__, __FILE__, $db_logging);
  if (!$gene=='' || !$gena=='' || !$genf=='' || !$gent=='')
  {
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe $gene $gena $genf $gent and has been updated.");
  }

}

function xenobetrade()
{
  //
  // SETUP GENERAL VARIABLES
  //
  global $playerinfo;
  global $inventory_factor;
  global $ore_price;
  global $ore_delta;
  global $ore_limit;
  global $goods_price;
  global $goods_delta;
  global $goods_limit;
  global $organics_price;
  global $organics_delta;
  global $organics_limit;
  global $xenobeisdead;
  global $db, $db_logging;
  // We need to get rid of this.. the bug causing it needs to be identified and squashed. In the meantime, we want functional xen's. :)
    $ore_price = 11;
    $organics_price = 5;
    $goods_price = 15;

  // OBTAIN SECTOR INFORMATION
  $sectres = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id='$playerinfo[sector]'");
  db_op_result ($db, $sectres, __LINE__, __FILE__, $db_logging);
  $sectorinfo = $sectres->fields;

  // OBTAIN ZONE INFORMATION
  $zoneres = $db->Execute ("SELECT zone_id,allow_attack,allow_trade FROM {$db->prefix}zones WHERE zone_id='$sectorinfo[zone_id]'");
  db_op_result ($db, $zoneres, __LINE__, __FILE__, $db_logging);
  $zonerow = $zoneres->fields;

  // Debug info
  //playerlog ($db, $playerinfo[ship_id], LOG_RAW, "PORT $sectorinfo[port_type] ALLOW_TRADE $zonerow[allow_trade] PORE $sectorinfo[port_ore] PORG $sectorinfo[port_organics] PGOO $sectorinfo[port_goods] ORE $playerinfo[ship_ore] ORG $playerinfo[ship_organics] GOO $playerinfo[ship_goods] CREDITS $playerinfo[credits] ");

  //
  //  MAKE SURE WE CAN TRADE HERE
  //
  if ($zonerow[allow_trade]=="N") return;

  //
  //  CHECK FOR A PORT WE CAN USE
  //
  if ($sectorinfo[port_type] == "none") return;
  // Xenobe DO NOT TRADE AT ENERGY PORTS SINCE THEY REGEN ENERGY
  if ($sectorinfo[port_type] == "energy") return;

  //
  //  CHECK FOR NEG CREDIT/CARGO
  //
  if ($playerinfo[ship_ore]<0) $playerinfo[ship_ore]=$shipore=0;
  if ($playerinfo[ship_organics]<0) $playerinfo[ship_organics]=$shiporganics=0;
  if ($playerinfo[ship_goods]<0) $playerinfo[ship_goods]=$shipgoods=0;
  if ($playerinfo[credits]<0) $playerinfo[credits]=$shipcredits=0;
  if ($sectorinfo[port_ore] <= 0) return;
  if ($sectorinfo[port_organics] <= 0) return;
  if ($sectorinfo[port_goods] <= 0) return;

  //
  //  CHECK Xenobe CREDIT/CARGO
  //
  if ($playerinfo[ship_ore]>0) $shipore=$playerinfo[ship_ore];
  if ($playerinfo[ship_organics]>0) $shiporganics=$playerinfo[ship_organics];
  if ($playerinfo[ship_goods]>0) $shipgoods=$playerinfo[ship_goods];
  if ($playerinfo[credits]>0) $shipcredits=$playerinfo[credits];
  // MAKE SURE WE HAVE CARGO OR CREDITS
  if (!$playerinfo[credits]>0 && !$playerinfo[ship_ore]>0 && !$playerinfo[ship_goods]>0 && !$playerinfo[ship_organics]>0) return;

  //
  //  MAKE SURE CARGOS COMPATABLE
  //
  if ($sectorinfo[port_type]=="ore" && $shipore>0) return;
  if ($sectorinfo[port_type]=="organics" && $shiporganics>0) return;
  if ($sectorinfo[port_type]=="goods" && $shipgoods>0) return;

  //
  // LETS TRADE SOME CARGO *
  //
  if ($sectorinfo[port_type]=="ore")
  //
  // PORT ORE
  //
  {
    // SET THE PRICES
    $ore_price = $ore_price - $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $organics_price = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $goods_price = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    //  SET CARGO BUY/SELL
    $amount_organics = $playerinfo[ship_organics];
    $amount_goods = $playerinfo[ship_goods];
    // SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT
    $amount_ore = NUM_HOLDS($playerinfo[hull]);
    // WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL
    $amount_ore = min($amount_ore, $sectorinfo[port_ore]);
    // WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY
    $amount_ore = min($amount_ore, floor(($playerinfo[credits] + $amount_organics * $organics_price + $amount_goods * $goods_price) / $ore_price));
    // BUY/SELL CARGO
    $total_cost = round(($amount_ore * $ore_price) - ($amount_organics * $organics_price + $amount_goods * $goods_price));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = $playerinfo[ship_ore]+$amount_ore;
    $neworganics = max(0,$playerinfo[ship_organics]-$amount_organics);
    $newgoods = max(0,$playerinfo[ship_goods]-$amount_goods);
    $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
    db_op_result ($db, $trade_result, __LINE__, __FILE__, $db_logging);
    $trade_result2 = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore-$amount_ore, port_organics=port_organics+$amount_organics, port_goods=port_goods+$amount_goods where sector_id=$sectorinfo[sector_id]");
    db_op_result ($db, $trade_result2, __LINE__, __FILE__, $db_logging);
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe Trade Results: Sold $amount_organics Organics Sold $amount_goods Goods Bought $amount_ore Ore Cost $total_cost");
  }
  if ($sectorinfo[port_type]=="organics")
  //
  // PORT ORGANICS
  //
  {
    // SET THE PRICES
    $organics_price = $organics_price - $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $ore_price = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $goods_price = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    //
    //  SET CARGO BUY/SELL
    //
    $amount_ore = $playerinfo[ship_ore];
    $amount_goods = $playerinfo[ship_goods];
    // SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT
    $amount_organics = NUM_HOLDS($playerinfo[hull]);
    // WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL
    $amount_organics = min($amount_organics, $sectorinfo[port_organics]);
    // WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY
    $amount_organics = min($amount_organics, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_goods * $goods_price) / $organics_price));
    //
    // BUY/SELL CARGO
    //
    $total_cost = round(($amount_organics * $organics_price) - ($amount_ore * $ore_price + $amount_goods * $goods_price));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = max(0,$playerinfo[ship_ore]-$amount_ore);
    $neworganics = $playerinfo[ship_organics]+$amount_organics;
    $newgoods = max(0,$playerinfo[ship_goods]-$amount_goods);
    $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
    db_op_result ($db, $trade_result, __LINE__, __FILE__, $db_logging);
    $trade_result2 = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore+$amount_ore, port_organics=port_organics-$amount_organics, port_goods=port_goods+$amount_goods where sector_id=$sectorinfo[sector_id]");
    db_op_result ($db, $trade_result2, __LINE__, __FILE__, $db_logging);
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe Trade Results: Sold $amount_goods Goods Sold $amount_ore Ore Bought $amount_organics Organics Cost $total_cost");
  }
  if ($sectorinfo[port_type]=="goods")
  //
  // PORT GOODS *
  //
  {
    //
    // SET THE PRICES
    //
    $goods_price = $goods_price - $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $ore_price = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $organics_price = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    //
    //  SET CARGO BUY/SELL
    //
    $amount_ore = $playerinfo[ship_ore];
    $amount_organics = $playerinfo[ship_organics];
    // SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT
    $amount_goods = NUM_HOLDS($playerinfo[hull]);
    // WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL
    $amount_goods = min($amount_goods, $sectorinfo[port_goods]);
    // WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY
    $amount_goods = min($amount_goods, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_organics * $organics_price) / $goods_price));
    //
    // BUY/SELL CARGO
    //
    $total_cost = round(($amount_goods * $goods_price) - ($amount_organics * $organics_price + $amount_ore * $ore_price));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = max(0,$playerinfo[ship_ore]-$amount_ore);
    $neworganics = max(0,$playerinfo[ship_organics]-$amount_organics);
    $newgoods = $playerinfo[ship_goods]+$amount_goods;
    $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
    db_op_result ($db, $trade_result, __LINE__, __FILE__, $db_logging);
    $trade_result2 = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore+$amount_ore, port_organics=port_organics+$amount_organics, port_goods=port_goods-$amount_goods where sector_id=$sectorinfo[sector_id]");
    db_op_result ($db, $trade_result2, __LINE__, __FILE__, $db_logging);
    playerlog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe Trade Results: Sold $amount_ore Ore Sold $amount_organics Organics Bought $amount_goods Goods Cost $total_cost");
  }

}

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
