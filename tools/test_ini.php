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
// File: test_ini.php

// Enable Error Reporting for tests.
ini_set ('error_reporting', E_ALL); // During development, output all errors, even notices
ini_set ('display_errors', '1'); // During development, *display* all errors
get_ini ("config/configset_classic.ini.php", $ini);

echo "<pre>[ini]\n". print_r($ini, true) ."</pre>\n";
echo "END<br />\n";

function get_ini($file, &$out)
{
	$ini = file ($file, FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);

	$container = null;
	foreach ($ini as $line)
	{
		if (substr (trim ($line), 0, 1) === '[' && substr (trim ($line), -1, 1) === ']')
		{
			$container = trim (substr ($line, 1, -1));
			continue;
		}
		elseif (substr (trim ($line), 0, 1) !== ';' && substr (trim ($line), 0, 2) !== '//')
		{
			list ($name, $data) = explode ('=', $line, 2);
			$name = trim ($name);
			$data = trim ($data);
			list ($value, $comment) = explode ('//', $data, 2);

			// Remove any semicolons from the end of the value.
			if (substr (trim ($value), -1,1) === ';')
			{
				$value = substr (trim ($value), 0, -1);
			}

			// Remove Quote Tags from the start and end.
			if (substr (trim ($value), 0, 1) === '\'' || substr (trim ($value), 0, 1) === '"')
			{
				$value = substr (trim ($value), 1);
			}
			if (substr (trim ($value), -1, 1) === '\'' || substr (trim ($value), -1, 1) === '"')
			{
				$value = substr (trim ($value), 0, -1);
			}

			$value = trim ($value);
			$comment = trim ($comment);

			// Check for Numeric types (int/long, double/float)
			if (is_numeric ($value))
			{
				$value +=0;
			}
			elseif (strtolower ($value) === "true" || strtolower ($value) === "false")
			{
				$value = (strtolower ($value) == "true" ? true : false);
				settype ($value, 'bool');
			}
			elseif (is_string ($value))
			{
				if (strlen (trim ($value)) == 0)
				{
					$value = null;
				}
			}
			if (!is_null ($container))
			{
				$out[$container][$name] = array ('value'=>$value, 'type'=>gettype ($value), 'comment'=>$comment);
			}
		}
	}
}
?>
