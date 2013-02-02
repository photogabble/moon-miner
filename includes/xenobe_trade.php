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
// File: includes/xenobe_trade.php

// Todo: SQL bind variables
if (strpos ($_SERVER['PHP_SELF'], 'xenobe_trade.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function xenobe_trade ($db)
{
    // Setup general variables
    global $playerinfo, $inventory_factor, $ore_price, $ore_delta, $ore_limit, $goods_price;
    global $goods_delta, $goods_limit, $organics_price, $organics_delta, $organics_limit, $xenobeisdead;

    // We need to get rid of this.. the bug causing it needs to be identified and squashed. In the meantime, we want functional xen's. :)
    $ore_price = 11;
    $organics_price = 5;
    $goods_price = 15;

    // Obtain sector information
    $sectres = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id = ?;", array ($playerinfo['sector']));
    db_op_result ($db, $sectres, __LINE__, __FILE__);
    $sectorinfo = $sectres->fields;

    // Obtain zone information
    $zoneres = $db->Execute ("SELECT zone_id, allow_attack, allow_trade FROM {$db->prefix}zones WHERE zone_id = ?;", array ($sectorinfo['zone_id']));
    db_op_result ($db, $zoneres, __LINE__, __FILE__);
    $zonerow = $zoneres->fields;

    // Make sure we can trade here
    if ($zonerow[allow_trade]=="N") return;

    // Check for a port we can use
    if ($sectorinfo[port_type] == "none") return;

    // Xenobe do not trade at energy ports since they regen energy
    if ($sectorinfo[port_type] == "energy") return;

    // Check for negative credits or cargo
    if ($playerinfo[ship_ore]<0) $playerinfo[ship_ore] = $shipore = 0;
    if ($playerinfo[ship_organics]<0) $playerinfo[ship_organics] = $shiporganics = 0;
    if ($playerinfo[ship_goods]<0) $playerinfo[ship_goods] = $shipgoods = 0;
    if ($playerinfo[credits]<0) $playerinfo[credits] = $shipcredits = 0;
    if ($sectorinfo[port_ore] <= 0) return;
    if ($sectorinfo[port_organics] <= 0) return;
    if ($sectorinfo[port_goods] <= 0) return;

  //
  //  CHECK Xenobe CREDIT/CARGO
  //
  if ($playerinfo[ship_ore] > 0) $shipore = $playerinfo[ship_ore];
  if ($playerinfo[ship_organics] > 0) $shiporganics = $playerinfo[ship_organics];
  if ($playerinfo[ship_goods] > 0) $shipgoods = $playerinfo[ship_goods];
  if ($playerinfo[credits] > 0) $shipcredits = $playerinfo[credits];
  // MAKE SURE WE HAVE CARGO OR CREDITS
  if (!$playerinfo[credits] > 0 && !$playerinfo[ship_ore] > 0 && !$playerinfo[ship_goods] > 0 && !$playerinfo[ship_organics] > 0) return;

  //
  //  MAKE SURE CARGOS COMPATABLE
  //
  if ($sectorinfo[port_type] == "ore" && $shipore > 0) return;
  if ($sectorinfo[port_type] == "organics" && $shiporganics > 0) return;
  if ($sectorinfo[port_type] == "goods" && $shipgoods > 0) return;

  //
  // LETS TRADE SOME CARGO *
  //
  if ($sectorinfo[port_type] == "ore")
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
    $total_cost = round (($amount_ore * $ore_price) - ($amount_organics * $organics_price + $amount_goods * $goods_price));
    $newcredits = max (0, $playerinfo['credits'] - $total_cost);
    $newore = $playerinfo['ship_ore'] + $amount_ore;
    $neworganics = max (0, $playerinfo['ship_organics'] - $amount_organics);
    $newgoods = max (0, $playerinfo['ship_goods'] - $amount_goods);
    $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating = rating + 1, credits = ?, ship_ore = ?, ship_organics = ?, ship_goods = ? WHERE ship_id = ?;", array ($newcredits, $newore, $neworganics, $newgoods, $playerinfo['ship_id']));
    db_op_result ($db, $trade_result, __LINE__, __FILE__);
    $trade_result2 = $db->Execute("UPDATE {$db->prefix}universe SET port_ore = port_ore - ?, port_organics = port_organics + ?, port_goods = port_goods + ? WHERE sector_id = ?;", array ($amount_ore, $amount_organics, $amount_goods, $sectorinfo['sector_id']));
    db_op_result ($db, $trade_result2, __LINE__, __FILE__);
    \bnt\PlayerLog::writeLog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe Trade Results: Sold $amount_organics Organics Sold $amount_goods Goods Bought $amount_ore Ore Cost $total_cost");
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
    $newcredits = max (0, $playerinfo['credits'] - $total_cost);
    $newore = max (0, $playerinfo['ship_ore'] - $amount_ore);
    $neworganics = $playerinfo['ship_organics'] + $amount_organics;
    $newgoods = max (0, $playerinfo['ship_goods'] - $amount_goods);
    $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating = rating + 1, credits = ?, ship_ore = ?, ship_organics = ?, ship_goods = ? WHERE ship_id = ?;", array ($newcredits, $newore, $neworganics, $newgoods, $playerinfo['ship_id']));
    db_op_result ($db, $trade_result, __LINE__, __FILE__);
    $trade_result2 = $db->Execute("UPDATE {$db->prefix}universe SET port_ore = port_ore + ?, port_organics = port_organics - ?, port_goods = port_goods + ? WHERE sector_id = ?;", array ($amount_ore, $amount_organics, $amount_goods, $sectorinfo['sector_id']));
    db_op_result ($db, $trade_result2, __LINE__, __FILE__);
    \bnt\PlayerLog::writeLog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe Trade Results: Sold $amount_goods Goods Sold $amount_ore Ore Bought $amount_organics Organics Cost $total_cost");
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
    $newcredits = max (0, $playerinfo['credits'] - $total_cost);
    $newore = max (0, $playerinfo['ship_ore'] - $amount_ore);
    $neworganics = max (0, $playerinfo['ship_organics'] - $amount_organics);
    $newgoods = $playerinfo['ship_goods'] + $amount_goods;
    $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating=rating+1, credits = ?, ship_ore = ?, ship_organics = ?, ship_goods = ? WHERE ship_id = ?;", array ($newcredits, $newore, $neworganics, $newgoods, $playerinfo['ship_id']));
    db_op_result ($db, $trade_result, __LINE__, __FILE__);
    $trade_result2 = $db->Execute("UPDATE {$db->prefix}universe SET port_ore=port_ore + ?, port_organics = port_organics + ?, port_goods = port_goods - ? WHERE sector_id = ?;", array ($amount_ore, $amount_organics, $amount_goods, $sectorinfo['sector_id']));
    db_op_result ($db, $trade_result2, __LINE__, __FILE__);
    \bnt\PlayerLog::writeLog ($db, $playerinfo[ship_id], LOG_RAW, "Xenobe Trade Results: Sold $amount_ore Ore Sold $amount_organics Organics Bought $amount_goods Goods Cost $total_cost");
  }

}
?>
