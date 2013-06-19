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
// File: bigbang.php

$index_page = true; // Ensure that we do not set sessions
include './global_includes.php';
include './config/admin_pw.php';

// Set timelimit to infinite
set_time_limit (0);

// Get POST Variable "swordfish" and URL Sanitize it. (returns NULL if not found)
$swordfish  = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);

// Get POST Variable "step" and INT Sanitize it. (returns NULL if not found)
$step = (int) filter_input (INPUT_POST, 'step', FILTER_SANITIZE_NUMBER_INT);

if ($swordfish === null) // If no swordfish password has been entered, we are on the first step
{
    $step = "1";
}

if (($swordfish !== null) && (ADMIN_PW != $swordfish)) // If a swordfish password is not null and it does not match (bad pass), redirect to step 1 (default or 0.php)
{
    $variables['goodpass'] = false;
    include_once 'bigbang/0.php';
}
else // If swordfish is set and matches (good pass)
{
    $variables['goodpass'] = true;
    if (isset ($step) && $step != '') // We've got a good pass, and its not step 1
    {
            $bigbang_info = BntBigBang::findStep (false);
            $loader_file = $bigbang_info['files'][$step];
            $filename = 'bigbang/' . $loader_file;
            if (file_exists ($filename))
            {
                include_once ($filename);
            }
    }
}
?>
