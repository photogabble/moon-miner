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
// File: classes/BntFile.php
//
// Function for importing values from an INI file into the database.

if (strpos ($_SERVER['PHP_SELF'], 'BntFile.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntFile
{
    static function iniToDb ($db, $ini_file, $ini_table, $section, $bntreg)
    {
        // This is a loop, that reads a ini file, of the type variable = value.
        // It will loop thru the list of the ini variables, and push them into the db.
        $ini_keys = parse_ini_file ($ini_file, true);

        $status_array = array();
        $i = 0;
        $resa = $db->StartTrans (); // We enclose the inserts in a transaction as it is roughly 30 times faster
        DbOp::dbResult ($db, $resa, __LINE__, __FILE__);

        foreach ($ini_keys as $config_category => $config_line)
        {
            foreach ($config_line as $config_key => $config_value)
            {
                if (strpos ($ini_file, 'configset') !== false)
                {
                    // Import all the variables into the registry
                    $bntreg->set ($config_key, $config_value);
                }

                // We have to ensure that the language string (config_value) is utf8 encoded before sending to the database
                $config_value = utf8_encode ($config_value);
                $debug_query = $db->Execute ("INSERT into {$db->prefix}$ini_table (name, category, value, section) VALUES (?,?,?,?)", array ($config_key, $config_category, $config_value, $section));
                DbOp::dbResult ($db, $debug_query, __LINE__, __FILE__);
                if ($debug_query === false)
                {
                    $status_array[$i] = $debug_query;
                    $i++;
                }
            }
        }

        $status_array[$i] = $db->CompleteTrans(); // Complete the transaction
        DbOp::dbResult ($db, $status_array[$i], __LINE__, __FILE__);

        if ($status_array[$i] === false)
        {
            return $status_array;
        }
        else
        {
            return true;
        }
    }
}
?>
