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
// File: includes/db_op_result.php

if (strpos ($_SERVER['PHP_SELF'], 'db_op_result.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function db_op_result ($db, $query, $served_line, $served_page)
{
    if ($db->ErrorMsg() == '')
    {
        return true;
    }
    else
    {
        // Convert the content of PHP_SELF (in case it has been tainted) to the correct html entities
        $safe_script_name = htmlentities ($_SERVER['PHP_SELF']);
        $dberror = "A Database error occurred in " . $served_page .
                   " on line " . ($served_line-1) .
                   " (called from: " . $safe_script_name . ": " . $db->ErrorMsg();
        $dberror = str_replace ("'", "&#39;", $dberror); // Allows the use of apostrophes.
        if ($db->logging)
        {
            \bnt\adminLog::writeLog ($db, LOG_RAW, $dberror);
        }

        return $db->ErrorMsg();
    }
}
?>
