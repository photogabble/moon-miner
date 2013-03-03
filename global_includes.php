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
include_once './global_defines.php';            // Defines used in a few places

include_once './includes/check_login.php';		// Needs to be refactored and split into several functions
include_once './includes/db_kill_player.php';   // Remove global variables, redo calc_ownership so that it autoloads
include_once './includes/load_languages.php';   // Global variables are a mess in this file
include_once './includes/request_var.php';		// This injects register globals -- eliminate

require './vendor/autoload.php';				// Load the auto-loader
require_once './common.php';					// Loads the boot-strap messy code for each page
?>
