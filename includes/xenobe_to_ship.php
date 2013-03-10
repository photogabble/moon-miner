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
// File: xenobe_to_ship.php

// Todo: SQL bind variables for all SQL calls

if (strpos ($_SERVER['PHP_SELF'], 'xenobe_to_ship.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function xenobe_to_ship ($db, $ship_id)
{
    // Setup general variables
    global $attackerbeams;
    global $attackerfighters;
    global $attackershields;
    global $attackertorps;
    global $attackerarmor;
    global $attackertorpdamage;
    global $start_energy;
    global $playerinfo;
    global $rating_combat_factor;
    global $upgrade_cost;
    global $upgrade_factor;
    global $sector_max;
    global $xenobeisdead;

    // Lookup target details
    $resa = $db->Execute ("LOCK TABLES {$db->prefix}ships WRITE, {$db->prefix}universe WRITE, {$db->prefix}zones READ, {$db->prefix}planets READ, {$db->prefix}news WRITE, {$db->prefix}logs WRITE");
    DbOp::dbResult ($db, $resa, __LINE__, __FILE__);
    $resultt = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id = ?;", array ($ship_id));
    DbOp::dbResult ($db, $resultt, __LINE__, __FILE__);
    $targetinfo = $resultt->fields;

    // Verify not attacking another Xenobe
    // Added because the xenobe were killing each other off
    if (strstr ($targetinfo['email'], '@xenobe'))                       // He's a xenobe
    {
        $resb = $db->Execute ("UNLOCK TABLES");
        DbOp::dbResult ($db, $resb, __LINE__, __FILE__);
        return;
    }

    // Verify sector allows attack
    $sectres = $db->Execute ("SELECT sector_id,zone_id FROM {$db->prefix}universe WHERE sector_id = ?;", array ($targetinfo['sector']));
    DbOp::dbResult ($db, $sectres, __LINE__, __FILE__);
    $sectrow = $sectres->fields;
    $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM {$db->prefix}zones WHERE zone_id = ?;", array ($sectrow['zone_id']));
    DbOp::dbResult ($db, $zoneres, __LINE__, __FILE__);
    $zonerow = $zoneres->fields;
    if ($zonerow['allow_attack'] == "N")                        //  Dest link must allow attacking
    {
        PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "Attack failed, you are in a sector that prohibits attacks.");
        return;
    }

    // Use emergency warp device
    if ($targetinfo['dev_emerwarp'] > 0)
    {
        PlayerLog::writeLog ($db, $targetinfo['ship_id'], LOG_ATTACK_EWD, "Xenobe $playerinfo[character_name]");
        $dest_sector = mt_rand (0, $sector_max);
        $result_warp = $db->Execute ("UPDATE {$db->prefix}ships SET sector = ?, dev_emerwarp = dev_emerwarp - 1 WHERE ship_id = ?;", array ($dest_sector, $targetinfo['ship_id']));
        DbOp::dbResult ($db, $result_warp, __LINE__, __FILE__);
        return;
    }

    // Setup attacker variables
    $attackerbeams = CalcLevels::Beams ($playerinfo['beams'], $level_factor);
    if ($attackerbeams > $playerinfo['ship_energy'])
    {
        $attackerbeams = $playerinfo['ship_energy'];
    }

    $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackerbeams;
    $attackershields = CalcLevels::Shields ($playerinfo['shields'], $level_factor);
    if ($attackershields > $playerinfo['ship_energy'])
    {
        $attackershields = $playerinfo['ship_energy'];
    }

    $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackershields;
    $attackertorps = round (pow ($level_factor, $playerinfo['torp_launchers'])) * 2;
    if ($attackertorps > $playerinfo['torps'])
    {
        $attackertorps = $playerinfo['torps'];
    }

    $playerinfo['torps'] = $playerinfo['torps'] - $attackertorps;
    $attackertorpdamage = $torp_dmg_rate * $attackertorps;
    $attackerarmor = $playerinfo['armor_pts'];
    $attackerfighters = $playerinfo['ship_fighters'];
    $playerdestroyed = 0;

    // Setup target variables
    $targetbeams = CalcLevels::Beams ($targetinfo['beams'], $level_factor);
    if ($targetbeams > $targetinfo['ship_energy'])
    {
        $targetbeams = $targetinfo['ship_energy'];
    }

    $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetbeams;
    $targetshields = CalcLevels::Shields ($targetinfo['shields'], $level_factor);
    if ($targetshields>$targetinfo['ship_energy'])
    {
        $targetshields = $targetinfo['ship_energy'];
    }

    $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetshields;
    $targettorpnum = round (pow ($level_factor, $targetinfo['torp_launchers']))*2;
    if ($targettorpnum > $targetinfo['torps'])
    {
        $targettorpnum = $targetinfo['torps'];
    }

    $targetinfo['torps'] = $targetinfo['torps'] - $targettorpnum;
    $targettorpdmg = $torp_dmg_rate * $targettorpnum;
    $targetarmor = $targetinfo['armor_pts'];
    $targetfighters = $targetinfo['ship_fighters'];
    $targetdestroyed = 0;

    // Begin combat procedures
    if ($attackerbeams > 0 && $targetfighters > 0)                  // Attacker has beams - target has fighters - beams vs. fighters
    {
        if ($attackerbeams > round ($targetfighters / 2))           // Attacker beams GT half target fighters
        {
            $lost = $targetfighters-(round ($targetfighters/2));
            $targetfighters = $targetfighters - $lost;              // T loses half all fighters
            $attackerbeams = $attackerbeams - $lost;                // A loded beams EQ to half T fighters
        }
        else                                                        // Attacker beams LE half target fighters
        {
            $targetfighters = $targetfighters - $attackerbeams;     // T loses fighters EQ to A beams
            $attackerbeams = 0;                                     // A loses all beams
        }
    }

    if ($attackerfighters > 0 && $targetbeams > 0)                      // Target has beams - Attacker has fighters - beams vs. fighters
    {
        if ($targetbeams > round ($attackerfighters / 2))               // Target beams GT half attacker fighters
        {
            $lost = $attackerfighters-(round ($attackerfighters / 2));
            $attackerfighters = $attackerfighters - $lost;               // A loses half of all fighters
            $targetbeams = $targetbeams - $lost;                         // T loses beams EQ to half A fighters
        }
        else
        {                                                                 // Target beams LE half attacker fighters
            $attackerfighters = $attackerfighters - $targetbeams;         // A loses fighters EQ to T beams A loses fighters
            $targetbeams=0;                                               // T loses all beams
        }
    }

    if ($attackerbeams > 0)
    {                                                                   // Attacker has beams left - continue combat - Beams vs. shields
        if ($attackerbeams > $targetshields)                            // Attacker beams GT target shields
        {
            $attackerbeams = $attackerbeams - $targetshields;           // A loses beams EQ to T shields
            $targetshields=0;                                           // T loses all shields
        }
        else
        {                                                               // Attacker beams LE target shields
            $targetshields = $targetshields - $attackerbeams;           // T loses shields EQ to A beams
            $attackerbeams=0;                                           // A loses all beams
        }
    }
    if ($targetbeams > 0)
    {                                                                   // Target has beams left - continue combat - beams VS shields
        if ($targetbeams > $attackershields)
        {                                                               // Target beams GT Attacker shields
            $targetbeams = $targetbeams - $attackershields;             // T loses beams EQ to A shields
            $attackershields = 0;                                       // A loses all shields
        }
        else
        {                                                               // Target beams LE Attacker shields
            $attackershields = $attackershields - $targetbeams;         // A loses shields EQ to T beams
            $targetbeams = 0;                                           // T loses all beams
        }
    }

    if ($attackerbeams > 0)
    {                                                                   // Attacker has beams left - continue combat - beams VS armor
        if ($attackerbeams > $targetarmor)
        {                                                               // Attacker beams GT target armor
            $attackerbeams = $attackerbeams - $targetarmor;             // A loses beams EQ to T armor
            $targetarmor = 0;                                           // T loses all armor (T DESTROYED)
        }
        else
        {                                                               // Attacker beams LE target armor
            $targetarmor = $targetarmor - $attackerbeams;               // T loses armor EQ to A beams
            $attackerbeams = 0;                                         // A loses all beams
        }
    }

    if ($targetbeams > 0)
    {                                                                   // Target has beams left - continue combat - beams VS armor
        if ($targetbeams > $attackerarmor)
        {                                                               // Target beams GT Attacker armor
            $targetbeams = $targetbeams - $attackerarmor;               // T loses beams EQ to A armor
            $attackerarmor = 0;                                         // A loses all armor (A DESTROYED)
        }
        else
        {                                                               // Target beams LE Attacker armor
            $attackerarmor = $attackerarmor - $targetbeams;             // A loses armor EQ to T beams
            $targetbeams = 0;                                           // T loses all beams
        }
    }
    if ($targetfighters > 0 && $attackertorpdamage > 0)
    {                                                                   // Attacker fires torps - target has fighters - torps VS fighters
        if ($attackertorpdamage > round ($targetfighters / 2))
        {                                                               // Attacker fired torps GT half target fighters
            $lost = $targetfighters - (round ($targetfighters / 2));
            $targetfighters = $targetfighters - $lost;                  // T loses half all fighters
            $attackertorpdamage = $attackertorpdamage - $lost;          // A loses fired torps EQ to half T fighters
        }
        else
        {                                                               // Attacker fired torps LE half target fighters
            $targetfighters = $targetfighters - $attackertorpdamage;    // T loses fighters EQ to A torps fired
            $attackertorpdamage = 0;                                    // A loses all torps fired
        }
    }
    if ($attackerfighters > 0 && $targettorpdmg > 0)
    {                                                                   // Target fires torps - Attacker has fighters - torps VS fighters
        if ($targettorpdmg > round ($attackerfighters / 2))
        {                                                               // Target fired torps GT half Attacker fighters
            $lost = $attackerfighters - (round ($attackerfighters / 2));
            $attackerfighters = $attackerfighters - $lost;               // A loses half all fighters
            $targettorpdmg = $targettorpdmg - $lost;                     // T loses fired torps EQ to half A fighters
        }
        else
        {                                                                // Target fired torps LE half Attacker fighters
            $attackerfighters = $attackerfighters - $targettorpdmg;      // A loses fighters EQ to T torps fired
            $targettorpdmg = 0;                                          // T loses all torps fired
        }
    }
    if ($attackertorpdamage > 0)
    {                                                                   // Attacker fires torps - continue combat - torps VS armor
        if ($attackertorpdamage > $targetarmor)
        {                                                               // Attacker fired torps GT half target armor
            $attackertorpdamage = $attackertorpdamage - $targetarmor;   // A loses fired torps EQ to T armor
            $targetarmor=0;                                             // T loses all armor (T DESTROYED)
        }
        else
        {                                                                // Attacker fired torps LE half target armor
            $targetarmor = $targetarmor - $attackertorpdamage;           // T loses armor EQ to A torps fired
            $attackertorpdamage = 0;                                     // A loses all torps fired
        }
    }
    if ($targettorpdmg > 0)
    {                                                                   // Target fires torps - continue combat - torps VS armor
        if ($targettorpdmg > $attackerarmor)
        {                                                               // Target fired torps GT half Attacker armor
            $targettorpdmg = $targettorpdmg - $attackerarmor;           // T loses fired torps EQ to A armor
            $attackerarmor = 0;                                         // A loses all armor (A DESTROYED)
        }
        else
        {                                                               // Target fired torps LE half Attacker armor
            $attackerarmor = $attackerarmor - $targettorpdmg;           // A loses armor EQ to T torps fired
            $targettorpdmg = 0;                                         // T loses all torps fired
        }
    }
    if ($attackerfighters > 0 && $targetfighters > 0)
    {                                                                   // Attacker has fighters - target has fighters - fighters VS fighters
        if ($attackerfighters > $targetfighters)
        {                                                               // Attacker fighters GT target fighters
            $temptargfighters=0;                                        // T will lose all fighters
        }
        else
        {                                                               // Attacker fighters LE target fighters
            $temptargfighters = $targetfighters - $attackerfighters;    // T will lose fighters EQ to A fighters
        }

        if ($targetfighters > $attackerfighters)
        {                                                               // Target fighters GT Attacker fighters
            $tempplayfighters = 0;                                      // A will lose all fighters
        }
        else
        {                                                               // Target fighters LE Attacker fighters
            $tempplayfighters = $attackerfighters - $targetfighters;    // A will lose fighters EQ to T fighters
        }

        $attackerfighters = $tempplayfighters;
        $targetfighters = $temptargfighters;
    }
    if ($attackerfighters > 0)
    {                                                                   // Attacker has fighters - continue combat - fighters VS armor
        if ($attackerfighters > $targetarmor)
        {                                                               // Attacker fighters GT target armor
            $targetarmor=0;                                             // T loses all armor (T DESTROYED)
        }
        else
        {                                                               // Attacker fighters LE target armor
            $targetarmor = $targetarmor - $attackerfighters;            // T loses armor EQ to A fighters
        }
    }
    if ($targetfighters > 0)
    {                                                                   // Target has fighters - continue combat - fighters VS armor
        if ($targetfighters > $attackerarmor)
        {                                                               // Target fighters GT Attacker armor
            $attackerarmor = 0;                                         // A loses all armor (A DESTROYED)
        }
        else
        {                                                               // Target fighters LE Attacker armor
            $attackerarmor = $attackerarmor - $targetfighters;          // A loses armor EQ to T fighters
        }
    }

    // Fix negative value vars
    if ($attackerfighters < 0)
    {
        $attackerfighters = 0;
    }

    if ($attackertorps    < 0)
    {
        $attackertorps = 0;
    }

    if ($attackershields  < 0)
    {
        $attackershields = 0;
    }

    if ($attackerbeams    < 0)
    {
        $attackerbeams = 0;
    }

    if ($attackerarmor    < 0)
    {
        $attackerarmor = 0;
    }

    if ($targetfighters   < 0)
    {
        $targetfighters = 0;
    }

    if ($targettorpnum    < 0)
    {
        $targettorpnum = 0;
    }

    if ($targetshields    < 0)
    {
        $targetshields = 0;
    }

    if ($targetbeams      < 0)
    {
        $targetbeams = 0;
    }

    if ($targetarmor      < 0)
    {
        $targetarmor = 0;
    }

    // Desl with destroyed ships

    // Target ship was destroyed
    if (!$targetarmor > 0)
    {
        if ($targetinfo['dev_escapepod'] == "Y")
        // Target had no escape pod
        {
            $rating=round ($targetinfo['rating'] / 2);
            $resc = $db->Execute ("UPDATE {$db->prefix}ships SET hull = 0, engines = 0, power = 0, computer = 0, sensors = 0, beams = 0, torp_launchers = 0, torps = 0, armor = 0, armor_pts = 100, cloak = 0, shields = 0, sector = 0, ship_ore = 0, ship_organics = 0, ship_energy = 1000, ship_colonists = 0, ship_goods = 0, ship_fighters = 100, ship_damage = 0, on_planet='N', planet_id = 0, dev_warpedit = 0, dev_genesis = 0, dev_beacon = 0, dev_emerwarp = 0, dev_escapepod = 'N', dev_fuelscoop = 'N', dev_minedeflector = 0, ship_destroyed = 'N', rating = ?, dev_lssd='N' WHERE ship_id = ?;", array ($rating, $targetinfo['ship_id']));
            DbOp::dbResult ($db, $resc, __LINE__, __FILE__);
            PlayerLog::writeLog ($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "Xenobe $playerinfo[character_name]|Y");
        }
        else
        // Target had no pod
        {
            PlayerLog::writeLog ($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "Xenobe $playerinfo[character_name]|N");
            BntPlayer::kill ($db, $targetinfo['ship_id'], false, $langvars);
        }

        if ($attackerarmor>0)
        {
            // Attacker still alive to salvage target
            $rating_change=round ($targetinfo['rating'] * $rating_combat_factor);
            $free_ore = round ($targetinfo['ship_ore'] / 2);
            $free_organics = round ($targetinfo['ship_organics'] / 2);
            $free_goods = round ($targetinfo['ship_goods'] / 2);
            $free_holds = CalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
            if ($free_holds > $free_goods)
            {                                                        // Figure out what we can carry
                $salv_goods = $free_goods;
                $free_holds = $free_holds - $free_goods;
            }
            elseif ($free_holds > 0)
            {
                $salv_goods = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_goods = 0;
            }

            if ($free_holds > $free_ore)
            {
                $salv_ore = $free_ore;
                $free_holds = $free_holds - $free_ore;
            }
            elseif ($free_holds > 0)
            {
                $salv_ore = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_ore = 0;
            }

            if ($free_holds > $free_organics)
            {
                $salv_organics = $free_organics;
                $free_holds = $free_holds - $free_organics;
            }
            elseif ($free_holds > 0)
            {
                $salv_organics = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_organics = 0;
            }

            $ship_value = $upgrade_cost*(round (pow ($upgrade_factor, $targetinfo['hull']))+round (pow ($upgrade_factor, $targetinfo['engines']))+round (pow ($upgrade_factor, $targetinfo['power']))+round (pow ($upgrade_factor, $targetinfo['computer']))+round (pow ($upgrade_factor, $targetinfo['sensors']))+round (pow ($upgrade_factor, $targetinfo['beams']))+round (pow ($upgrade_factor, $targetinfo['torp_launchers']))+round (pow ($upgrade_factor, $targetinfo['shields']))+round (pow ($upgrade_factor, $targetinfo['armor']))+round (pow ($upgrade_factor, $targetinfo['cloak'])));
            $ship_salvage_rate = mt_rand (10, 20);
            $ship_salvage = $ship_value * $ship_salvage_rate / 100;
            PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "Attack successful, $targetinfo[character_name] was defeated and salvaged for $ship_salvage credits.");
            $resd = $db->Execute ("UPDATE {$db->prefix}ships SET ship_ore = ship_ore + ?, ship_organics = ship_organics + ?, ship_goods = ship_goods + ?, credits = credits + ? WHERE ship_id = ?;", array ($salv_ore, $salv_organics, $salv_goods, $ship_salvage, $playerinfo['ship_id']));
            DbOp::dbResult ($db, $resd, __LINE__, __FILE__);
            $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
            $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
            $energy = $playerinfo['ship_energy'];
            $rese = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy = ?, ship_fighters = ship_fighters - ?, torps = torps - ?, armor_pts = armor_pts - ?, rating = rating - ? WHERE ship_id = ?;", array ($energy, $fighters_lost, $attackertorps, $armor_lost, $rating_change, $playerinfo['ship_id']));
            DbOp::dbResult ($db, $rese, __LINE__, __FILE__);
        }
    }

    // Target and attacker live
    if ($targetarmor > 0 && $attackerarmor > 0)
    {
        $rating_change = round ($targetinfo['rating'] * .1);
        $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
        $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
        $energy = $playerinfo['ship_energy'];
        $target_rating_change = round ($targetinfo['rating'] / 2);
        $target_armor_lost = $targetinfo['armor_pts'] - $targetarmor;
        $target_fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
        $target_energy = $targetinfo['ship_energy'];
        PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "Attack failed, $targetinfo[character_name] survived.");
        PlayerLog::writeLog ($db, $targetinfo['ship_id'], LOG_ATTACK_WIN, "Xenobe $playerinfo[character_name]|$target_armor_lost|$target_fighters_lost");
        $resf = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy = ?, ship_fighters = ship_fighters - ?, torps = torps - ? , armor_pts = armor_pts - ?, rating=rating - ? WHERE ship_id = ?;", array ($energy, $fighters_lost, $attackertorps, $armor_lost, $rating_change, $playerinfo[ship_id]));
        DbOp::dbResult ($db, $resf, __LINE__, __FILE__);
        $resg = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy = ?, ship_fighters = ship_fighters - ?, armor_pts=armor_pts - ?, torps=torps - ?, rating = ? WHERE ship_id = ?;", array ($target_energy, $target_fighters_lost, $target_armor_lost, $targettorpnum, $target_rating_change, $targetinfo['ship_id']));
        DbOp::dbResult ($db, $resg, __LINE__, __FILE__);
    }

    // Attacker ship destroyed
    if (!$attackerarmor > 0)
    {
        PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "$targetinfo[character_name] destroyed your ship!");
        BntPlayer::kill ($db, $playerinfo['ship_id'], false, $langvars);
        $xenobeisdead = 1;
        if ($targetarmor > 0)
        {
            // Target still alive to salvage attacker
            $rating_change = round ($playerinfo['rating'] * $rating_combat_factor);
            $free_ore = round ($playerinfo['ship_ore'] / 2);
            $free_organics = round ($playerinfo['ship_organics'] / 2);
            $free_goods = round ($playerinfo['ship_goods'] / 2);
            $free_holds = CalcLevels::Holds ($targetinfo['hull'], $level_factor) - $targetinfo['ship_ore'] - $targetinfo['ship_organics'] - $targetinfo['ship_goods'] - $targetinfo['ship_colonists'];
            if ($free_holds > $free_goods)
            {                                                        // Figure out what target can carry
                $salv_goods = $free_goods;
                $free_holds = $free_holds - $free_goods;
            }
            elseif ($free_holds > 0)
            {
                $salv_goods = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_goods = 0;
            }

            if ($free_holds > $free_ore)
            {
                $salv_ore = $free_ore;
                $free_holds = $free_holds - $free_ore;
            }
            elseif ($free_holds > 0)
            {
                $salv_ore = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_ore = 0;
            }

            if ($free_holds > $free_organics)
            {
                $salv_organics = $free_organics;
                $free_holds = $free_holds - $free_organics;
            }
            elseif ($free_holds > 0)
            {
                $salv_organics = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_organics = 0;
            }

            $ship_value = $upgrade_cost*(round (pow ($upgrade_factor, $playerinfo['hull']))+round (pow ($upgrade_factor, $playerinfo['engines']))+round (pow ($upgrade_factor, $playerinfo['power']))+round (pow ($upgrade_factor, $playerinfo['computer']))+round (pow ($upgrade_factor, $playerinfo['sensors']))+round (pow ($upgrade_factor, $playerinfo['beams']))+round (pow ($upgrade_factor, $playerinfo['torp_launchers']))+round (pow ($upgrade_factor, $playerinfo['shields']))+round (pow ($upgrade_factor, $playerinfo['armor']))+round (pow ($upgrade_factor, $playerinfo['cloak'])));
            $ship_salvage_rate = mt_rand (10, 20);
            $ship_salvage = $ship_value * $ship_salvage_rate / 100;
            PlayerLog::writeLog ($db, $targetinfo['ship_id'], LOG_ATTACK_WIN, "Xenobe $playerinfo[character_name]|$armor_lost|$fighters_lost");
            PlayerLog::writeLog ($db, $targetinfo['ship_id'], LOG_RAW, "You destroyed the Xenobe ship and salvaged $salv_ore units of ore, $salv_organics units of organics, $salv_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.");
            $resh = $db->Execute ("UPDATE {$db->prefix}ships SET ship_ore = ship_ore + ?, ship_organics = ship_organics + ?, ship_goods = ship_goods + ?, credits = credits + ? WHERE ship_id = ?;", array ($salv_ore, $salv_organics, $salv_goods, $ship_salvage, $targetinfo['ship_id']));
            DbOp::dbResult ($db, $resh, __LINE__, __FILE__);
            $armor_lost = $targetinfo['armor_pts'] - $targetarmor;
            $fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
            $energy = $targetinfo['ship_energy'];
            $resi = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy = ? , ship_fighters = ship_fighters - ?, torps = torps - ?, armor_pts = armor_pts - ?, rating=rating - ? WHERE ship_id = ?;", array ($energy, $fighters_lost, $targettorpnum, $armor_lost, $rating_change, $targetinfo['ship_id']));
            DbOp::dbResult ($db, $resi, __LINE__, __FILE__);
        }
    }
    $resj = $db->Execute ("UNLOCK TABLES");
    DbOp::dbResult ($db, $resj, __LINE__, __FILE__);
}
?>
