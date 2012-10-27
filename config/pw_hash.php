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
// File: pw_hash.php
if (strpos ($_SERVER['PHP_SELF'], 'pw_hash.php')) // Prevent direct access to this file
{
    die ('Please do not access this file directly');
}

// Define the hash strength, which for now defaults to 10 (it is a base-2 log iteration count). This is used for password stretching and prevents less-secure portable hashes for older systems.
// We will try to keep our default matching the current bcrypt strength used in the PHP implementation for password_hash, which should land in PHP-5.5
define('HASH_STRENGTH', 10);
?>
