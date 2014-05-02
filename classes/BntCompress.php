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
// File: classes/BntCompress.php
//

if (strpos ($_SERVER['PHP_SELF'], 'BntCompress.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntCompress
{
    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    public static function compress($output)
    {

        // Check to see if we have data, if not, then return null
        if (is_null ($output))
        {
            return null;
        }

        // Handle the supported compressions.
        $supported_enc = array ();
        if (isset ($_SERVER['HTTP_ACCEPT_ENCODING']))
        {
            $supported_enc = explode (",", $_SERVER['HTTP_ACCEPT_ENCODING']);
        }

        if (in_array ("gzip", $supported_enc) === true)
        {
            header ('Vary: Accept-Encoding');
            header ('Content-Encoding: gzip');
            header ("DEBUG: gzip found");

            return gzencode ($output, 9);
        }
        elseif (in_array ("deflate", $supported_enc) === true)
        {
            header ('Vary: Accept-Encoding');
            header ('Content-Encoding: deflate');
            header ("DEBUG: deflate found");

            return gzdeflate ($output, 9);
        }
        else
        {
            header ("DEBUG: None found");

            return $output;
        }
    }
}
?>
