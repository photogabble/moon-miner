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
include_once './includes/admin_log.php';
include_once './includes/check_login.php';
include_once './includes/db_kill_player.php';
include_once './includes/db_op_result.php';
include_once './includes/load_languages.php';
include_once './includes/log_move.php';
include_once './includes/num_armor.php';
include_once './includes/num_beams.php';
include_once './includes/num_energy.php';
include_once './includes/num_fighters.php';
include_once './includes/num_holds.php';
include_once './includes/num_shields.php';
include_once './includes/num_torpedoes.php';
include_once './includes/player_log.php';
include_once './includes/request_var.php';
include_once './includes/text_gotomain.php'; // This will be eliminated while migrating to templates
include_once './includes/number.php';        // This will be eliminated while migrating to templates

// Adodb handles database abstraction. We also use clob sessions, so that pgsql can be
// supported in the future, and cryptsessions, so the session data itself is encrypted.
require_once $ADOdbpath . "/adodb.inc.php";
include_once $ADOdbpath . "/adodb-perf.inc.php";
include_once $ADOdbpath . "/session/adodb-session2.php";
include_once $ADOdbpath . "/session/adodb-encrypt-mcrypt.php";
include_once $ADOdbpath . "/session/adodb-compress-gzip.php";

require_once './classes/bnt/spl_class_loader.php';
require_once './common.php';
?>
