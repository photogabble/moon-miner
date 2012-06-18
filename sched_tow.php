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
// File: sched_tow.php

if (preg_match("/sched_tow.php/i", $_SERVER['PHP_SELF']))
{
    echo "You can not access this file directly!";
    die();
}

echo "<B>ZONES</B><br><br>";
echo "Towing bigger players out of restricted zones...";
$num_to_tow = 0;
do
{
    $res = $db->Execute("SELECT ship_id,character_name,hull,sector,$dbtables[universe].zone_id,max_hull FROM $dbtables[ships],$dbtables[universe],$dbtables[zones] WHERE sector=sector_id AND $dbtables[universe].zone_id=$dbtables[zones].zone_id AND max_hull<>0 AND (($dbtables[ships].hull + $dbtables[ships].engines + $dbtables[ships].computer + $dbtables[ships].beams + $dbtables[ships].torp_launchers + $dbtables[ships].shields + $dbtables[ships].armor)/7) >max_hull AND ship_destroyed='N'");
    if ($res)
    {
        $num_to_tow = $res->RecordCount();
        echo "<br>$num_to_tow players to tow:<br>";
        while (!$res->EOF)
        {
            $row = $res->fields;
            echo "...towing $row[character_name] out of $row[sector] ...";
            $newsector = rand(0, $sector_max-1);
            echo " to sector $newsector.<br>";
            $query = $db->Execute("UPDATE $dbtables[ships] SET sector=$newsector,cleared_defences=' ' where ship_id=$row[ship_id]");
            playerlog($row[ship_id], LOG_TOW, "$row[sector]|$newsector|$row[max_hull]");
            log_move($row[ship_id],$newsector);
            $res->MoveNext();
        }
    }
    else
    {
        echo "<br>No players to tow.<br>";
    }
} while ($num_to_tow);

echo "<br>";
$multiplier = 0; //no use to run this again
?>
