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
// File: sched_turns.php

if (strpos ($_SERVER['PHP_SELF'], 'sched_turns.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

echo "<strong>TURNS</strong><br><br>";
echo "Adding turns...";
$resa = $db->Execute("UPDATE {$db->prefix}ships SET turns = LEAST(turns + (? * ?), ?) WHERE turns < ?", array ($turns_per_tick, $multiplier, $max_turns, $max_turns));
db_op_result ($db, $resa, __LINE__, __FILE__);
is_query_ok ($resa);
echo "<br>";
$multiplier = 0;
?>
