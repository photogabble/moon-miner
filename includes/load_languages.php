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
// File: includes/load_languages.php

if (strpos ($_SERVER['PHP_SELF'], 'load_languages.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

function load_languages ($db = null, $language = null, $categories = null, &$langvars = null)
{
    // Check if all supplied args are valid, if not return false.
    if (is_null ($db) || is_null ($language) || !is_array ($categories))
    {
        return false;
    }

    global $no_db;

    if ($no_db)
    {
        // Slurp in language variables from the ini file directly
        $ini_file = './languages/' . $language . '.ini.php';
        $ini_keys = parse_ini_file ($ini_file, true);
        foreach ($ini_keys as $config_category => $config_line)
        {
            foreach ($config_line as $config_key => $config_value)
            {
                global $$config_key;
                $$config_key = $config_value;
                $langvars[$config_key] = $config_value;
            }
        }

        return true; // Results were added into array, signal that we were successful.
    }
    else
    {
        // Populate the $langvars array
        foreach ($categories as $category)
        {
            if (!isset ($disable_cache))
            {
                // Disable caching until we have the config preferences in the database
                $disable_cache = true;
            }

            if ($disable_cache)
            {
                // Select from the database and return the value of the language variables requested, but do not use caching
                $result = $db->Execute("SELECT name, value FROM {$db->prefix}languages WHERE category = ? AND section = ?;", array ($category, $language));
            }
            else
            {
                // Do a cached select from the database and return the value of the language variables requested
                $result = $db->CacheExecute(7200, "SELECT name, value FROM {$db->prefix}languages WHERE category = ? AND section = ?;", array ($category, $language));
            }

            db_op_result ($db, $result, __LINE__, __FILE__);

            while ($result && !$result->EOF)
            {
                $row = $result->fields;
                global $$row['name'];
                $$row['name'] = $row['value'];
                $langvars[$row['name']] = $row['value'];
                $result->MoveNext();
            }
        }

        return true; // Results were added into array, signal that we were successful.
    }
}
?>
