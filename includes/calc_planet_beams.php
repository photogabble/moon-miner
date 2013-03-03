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
// File: includes/calc_planet_beams.php

if (strpos ($_SERVER['PHP_SELF'], 'calc_planet_beams.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function calc_planet_beams ($db, $ownerinfo, $base_defense, $planetinfo)
{
    $base_factor = ($planetinfo['base'] == 'Y') ? $base_defense : 0;

    $planetbeams = \bnt\CalcLevels::Beams ($ownerinfo['beams'] + $base_factor, $level_factor);
    $energy_available = $planetinfo['energy'];

    $res = $db->Execute("SELECT beams FROM {$db->prefix}ships WHERE planet_id = ? AND on_planet = 'Y';", array ($planetinfo['planet_id']));
    \bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
    if ($res instanceof ADORecordSet)
    {
        while (!$res->EOF)
        {
            $planetbeams = $planetbeams + \bnt\CalcLevels::Beams ($res->fields['beams'], $level_factor);
            $res->MoveNext();
        }
    }

    if ($planetbeams > $energy_available)
    {
        $planetbeams = $energy_available;
    }
    $planetinfo['energy'] -= $planetbeams;

    return $planetbeams;
}
?>
