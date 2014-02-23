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
// File: classes/BntTranslate.php

if (strpos ($_SERVER['PHP_SELF'], 'BntTranslate.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntTranslate
{
    private static $langvars = array ();

    static function load ($db = null, $language = null, $categories = null)
    {
        // Check if all supplied args are valid, if not return false.
        if (is_null ($db) || is_null ($language) || !is_array ($categories))
        {
            return false;
        }

        if ($db->inactive)
        {
            // Slurp in language variables from the ini file directly
            $ini_file = './languages/' . $language . '.ini.php';
            $ini_keys = parse_ini_file ($ini_file, true);
            foreach ($ini_keys as $config_category => $config_line)
            {
                foreach ($config_line as $config_key => $config_value)
                {
                    self::$langvars[$config_key] = $config_value;
                }
            }

            return self::$langvars;
        }
        else
        {
            // Populate the $langvars array
            foreach ($categories as $category)
            {
				if ($db instanceof ADODB_mysqli)
				{
	                // Select from the database and return the value of the language variables requested, but do not use caching
                    $final_result = $db->Execute ("SELECT name, value FROM {$db->prefix}languages WHERE category = ? AND section = ?;", array ($category, $language));
	                BntDb::logDbErrors ($db, $final_result, __LINE__, __FILE__);
    	            while ($final_result && !$final_result->EOF)
        	        {
            	        $row = $final_result->fields;
                	    self::$langvars[$row['name']] = $row['value'];
                    	$final_result->MoveNext();
	                }
				}
				else
				{
	                // Select from the database and return the value of the language variables requested, but do not use caching
					$query = "SELECT name, value FROM {$db->prefix}languages WHERE category = :category AND section = :language;";
					$result = $db->prepare ($query);
	                BntDb::logDbErrors ($db, $query, $result, __LINE__, __FILE__);

					$result->bindParam (':category', $category, PDO::PARAM_STR);
					$result->bindParam (':language', $language, PDO::PARAM_STR);
					$final_result = $result->execute ();
	                BntDb::logDbErrors ($db, $query, $final_result, __LINE__, __FILE__);

					while (($row = $result->fetch ()) !== false)
                    {
                	    self::$langvars[$row['name']] = $row['value'];
	                }
				}
            }

            return self::$langvars;
        }
    }
}
?>
