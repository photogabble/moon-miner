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
// File: global_includes.php

require_once './config/db_config.php';          // Database variables

require_once './global_defines.php';            // Defines used in a few places
require_once './vendor/adodb/adodb-php/session/adodb-session2.php'; // The latest version of composer now chooses session instead of session2 which breaks everything

require_once './vendor/autoload.php';           // Load the auto-loader
require_once './common.php';                    // Loads the boot-strap messy code for each page
?>
