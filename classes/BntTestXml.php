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
// File: classes/BntTestXml.php

if (strpos ($_SERVER['PHP_SELF'], 'BntTestXml.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntTestXml
{
    static function parse ($filename)
    {
        if (file_exists ($filename))
        {
            libxml_use_internal_errors (true);
            $xml = simplexml_load_file ($filename);
            if (!$xml)
            {
                $message = "Failed to open text.xml. ";
                $errors = libxml_get_errors();
                foreach ($errors as $error)
                {
                    $message .= "\t" . $error->message . " ";
                }
            }
            else
            {
                $message = true;
            }
            libxml_use_internal_errors (false);
        }
        else
        {
            $message = 'File not found';
        }

        return $message;
    }
}
?>
