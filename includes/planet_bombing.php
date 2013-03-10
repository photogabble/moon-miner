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
// File: includes/planet_bombing.php

if (strpos ($_SERVER['PHP_SELF'], 'planet_bombing.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function planet_bombing ($db)
{
    global $playerinfo, $ownerinfo, $planetinfo, $planetbeams, $planetfighters, $attackerfighters;
    global $planettorps, $torp_dmg_rate, $l_cmb_atleastoneturn;
    global $l_bombsaway, $l_bigfigs, $l_bigbeams, $l_bigtorps, $l_strafesuccess;

    if ($playerinfo['turns'] < 1)
    {
        echo $l_cmb_atleastoneturn . "<br><br>";
        BntText::gotoMain ($langvars);
        include_once './footer.php';
        die ();
    }

    echo $l_bombsaway . "<br><br>\n";
    $attackerfighterslost = 0;
    $planetfighterslost = 0;
    $attackerfightercapacity = CalcLevels::Fighters ($playerinfo['computer'], $level_factor);
    $ownerfightercapacity = CalcLevels::Fighters ($ownerinfo['computer'], $level_factor);
    $beamsused = 0;

    $res = $db->Execute ("LOCK TABLES {$db->prefix}ships WRITE, {$db->prefix}planets WRITE");
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);

    include_once './includes/calc_planet_torps.php';
    $planettorps = calc_planet_torps ($db, $ownerinfo, $planetinfo, $base_defense, $level_factor);
    $planetbeams = CalcLevels::planetBeams ($db, $ownerinfo, $base_defense, $planetinfo);

    $planetfighters = $planetinfo['fighters'];
    $attackerfighters = $playerinfo['ship_fighters'];

    if ($ownerfightercapacity / $attackerfightercapacity < 1)
    {
        echo $l_bigfigs . "<br><br>\n";
    }

    if ($planetbeams <= $attackerfighters)
    {
        $attackerfighterslost = $planetbeams;
        $beamsused = $planetbeams;
    }
    else
    {
        $attackerfighterslost = $attackerfighters;
        $beamsused = $attackerfighters;
    }

    if ($attackerfighters <= $attackerfighterslost)
    {
        echo $l_bigbeams . "<br>\n";
    }
    else
    {
        $attackerfighterslost += $planettorps * $torp_dmg_rate;

        if ($attackerfighters <= $attackerfighterslost)
        {
            echo $l_bigtorps . "<br>\n";
        }
        else
        {
            echo $l_strafesuccess . "<br>\n";
            if ($ownerfightercapacity / $attackerfightercapacity > 1)
            {
                $planetfighterslost = $attackerfighters - $attackerfighterslost;

            }
            else
            {
                $planetfighterslost = round (($attackerfighters - $attackerfighterslost) * $ownerfightercapacity / $attackerfightercapacity);
            }
            if ($planetfighterslost > $planetfighters)
            {
                $planetfighterslost = $planetfighters;
            }
        }
    }

    echo "<br><br>\n";
    PlayerLog::writeLog ($db, $ownerinfo['ship_id'], LOG_PLANET_BOMBED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]|$beamsused|$planettorps|$planetfighterslost");
    $res = $db->Execute ("UPDATE {$db->prefix}ships SET turns = turns - 1, turns_used = turns_used + 1, ship_fighters = ship_fighters - ? WHERE ship_id = ?", array ($attackerfighters, $playerinfo['ship_id']));
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);
    $res = $db->Execute ("UPDATE {$db->prefix}planets SET energy=energy - ?, fighters=fighters - ?, torps=torps - ? WHERE planet_id = ?", array ($beamsused, $planetfighterslost, $planettorps, $planetinfo['planet_id']));
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);
    $res = $db->Execute ("UNLOCK TABLES");
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);
}
?>
