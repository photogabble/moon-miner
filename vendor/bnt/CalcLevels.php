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
// File: vendor/bnt/CalcLevels.php
namespace bnt;

if (strpos ($_SERVER['PHP_SELF'], 'CalcLevels.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
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
}
?>
