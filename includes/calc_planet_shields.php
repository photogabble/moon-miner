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
// File: includes/calc_planet_shields.php

if (strpos ($_SERVER['PHP_SELF'], 'calc_planet_shields.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function calc_planet_shields ($db, $ownerinfo, $base_defense, $planetinfo)
{
    $base_factor = ($planetinfo['base'] == 'Y') ? $base_defense : 0;

    $planetshields = \bnt\CalcLevels::Shields ($ownerinfo['shields'] + $base_factor, $level_factor);
    $energy_available = $planetinfo['energy'];

    $res = $db->Execute ("SELECT shields FROM {$db->prefix}ships WHERE planet_id = ? AND on_planet = 'Y';", array ($planetinfo['planet_id']));
    \bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
    if ($res instanceof ADORecordSet)
    {
        while (!$res->EOF)
        {
            $planetshields += \bnt\CalcLevels::Shields ($res->fields['shields'], $level_factor);
            $res->MoveNext ();
        }
    }

    if ($planetshields > $energy_available)
    {
        $planetshields = $energy_available;
    }
    $planetinfo['energy'] -= $planetshields;

    return $planetshields;
}
?>
