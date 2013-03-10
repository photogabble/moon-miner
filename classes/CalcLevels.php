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
// File: classes/CalcLevels.php

if (strpos ($_SERVER['PHP_SELF'], 'CalcLevels.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class CalcLevels
{
    static function Armor ($level_armor, $level_factor)
    {
        return round (pow ($level_factor, $level_armor) * 100);
    }

    static function Holds ($level_hull, $level_factor)
    {
        return round (pow ($level_factor, $level_hull) * 100);
    }

    static function Shields ($level_shields, $level_factor)
    {
        return round (pow ($level_factor, $level_shields) * 100);
    }

    static function Torpedoes ($level_torp_launchers, $level_factor)
    {
        return round (pow ($level_factor, $level_torp_launchers) * 100);
    }

    static function Beams ($level_beams, $level_factor)
    {
        return round (pow ($level_factor, $level_beams) * 100);
    }

    static function Fighters ($level_computer, $level_factor)
    {
        return round (pow ($level_factor, $level_computer) * 100);
    }

    static function Energy ($level_power, $level_factor)
    {
        return round (pow ($level_factor, $level_power) * 500);
    }

	static function planetBeams ($db, $ownerinfo, $base_defense, $planetinfo)
	{
    	$base_factor = ($planetinfo['base'] == 'Y') ? $base_defense : 0;

	    $planetbeams = CalcLevels::Beams ($ownerinfo['beams'] + $base_factor, $level_factor);
    	$energy_available = $planetinfo['energy'];

	    $res = $db->Execute ("SELECT beams FROM {$db->prefix}ships WHERE planet_id = ? AND on_planet = 'Y';", array ($planetinfo['planet_id']));
    	DbOp::dbResult ($db, $res, __LINE__, __FILE__);
	    if ($res instanceof ADORecordSet)
    	{
        	while (!$res->EOF)
	        {
    	        $planetbeams = $planetbeams + CalcLevels::Beams ($res->fields['beams'], $level_factor);
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

	static function planetShields ($db, $ownerinfo, $base_defense, $planetinfo)
	{
    	$base_factor = ($planetinfo['base'] == 'Y') ? $base_defense : 0;
    	$planetshields = CalcLevels::Shields ($ownerinfo['shields'] + $base_factor, $level_factor);
    	$energy_available = $planetinfo['energy'];

    	$res = $db->Execute ("SELECT shields FROM {$db->prefix}ships WHERE planet_id = ? AND on_planet = 'Y';", array ($planetinfo['planet_id']));
	    DbOp::dbResult ($db, $res, __LINE__, __FILE__);
    	if ($res instanceof ADORecordSet)
	    {
    	    while (!$res->EOF)
        	{
            	$planetshields += CalcLevels::Shields ($res->fields['shields'], $level_factor);
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
}
?>
