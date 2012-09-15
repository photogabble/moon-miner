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
// File: admin/log_viewer.php

if (strpos ($_SERVER['PHP_SELF'], 'log_viewer.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

echo "<form action=log.php method=post>" .
     "<input type='hidden' name=swordfish value=" . $_POST['swordfish'] . ">" .
     "<input type='hidden' name=player value=0>" .
     "<input type=submit value=\"" . $langvars['l_admin_view_admin_log'] . "\">" .
     "</form>" .
     "<form action=log.php method=post>" .
     "<input type='hidden' name=swordfish value=" . $_POST['swordfish'] . ">" .
     "<select name=player>";

$res = $db->Execute("SELECT ship_id, character_name FROM {$db->prefix}ships ORDER BY character_name ASC");
db_op_result ($db, $res, __LINE__, __FILE__);
while (!$res->EOF)
{
    $players[] = $res->fields;
    $res->MoveNext();
}

foreach ($players as $player)
{
    echo "<option value=" . $player['ship_id'] . ">" . $player['character_name'] . "</option>";
}

echo "</select>&nbsp;&nbsp;" .
     "<input type=submit value=\"" . $langvars['l_admin_view_player_log'] . "\">" .
     "</form><hr size=1 width=80%>";
?>
