<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: classes/BntNews.php

// Todo: Add validity checking for the format of $day
if (strpos ($_SERVER['PHP_SELF'], 'BntNews.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntNews
{
    public static function previousDay($day)
    {
        // Convert the formatted date into a timestamp
        $day = strtotime ($day);

        // Subtract one day in seconds from the timestamp
        $day = $day - 86400;

        // Return the final amount formatted as YYYY/MM/DD
        return date ("Y/m/d", $day);
    }

    public static function nextDay($day)
    {
        // Convert the formatted date into a timestamp
        $day = strtotime ($day);

        // Add one day in seconds to the timestamp
        $day = $day + 86400;

        // Return the final amount formatted as YYYY/MM/DD
        return date ("Y/m/d", $day);
    }
}
?>
