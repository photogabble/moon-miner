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
// File: admin/universe_editor.php

if (strpos ($_SERVER['PHP_SELF'], 'universe_editor.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

echo "<strong>" . $langvars['l_universe_editor'] . "</strong>";
$title = $langvars['l_change_uni_title'];
echo "<br>" . $langvars['l_expand_or_contract'] . "<br>";

if (empty($action))
{
    echo "<form action='admin.php' method='post'>";
    echo $langvars['l_universe_size'] . ": <input type='text' name='radius' value=\"" . $universe_size . "\">";
    echo "<input type='hidden' name='swordfish' value='" . $_POST['swordfish'] . "'>";
    echo "<input type='hidden' name='menu' value='univedit'>";
    echo "<input type='hidden' name='action' value='doexpand'> ";
    echo "<input type='submit' value=\"" . $langvars['l_change_uni_title'] . "\">";
    echo "</form>";
}
elseif ($action == "doexpand")
{
    echo "<br><font size='+2'>" . $langvars['l_universe_update'] . "</font><br>";
    $result = $db->Execute ("SELECT sector_id FROM {$db->prefix}universe ORDER BY sector_id ASC");
    db_op_result ($db, $result, __LINE__, __FILE__);
    while (!$result->EOF)
    {
        $row = $result->fields;
        $distance = mt_rand (1, $radius);
        $resx = $db->Execute("UPDATE {$db->prefix}universe SET distance = ? WHERE sector_id = ?", array ($distance, $row['sector_id']));
        db_op_result ($db, $resx, __LINE__, __FILE__);

        $langvars['l_admin_updated_distance'] = str_replace("[sector]", $row['sector_id'], $langvars['l_admin_updated_distance']);
        $langvars['l_admin_updated_distance'] = str_replace("[distance]", $distance, $langvars['l_admin_updated_distance']);
        echo $langvars['l_admin_updated_distance'] . "<br>";
        $result->MoveNext();
    }
}
?>
