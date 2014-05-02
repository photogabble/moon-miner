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
// This class handles direct file functions for BNT. Included is iniToDb, a function
// for importing values from an INI file into the database.

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
        // We need a way to deal with errors in parse_ini_file here #fixit #todo

        $status_array = array();
        $j = 0;
        $start_tran_res = $db->beginTransaction (); // We enclose the inserts in a transaction as it is roughly 30 times faster
        BntDb::logDbErrors ($db, $start_tran_res, __LINE__, __FILE__);

        $insert_sql = "INSERT into {$db->prefix}$ini_table (name, category, value, section) VALUES (:config_key, :config_category, :config_value, :section)";
        $stmt = $db->prepare ($insert_sql);
        foreach ($ini_keys as $config_category => $config_line)
        {
            foreach ($config_line as $config_key => $config_value)
            {
                if (strpos ($ini_file, 'configset') !== false)
                {
                    // Import all the variables into the registry
                    $bntreg->$config_key = $config_value;
                }

                $j++;
                $stmt->bindParam (':config_key', $config_key);
                $stmt->bindParam (':config_category', $config_category);
                $stmt->bindParam (':config_value', $config_value);
                $stmt->bindParam (':section', $section);
                $result = $stmt->execute ();
                $status_array[$j] = BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);
            }
        }

        for ($k = 1; $k < $j; $k++)
        {
            // Status Array will continue the results of individual executes. It should be === true unless something went horribly wrong.
            if ($status_array[$k] !== true)
            {
                $final_result = false;
            }
            else
            {
                $final_result = true;
            }
        }

        if ($final_result !== true) // If the final result is not true, rollback our transaction, and report "FALSE"
        {
            $rollback_status = $db->rollBack();
            BntDb::logDbErrors ($db, "Rollback transaction on BntFile::initodb", __LINE__, __FILE__);

            return false;
        }
        else // Else we process the transaction, and report "TRUE"
        {
            $trans_status = $db->commit(); // Complete the transaction
            BntDb::logDbErrors ($db, "Complete transaction on BntFile::initodb", __LINE__, __FILE__);

            return true;
        }
    }
}
?>
