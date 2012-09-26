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

// Clear variables array before use, and set array with all used variables in page
$variables = null;
if (!isset ($_POST['operation']))
{
    $_POST['operation'] = '';
}

if (empty ($_POST['user']))
{
    $res = $db->Execute("SELECT ship_id, character_name FROM {$db->prefix}ships ORDER BY character_name");
    db_op_result ($db, $res, __LINE__, __FILE__);
    while (!$res->EOF)
    {
        $players[]=$res->fields;
        $res->MoveNext();
    }
    $variables['user'] = '';
    $variables['players'] = $players;
}
else
{
    if ($_POST['operation'] == '')
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=?;", array ($_POST['user']));
        db_op_result ($db, $res, __LINE__, __FILE__);
        $row = $res->fields;
        $variables['operation'] = $_POST['operation'];
        $variables['user'] = $_POST['user'];
        $variables['character_name'] = $row['character_name'];
        $variables['password'] = $row['password'];
        $variables['email'] = $row['email'];
        $variables['ship_name'] = $row['ship_name'];
        $variables['hull'] = $row['hull'];
        $variables['engines'] = $row['engines'];
        $variables['power'] = $row['power'];
        $variables['computer'] = $row['computer'];
        $variables['sensors'] = $row['sensors'];
        $variables['beams'] = $row['beams'];
        $variables['armor'] = $row['armor'];
        $variables['shields'] = $row['shields'];
        $variables['torp_launchers'] = $row['torp_launchers'];
        $variables['cloak'] = $row['cloak'];
        $variables['ship_ore'] = $row['ship_ore'];
        $variables['ship_organics'] = $row['ship_organics'];
        $variables['ship_goods'] = $row['ship_goods'];
        $variables['ship_energy'] = $row['ship_energy'];
        $variables['ship_colonists'] = $row['ship_colonists'];
        $variables['ship_fighters'] = $row['ship_fighters'];
        $variables['torps'] = $row['torps'];
        $variables['armor_pts'] = $row['armor_pts'];
        $variables['dev_beacon'] = $row['dev_beacon'];
        $variables['dev_warpedit'] = $row['dev_warpedit'];
        $variables['dev_genesis'] = $row['dev_genesis'];
        $variables['dev_minedeflector'] = $row['dev_minedeflector'];
        $variables['credits'] = $row['credits'];
        $variables['turns'] = $row['turns'];
        $variables['sector'] = $row['sector'];

        // For checkboxes, switch out the database stored value of Y/N for the html checked="checked", so the checkbox actually is checked.
        $variables['dev_emerwarp'] = '';
        if ($row['dev_emerwarp'] == 'Y')
        {
            $variables['dev_emerwarp'] = 'checked="checked"';
        }

        $variables['dev_escapepod'] = '';
        if ($row['dev_escapepod'] == 'Y')
        {
            $variables['dev_escapepod'] = 'checked="checked"';
        }

        $variables['dev_fuelscoop'] = '';
        if ($row['dev_fuelscoop'] == 'Y')
        {
            $variables['dev_fuelscoop'] = 'checked="checked"';
        }

        $variables['ship_destroyed'] = '';
        if ($row['ship_destroyed'] == 'Y')
        {
            $variables['ship_destroyed'] = 'checked="checked"';
        }
    }
    elseif ($_POST['operation'] == 'save')
    {
        // update database
        $_ship_destroyed = empty ($_POST['ship_destroyed']) ? "N" : "Y";
        $_dev_escapepod = empty ($_POST['dev_escapepod']) ? "N" : "Y";
        $_dev_fuelscoop = empty ($_POST['dev_fuelscoop']) ? "N" : "Y";
        $variables['debug'] = $_dev_escapepod;
        $resx = $db->Execute("UPDATE {$db->prefix}ships SET character_name=?, password=?, email=?, ship_name=?, ship_destroyed=?, hull=?, engines=?, power=?, computer=?, sensors=?, armor=?, shields=?, beams=?, torp_launchers=?, cloak=?, credits=?, turns=?, dev_warpedit=?, dev_genesis=?, dev_beacon=?, dev_emerwarp=?, dev_escapepod=?, dev_fuelscoop=?, dev_minedeflector=?, sector=?, ship_ore=?, ship_organics=?, ship_goods=?, ship_energy=?, ship_colonists=?, ship_fighters=?, torps=?, armor_pts=? WHERE ship_id=?", array ($character_name, $password2, $email, $ship_name, $_ship_destroyed, $hull, $engines, $power, $computer, $sensors, $armor, $shields, $beams, $torp_launchers, $cloak, $credits, $turns, $dev_warpedit, $dev_genesis, $dev_beacon, $dev_emerwarp, $_dev_escapepod, $_dev_fuelscoop, $dev_minedeflector, $sector, $ship_ore, $ship_organics, $ship_goods, $ship_energy, $ship_colonists, $ship_fighters, $torps, $armor_pts, $_POST['user']));
        db_op_result ($db, $resx, __LINE__, __FILE__);
        $button_main = false;
        $variables['user'] = $_POST['user'];
    }
}

$variables['lang'] = $lang;
$variables['swordfish'] = $swordfish;
$variables['operation'] = $_POST['operation'];

// Set the module name.
$variables['module'] = $module_name;

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvar";

$template->AddVariables('langvars', $langvars);
$template->AddVariables('variables', $variables);
?>
