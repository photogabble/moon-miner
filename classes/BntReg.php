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
// File: classes/BntReg.php

if (strpos ($_SERVER['PHP_SELF'], 'BntReg.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntReg
{
    public static function init($db, $bntreg)
    {
        // Get the config_values from the DB - This is a pdo operation
        $stmt = "SELECT name,value FROM {$db->prefix}gameconfig";
        $result = $db->query ($stmt);

        BntDb::logDbErrors ($db, $stmt, __LINE__, __FILE__);
/*            $db->inactive = false; // The database is active!
            $pdo_db->inactive = false;
                $no_langs_yet = true;
*/

        if ($result !== false) // If the database is not live, this will give false, and db calls will fail silently
        {
            $big_array = $result->fetchAll();
            BntDb::logDbErrors ($db, "fetchAll from gameconfig", __LINE__, __FILE__);
            if (!empty ($big_array))
            {
                foreach ($big_array as $row)
                {
                    $bntreg->$row['name'] = $row['value'];
                }

                return $bntreg;
            }
            else
            {
                // Slurp in config variables from the ini file directly
                $ini_file = 'config/classic_config.ini.php'; // This is hard-coded for now, but when we get multiple game support, we may need to change this.
                $ini_keys = parse_ini_file ($ini_file, true);
                foreach ($ini_keys as $config_category=>$config_line)
                {
                    foreach ($config_line as $config_key=>$config_value)
                    {
                        $bntreg->$config_key = $config_value;
                    }
                }

                return $bntreg;
            }
        }
        else
        {
            // Slurp in config variables from the ini file directly
            $ini_file = 'config/classic_config.ini.php'; // This is hard-coded for now, but when we get multiple game support, we may need to change this.
            $ini_keys = parse_ini_file ($ini_file, true);
            foreach ($ini_keys as $config_category=>$config_line)
            {
                foreach ($config_line as $config_key=>$config_value)
                {
                    $bntreg->$config_key = $config_value;
                }
            }

            return $bntreg;
        }
    }
}
