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
// File: admin/zone_editor.php

if (strpos ($_SERVER['PHP_SELF'], 'zone_editor.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

echo "<strong>" . $langvars['l_admin_zone_editor'] . "</strong>";
echo "<br>";
echo "<form action='admin.php' method='post'>";
if (empty ($zone))
{
    echo "<select size='20' name='zone'>";
    $res = $db->Execute("SELECT zone_id, zone_name FROM {$db->prefix}zones ORDER BY zone_name");
    db_op_result ($db, $res, __LINE__, __FILE__);
    while (!$res->EOF)
    {
        $row=$res->fields;
        echo "<option value='" . $row['zone_id'] . "'>" . $row['zone_name'] . "</option>";
        $res->MoveNext();
    }

    echo "</select>";
    echo "<input type='hidden' name='operation' value='editzone'>";
    echo "&nbsp;<input type='submit' value='" . $langvars['l_edit'] . "'>";
}
else
{
    if ($operation == "editzone")
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}zones WHERE zone_id = ?", array ($zone));
        db_op_result ($db, $res, __LINE__, __FILE__);
        $row = $res->fields;
        echo "<table border=0 cellspacing=0 cellpadding=5>";
        echo "<tr><td>" . $langvars['l_admin_zone_id'] . "</td><td>" . $row['zone_id'] . "</td></tr>";
        echo "<tr><td>" . $langvars['l_ze_name'] . "</td><td><input type='text' name=zone_name value=\"" . $row['zone_name'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_admin_allow_beacon'] . "</td><td><input type=checkbox name=zone_beacon value=on " . checked($row['allow_beacon']) . "></td>";
        echo "<tr><td>" . $langvars['l_admin_allow_attack'] . "</td><td><input type=checkbox name=zone_attack value=on " . checked($row['allow_attack']) . "></td>";
        echo "<tr><td>" . $langvars['l_admin_allow_warpedit'] . "</td><td><input type=checkbox name=zone_warpedit value=on " . checked($row['allow_warpedit']) . "></td>";
        echo "<tr><td>" . $langvars['l_admin_allow_planet'] . "</td><td><input type=checkbox name=zone_planet value=on " . checked($row['allow_planet']) . "></td>";
        echo "</table>";
        echo "<tr><td>" . $langvars['l_admin_max_hull'] . "</td><td><input type='text' name=zone_hull value=\"" . $row['max_hull'] . "\"></td></tr>";
        echo "<br>";
        echo "<input type='hidden' name=zone value=" . $zone . ">";
        echo "<input type='hidden' name=operation value='savezone'>";
        echo "<input type=submit value='save'>";
    }
    elseif ($operation == "savezone")
    {
        // Update database
        $_zone_beacon = empty ($zone_beacon) ? "N" : "Y";
        $_zone_attack = empty ($zone_attack) ? "N" : "Y";
        $_zone_warpedit = empty ($zone_warpedit) ? "N" : "Y";
        $_zone_planet = empty ($zone_planet) ? "N" : "Y";
        $resx = $db->Execute("UPDATE {$db->prefix}zones SET zone_name = ?, allow_beacon = ? , allow_attack= ?  , allow_warpedit = ? , allow_planet = ?, max_hull = ? WHERE zone_id = ?", array($zone_name, $_zone_beacon , $_zone_attack, $_zone_warpedit, $_zone_planet, $zone_hull, $zone));
        db_op_result ($db, $resx, __LINE__, __FILE__);
        echo $langvars['l_admin_changes_saved'] . "<br><br>";
        echo "<input type=submit value=\"" . $langvars['l_admin_return_zone_editor'] . "\">";
        $button_main = false;
    }
    else
    {
        echo $langvars['l_invalid_operation'];
    }
}

echo "<input type='hidden' name=menu value='zone_editor.php'>";
echo "<input type='hidden' name=swordfish value=" . $_POST['swordfish'] . ">";
echo "</form>";
?>
