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
// File: sched_degrade.php

if (preg_match("/sched_degrade.php/i", $_SERVER['PHP_SELF']))
{
    echo "You can not access this file directly!";
    die();
}

echo "<strong>Degrading Sector Fighters with no friendly base</strong><br><br>";
$res = $db->Execute("SELECT * from {$db->prefix}sector_defence where defence_type = 'F'");
while (!$res->EOF)
{
    $row = $res->fields;
    $res3 = $db->Execute ("SELECT * from {$db->prefix}ships where ship_id = $row[ship_id]");
    $sched_playerinfo = $res3->fields;
    $res2 = $db->Execute ("SELECT * from {$db->prefix}planets where (owner = $row[ship_id] or (corp = $sched_playerinfo[team] AND $sched_playerinfo[team] <> 0)) and sector_id = $row[sector_id] and energy > 0");
    if ($res2->EOF)
    {
        $db->Execute ("UPDATE {$db->prefix}sector_defence set quantity = quantity - GREATEST(ROUND(quantity * $defence_degrade_rate),1) where defence_id = $row[defence_id] and quantity > 0");
        $degrade_rate = $defence_degrade_rate * 100;
        playerlog ($db, $row['ship_id'], LOG_DEFENCE_DEGRADE, "$row[sector_id]|$degrade_rate");
    }
    else
    {
        $energy_required = ROUND($row[quantity] * $energy_per_fighter);
        $res4 = $db->Execute ("SELECT IFNULL(SUM(energy),0) as energy_available from {$db->prefix}planets where (owner = $row[ship_id] or (corp = $sched_playerinfo[team] AND $sched_playerinfo[team] <> 0)) and sector_id = $row[sector_id]");
        $planet_energy = $res4->fields;
        $energy_available = $planet_energy[energy_available];
        echo "available $energy_available, required $energy_required.";
        if ($energy_available > $energy_required)
        {
            while (!$res2->EOF)
            {
                $degrade_row = $res2->fields;
                $db->Execute ("UPDATE {$db->prefix}planets set energy = energy - GREATEST(ROUND($energy_required * (energy / $energy_available)),1)  where planet_id = $degrade_row[planet_id] ");
                $res2->MoveNext();
            }
        }
        else
        {
            $db->Execute ("UPDATE {$db->prefix}sector_defence set quantity = quantity - GREATEST(ROUND(quantity * $defence_degrade_rate),1) where defence_id = $row[defence_id] ");
            $degrade_rate = $defence_degrade_rate * 100;
            playerlog ($db, $row['ship_id'], LOG_DEFENCE_DEGRADE, "$row[sector_id]|$degrade_rate");
        }
    }
    $res->MoveNext();
}
$db->Execute("DELETE from {$db->prefix}sector_defence where quantity <= 0");
?>
