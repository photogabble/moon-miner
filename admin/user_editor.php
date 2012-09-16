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
// File: admin/user_editor.php

if (strpos ($_SERVER['PHP_SELF'], 'user_editor.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

$button_main = true;
echo "<strong>" . $langvars['l_admin_user_editor'] . "</strong>";
echo "<br>";
echo "<form action='admin.php' method='post'>";
if (empty ($user))
{
    echo "<select size='20' name='user'>";
    $res = $db->Execute("SELECT ship_id,character_name FROM {$db->prefix}ships ORDER BY character_name");
    db_op_result ($db, $res, __LINE__, __FILE__);
    while (!$res->EOF)
    {
        $row=$res->fields;
        echo "<option value='" . $row['ship_id'] . "'>" . $row['character_name'] . "</option>";
        $res->MoveNext();
    }
    echo "</select>";
    echo "&nbsp;<input type='submit' value='" . $langvars['l_edit'] . "'>";
}
else
{
    if (empty ($operation))
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array ($user));
        db_op_result ($db, $res, __LINE__, __FILE__);
        $row = $res->fields;
        echo "<table border='0' cellspacing='0' cellpadding='5'>";
        echo "<tr><td>" . $langvars['l_admin_player_name'] . "</td><td><input type='text' name='character_name' value=\"" . $row['character_name'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_admin_password'] . "</td><td><input type='text' name='password2' value=\"" . $row['password'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_admin_email'] . "</td><td><input type='email' name='email' placeholder='admin@example.com' value=\"" . $row['email'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_admin_user_id'] . "</td><td>" . $user . "</td></tr>";
        echo "<tr><td>" . $langvars['l_ship'] . "</td><td><input type='text' name='ship_name' value=\"" . $row['ship_name'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_admin_destroyed'] . "</td><td><input type='checkbox' name='ship_destroyed' value='ON' " . checked($row['ship_destroyed']) . "></td></tr>";
        echo "<tr><td>" . $langvars['l_admin_levels'] . "</td>";
        echo "<td><table border='0' cellspacing='0' cellpadding='5'>";
        echo "<tr><td>" . $langvars['l_hull'] . "</td><td><input type='text' size='5' name='hull' value=\"" . $row['hull'] . "\"></td>";
        echo "<td>" . $langvars['l_engines'] . "</td><td><input type='text' size='5' name='engines' value=\"" . $row['engines'] . "\"></td>";
        echo "<td>" . $langvars['l_power'] . "</td><td><input type='text' size='5' name='power' value=\"" . $row['power'] . "\"></td>";
        echo "<td>" . $langvars['l_computer'] . "</td><td><input type='text' size='5' name='computer' value=\"" . $row['computer'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_sensors'] . "</td><td><input type='text' size='5' name='sensors' value=\"" . $row['sensors'] . "\"></td>";
        echo "<td>" . $langvars['l_armor'] . "</td><td><input type='text' size='5' name='armor' value=\"" . $row['armor'] . "\"></td>";
        echo "<td>" . $langvars['l_shields'] . "</td><td><input type='text' size='5' name='shields' value=\"" . $row['shields'] . "\"></td>";
        echo "<td>" . $langvars['l_beams'] . "</td><td><input type='text' size='5' name='beams' value=\"" . $row['beams'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_torps'] . "</td><td><input type='text' size='5' name='torp_launchers' value=\"" . $row['torp_launchers'] . "\"></td>";
        echo "<td>" . $langvars['l_cloak'] . "</td><td><input type='text' size='5' name='cloak' value=\"" . $row['cloak'] . "\"></td></tr>";
        echo "</table></td></tr>";
        echo "<tr><td>" . $langvars['l_holds'] . "</td>";
        echo "<td><table border='0' cellspacing='0' cellpadding='5'>";
        echo "<tr><td>" . $langvars['l_ore'] . "</td><td><input type='text' size='8' name='ship_ore' value=\"" . $row['ship_ore'] . "\"></td>";
        echo "<td>" . $langvars['l_organics'] . "</td><td><input type='text' size='8' name='ship_organics' value=\"" . $row['ship_organics'] . "\"></td>";
        echo "<td>" . $langvars['l_goods'] . "</td><td><input type='text' size='8' name='ship_goods' value=\"" . $row['ship_goods'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_energy'] . "</td><td><input type='text' size='8' name='ship_energy' value=\"" . $row['ship_energy'] . "\"></td>";
        echo "<td>" . $langvars['l_colonists'] . "</td><td><input type='text' size='8' name='ship_colonists' value=\"" . $row['ship_colonists'] . "\"></td></tr>";
        echo "</table></td></tr>";
        echo "<tr><td>" . $langvars['l_admin_combat'] . "</td>";
        echo "<td><table border='0' cellspacing='0' cellpadding='5'>";
        echo "<tr><td>" . $langvars['l_fighters'] . "</td><td><input type='text' size='8' name='ship_fighters' value=\"" . $row['ship_fighters'] . "\"></td>";
        echo "<td>" . $langvars['l_torps'] . "</td><td><input type='text' size='8' name='torps' value=\"" . $row['torps'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_armorpts'] . "</td><td><input type='text' size='8' name='armor_pts' value=\"" . $row['armor_pts'] . "\"></td></tr>";
        echo "</table></td></tr>";
        echo "<tr><td>" . $langvars['l_devices'] . "</td>";
        echo "<td><table border='0' cellspacing='0' cellpadding='5'>";
        echo "<tr><td>" . $langvars['l_admin_beacons'] . "</td><td><input type='text' size='5' name='dev_beacon' value=\"" . $row['dev_beacon'] . "\"></td>";
        echo "<td>" . $langvars['l_warpedit'] . "</td><td><input type='text' size='5' name='dev_warpedit' value=\"" . $row['dev_warpedit'] . "\"></td>";
        echo "<td>" . $langvars['l_genesis'] . "</td><td><input type='text' size='5' name='dev_genesis' value=\"" . $row['dev_genesis'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_deflect'] . "</td><td><input type='text' size='5' name='dev_minedeflector' value=\"" . $row['dev_minedeflector'] . "\"></td>";
        echo "<td>" . $langvars['l_ewd'] . "</td><td><input type='text' size='5' name='dev_emerwarp' value=\"" . $row['dev_emerwarp'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_escape_pod'] . "</td><td><input type='checkbox' name='dev_escapepod' value='ON' " . checked($row['dev_escapepod']) . "></td>";
        echo "<td>" . $langvars['l_fuel_scoop'] . "</td><td><input type='checkbox' name='dev_fuelscoop' value='ON' " . checked($row['dev_fuelscoop']) . "></td></tr>";
        echo "</table></td></tr>";
        echo "<tr><td>" . $langvars['l_credits'] . "</td><td><input type='text' name='credits' value=\"" . $row['credits'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_turns'] . "</td><td><input type='text' name='turns' value=\"" . $row['turns'] . "\"></td></tr>";
        echo "<tr><td>" . $langvars['l_admin_current_sector'] . "</td><td><input type='text' name='sector' value=\"" . $row['sector'] . "\"></td></tr>";
        echo "</table>";
        echo "<br>";
        echo "<input type='hidden' name='user' value='$user'>";
        echo "<input type='hidden' name='operation' value='save'>";
        echo "<input type='submit' value='" . $langvars['l_save'] . "'>";
    }
    elseif ($operation == "save")
    {
        // update database
        $_ship_destroyed = empty ($ship_destroyed) ? "N" : "Y";
        $_dev_escapepod = empty ($dev_escapepod) ? "N" : "Y";
        $_dev_fuelscoop = empty ($dev_fuelscoop) ? "N" : "Y";
        $resx = $db->Execute("UPDATE {$db->prefix}ships SET character_name=?, password=?, email=?, ship_name=?, ship_destroyed=?, hull=?, engines=?, power=?, computer=?, sensors=?, armor=?, shields=?, beams=?, torp_launchers=?, cloak=?, credits=?, turns=?, dev_warpedit=?, dev_genesis=?, dev_beacon=?, dev_emerwarp=?, dev_escapepod=?, dev_fuelscoop=?, dev_minedeflector=?, sector=?, ship_ore=?, ship_organics=?, ship_goods=?, ship_energy=?, ship_colonists=?, ship_fighters=?, torps=?, armor_pts=? WHERE ship_id=?", array ($character_name, $password2, $email, $ship_name, $_ship_destroyed, $hull, $engines, $power, $computer, $sensors, $armor, $shields, $beams, $torp_launchers, $cloak, $credits, $turns, $dev_warpedit, $dev_genesis, $dev_beacon, $dev_emerwarp, $_dev_escapepod, $_dev_fuelscoop, $dev_minedeflector, $sector, $ship_ore, $ship_organics, $ship_goods, $ship_energy, $ship_colonists, $ship_fighters, $torps, $armor_pts, $user));

        db_op_result ($db, $resx, __LINE__, __FILE__);
        echo $langvars['l_admin_changes_saved'] . "<br><br>";
        echo "<input type='submit' value=\"" . $langvars['l_admin_return_user_editor'] . "\">";
        $button_main = false;
    }
    else
    {
        echo $langvars['l_admin_invalid_operation'];
    }
}
echo "<input type='hidden' name='menu' value='user_editor.php'>";
echo "<input type='hidden' name='swordfish' value='" . $_POST['swordfish'] . "'>";
echo "</form>";
?>
