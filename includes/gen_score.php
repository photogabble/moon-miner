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
// File: includes/gen_score.php

if (preg_match("/gen_score.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function gen_score ($sid)
{
    global $dbtables,$db;
    global $upgrade_factor;
    global $upgrade_cost;
    global $torpedo_price;
    global $armor_price;
    global $fighter_price;
    global $ore_price;
    global $organics_price;
    global $goods_price;
    global $energy_price;
    global $colonist_price;
    global $dev_genesis_price;
    global $dev_beacon_price;
    global $dev_emerwarp_price;
    global $dev_warpedit_price;
    global $dev_minedeflector_price;
    global $dev_escapepod_price;
    global $dev_fuelscoop_price;
    global $dev_lssd_price;
    global $base_ore;
    global $base_goods;
    global $base_organics;
    global $base_credits;

    $calc_hull           = "ROUND(pow($upgrade_factor,hull))";
    $calc_engines        = "ROUND(pow($upgrade_factor,engines))";
    $calc_power          = "ROUND(pow($upgrade_factor,power))";
    $calc_computer       = "ROUND(pow($upgrade_factor,computer))";
    $calc_sensors        = "ROUND(pow($upgrade_factor,sensors))";
    $calc_beams          = "ROUND(pow($upgrade_factor,beams))";
    $calc_torp_launchers = "ROUND(pow($upgrade_factor,torp_launchers))";
    $calc_shields        = "ROUND(pow($upgrade_factor,shields))";
    $calc_armor          = "ROUND(pow($upgrade_factor,armor))";
    $calc_cloak          = "ROUND(pow($upgrade_factor,cloak))";
    $calc_levels         = "($calc_hull + $calc_engines + $calc_power + $calc_computer + $calc_sensors + $calc_beams + $calc_torp_launchers + $calc_shields + $calc_armor + $calc_cloak) * $upgrade_cost";

    $calc_torps          = "$dbtables[ships].torps * $torpedo_price";
    $calc_armor_pts      = "armor_pts * $armor_price";
    $calc_ship_ore       = "ship_ore * $ore_price";
    $calc_ship_organics  = "ship_organics * $organics_price";
    $calc_ship_goods     = "ship_goods * $goods_price";
    $calc_ship_energy    = "ship_energy * $energy_price";
    $calc_ship_colonists = "ship_colonists * $colonist_price";
    $calc_ship_fighters  = "ship_fighters * $fighter_price";
    $calc_equip          = "$calc_torps + $calc_armor_pts + $calc_ship_ore + $calc_ship_organics + $calc_ship_goods + $calc_ship_energy + $calc_ship_colonists + $calc_ship_fighters";

    $calc_dev_warpedit      = "dev_warpedit * $dev_warpedit_price";
    $calc_dev_genesis       = "dev_genesis * $dev_genesis_price";
    $calc_dev_beacon        = "dev_beacon * $dev_beacon_price";
    $calc_dev_emerwarp      = "dev_emerwarp * $dev_emerwarp_price";
    $calc_dev_escapepod     = "if (dev_escapepod='Y', $dev_escapepod_price, 0)";
    $calc_dev_fuelscoop     = "if (dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
    $calc_dev_lssd          = "if (dev_lssd='Y', $dev_lssd_price, 0)";
    $calc_dev_minedeflector = "dev_minedeflector * $dev_minedeflector_price";
    $calc_dev               = "$calc_dev_warpedit + $calc_dev_genesis + $calc_dev_beacon + $calc_dev_emerwarp + $calc_dev_escapepod + $calc_dev_fuelscoop + $calc_dev_minedeflector + $calc_dev_lssd";

    $calc_planet_goods      = "SUM($dbtables[planets].organics) * $organics_price + SUM($dbtables[planets].ore) * $ore_price + SUM($dbtables[planets].goods) * $goods_price + SUM($dbtables[planets].energy) * $energy_price";
    $calc_planet_colonists  = "SUM($dbtables[planets].colonists) * $colonist_price";
    $calc_planet_defence    = "SUM($dbtables[planets].fighters) * $fighter_price + if ($dbtables[planets].base='Y', $base_credits + SUM($dbtables[planets].torps) * $torpedo_price, 0)";
    $calc_planet_credits    = "SUM($dbtables[planets].credits)";

    $res = $db->Execute("SELECT $calc_levels+$calc_equip+$calc_dev+$dbtables[ships].credits+$calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits AS score FROM $dbtables[ships] LEFT JOIN $dbtables[planets] ON $dbtables[planets].owner=ship_id WHERE ship_id=$sid AND ship_destroyed='N'");
    $row = $res->fields;
    $score = $row['score'];
    $res = $db->Execute("SELECT balance, loan FROM $dbtables[ibank_accounts] where ship_id = $sid");
    if ($res)
    {
        $row = $res->fields;
        $score += ($row['balance'] - $row['loan']);
    }

    if ($score < 0)
    {
        $score=0;
    }

    $score = ROUND(SQRT($score));
    $db->Execute("UPDATE $dbtables[ships] SET score=$score WHERE ship_id=$sid");

    return $score;
}
?>
