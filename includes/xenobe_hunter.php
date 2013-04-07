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
// File: includes/xenobe_hunter.php

if (strpos ($_SERVER['PHP_SELF'], 'xenobe_hunter.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function xenobe_hunter ($db)
{
    // Setup general Variables
    global $playerinfo, $targetlink, $xenobeisdead;

    $rescount = $db->Execute ("SELECT COUNT(*) AS num_players FROM {$db->prefix}ships WHERE ship_destroyed='N' AND email NOT LIKE '%@xenobe' AND ship_id > 1");
    DbOp::dbResult ($db, $rescount, __LINE__, __FILE__);
    $rowcount = $rescount->fields;
    $topnum = min (10, $rowcount['num_players']);

    // If we have killed all the players in the game then stop here.
    if ($topnum < 1)
    {
        return;
    }

    $res = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_destroyed='N' AND email NOT LIKE '%@xenobe' AND ship_id > 1 ORDER BY score DESC LIMIT ?", array ($topnum));
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);

    // Choose a target from the top player list
    $i = 1;
    $targetnum = mt_rand (1, $topnum);
    while (!$res->EOF)
    {
        if ($i == $targetnum)
        {
            $targetinfo = $res->fields;
        }
        $i++;
        $res->MoveNext();
    }

    // Make sure we have a target
    if (!$targetinfo)
    {
        PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "Hunt Failed: No Target ");

        return;
    }

    // Jump to target sector
    $sectres = $db->Execute ("SELECT sector_id, zone_id FROM {$db->prefix}universe WHERE sector_id=?", array ($targetinfo['sector']));
    DbOp::dbResult ($db, $sectres, __LINE__, __FILE__);
    $sectrow = $sectres->fields;

    $zoneres = $db->Execute ("SELECT zone_id,allow_attack FROM {$db->prefix}zones WHERE zone_id=?", array ($sectrow['zone_id']));
    DbOp::dbResult ($db, $zoneres, __LINE__, __FILE__);
    $zonerow = $zoneres->fields;

    // Only travel there if we can attack in the target sector
    if ($zonerow['allow_attack'] == "Y")
    {
        $stamp = date ("Y-m-d H-i-s");
        $move_result = $db->Execute ("UPDATE {$db->prefix}ships SET last_login=?, turns_used=turns_used+1, sector=? WHERE ship_id=?", array ($stamp, $targetinfo['sector'], $playerinfo['ship_id']));
        DbOp::dbResult ($db, $move_result, __LINE__, __FILE__);
        PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "Xenobe used a wormhole to warp to sector $targetinfo[sector] where he is hunting player $targetinfo[character_name].");
        if (!$move_result)
        {
            $error = $db->ErrorMsg();
            PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "Move failed with error: $error ");

            return;
        }

        // Check for sector defences
        $resultf = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=? AND defence_type ='F' ORDER BY quantity DESC", array ($targetinfo['sector']));
        DbOp::dbResult ($db, $resultf, __LINE__, __FILE__);
        $i = 0;
        $total_sector_fighters = 0;
        if ($resultf instanceof ADORecordSet)
        {
            while (!$resultf->EOF)
            {
                $defences[$i] = $resultf->fields;
                $total_sector_fighters += $defences[$i]['quantity'];
                $i++;
                $resultf->MoveNext();
            }
        }

        $resultm = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=? AND defence_type ='M'", array ($targetinfo['sector']));
        DbOp::dbResult ($db, $resultm, __LINE__, __FILE__);
        $i = 0;
        $total_sector_mines = 0;
        if ($resultm instanceof ADORecordSet)
        {
            while (!$resultm->EOF)
            {
                $defences[$i] = $resultm->fields;
                $total_sector_mines += $defences[$i]['quantity'];
                $i++;
                $resultm->MoveNext();
            }
        }

        if ($total_sector_fighters > 0 || $total_sector_mines > 0 || ($total_sector_fighters > 0 && $total_sector_mines > 0)) // Destination link has defences
        {
            // Attack sector defences
            $targetlink = $targetinfo['sector'];
            xenobe_to_sec_def ($db, $langvars);
        }

        if ($xenobeisdead > 0)
        {
            return; // Sector defenses killed the Xenobe
        }

        PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "Xenobe launching an attack on $targetinfo[character_name]."); // Attack the target

        if ($targetinfo['planet_id'] > 0) // Is player target on a planet?
        {
            xenobe_to_planet ($db, $targetinfo['planet_id']); // Yes, so move to that planet
        }
        else
        {
            xenobe_to_ship ($db, $targetinfo['ship_id']); // Not on a planet, so move to the ship
        }
    }
    else
    {
        PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "Xenobe hunt failed, target $targetinfo[character_name] was in a no attack zone (sector $targetinfo[sector]).");
    }
}
?>
