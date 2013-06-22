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
// File: classes/BntBigBang.php

if (strpos ($_SERVER['PHP_SELF'], 'BntBigBang.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntBigBang
{
    static function findStep ($current_file)
    {
        $i = 0;
        $bigbang_dir = new DirectoryIterator ('bigbang/');
        foreach ($bigbang_dir as $file_info) // Get a list of the files in the bigbang directory
        {
            // This is to get around the issue of not having DirectoryIterator::getExtension.
            $file_ext = pathinfo ($file_info->getFilename (), PATHINFO_EXTENSION);

            // If it is a PHP file, add it to the list of accepted make galaxy files
            if ($file_info->isFile () && $file_ext == 'php') // If it is a PHP file, add it to the list of accepted make galaxy files
            {
                $i++; // Increment a counter, so we know how many files there are to choose from
                $bigbang_files[$i] = $file_info->getFilename (); // The actual file name
             }
        }

        $bigbang_info['steps'] = $i;
        if ($current_file === false)
        {
            $bigbang_info['current_step'] = array_search ( '0.php', $bigbang_files); // If current file is set to false, just return the search from 0.
        }
        else
        {
            $bigbang_info['current_step'] = array_search ( basename ($current_file), $bigbang_files); // Usual search, from the current step
        }

        if (($bigbang_info['current_step'] + 1) > $i)
        {
            $j = $i;
        }
        else
        {
            $j = $bigbang_info['current_step'] + 1;
        }

        $bigbang_info['next_step'] = array_search ($bigbang_files[$j], $bigbang_files);
        $bigbang_info['files'] = $bigbang_files;
        natsort ($bigbang_info['files']);
        return $bigbang_info;
    }
}
?>
