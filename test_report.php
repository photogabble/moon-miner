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
// File: report.php

include './global_includes.php';
include_once './includes/get_avg_tech.php';

if (check_login ($db, $lang, $langvars)) // Checks player login, sets playerinfo
{
    die();
}

$langvars = null;

// New database driven language entries
load_languages ($db, $lang, array('main', 'report', 'device', 'common', 'global_includes', 'global_funcs', 'footer'), $langvars);

// Do string replacement for any language strings prior to sending them to the Template system
$langvars['l_global_mmenu'] = str_replace("[here]", "<a href='main.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mmenu']);

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email=?", array ($_SESSION['username']));
db_op_result ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$shiptypes[0] = "tinyship.png";
$shiptypes[1] = "smallship.png";
$shiptypes[2] = "mediumship.png";
$shiptypes[3] = "largeship.png";
$shiptypes[4] = "hugeship.png";

$shipavg = get_avg_tech ($playerinfo, "ship");

if ($shipavg < 8)
{
    $shiplevel = 0;
}
elseif ($shipavg < 12)
{
    $shiplevel = 1;
}
elseif ($shipavg < 16)
{
    $shiplevel = 2;
}
elseif ($shipavg < 20)
{
    $shiplevel = 3;
}
else
{
    $shiplevel = 4;
}

$holds_used = $playerinfo['ship_ore'] + $playerinfo['ship_organics'] + $playerinfo['ship_goods'] + $playerinfo['ship_colonists'];
$holds_max = NUM_HOLDS ($playerinfo['hull']);
$armor_pts_max = NUM_ARMOR ($playerinfo['armor']);
$ship_fighters_max = NUM_FIGHTERS ($playerinfo['computer']);
$torps_max = NUM_TORPEDOES ($playerinfo['torp_launchers']);
$energy_max = NUM_ENERGY($playerinfo['power']);
$escape_pod = ($playerinfo['dev_escapepod'] == 'Y') ? $l_yes : $l_no;
$fuel_scoop = ($playerinfo['dev_fuelscoop'] == 'Y') ? $l_yes : $l_no;
$lssd = ($playerinfo['dev_lssd'] == 'Y') ? $l_yes : $l_no;

// Always make sure we are using empty vars before use.
$variables = null;

// Set array with all used variables in page
$variables['lang'] = $lang;
$variables['color_header'] = $color_header;
$variables['color_line1'] = $color_line1;
$variables['color_line2'] = $color_line2;
$variables['playerinfo_character_name'] = $playerinfo['character_name'];
$variables['playerinfo_ship_name'] = $playerinfo['ship_name'];
$variables['number_playerinfo_credits'] = NUMBER ($playerinfo['credits']);
$variables['playerinfo_hull'] = $playerinfo['hull'];
$variables['playerinfo_engines'] = $playerinfo['engines'];
$variables['playerinfo_computer'] = $playerinfo['computer'];
$variables['playerinfo_sensors'] = $playerinfo['sensors'];
$variables['playerinfo_armor'] = $playerinfo['armor'];
$variables['playerinfo_shields'] = $playerinfo['shields'];
$variables['playerinfo_beams'] = $playerinfo['beams'];
$variables['playerinfo_power'] = $playerinfo['power'];
$variables['playerinfo_torp_launchers'] = $playerinfo['torp_launchers'];
$variables['playerinfo_cloak'] = $playerinfo['cloak'];
$variables['number_shipavg'] = NUMBER ($shipavg, 2);
$variables['number_holds_used'] = NUMBER ($holds_used);
$variables['number_holds_max'] = NUMBER ($holds_max);
$variables['number_playerinfo_ship_ore'] = NUMBER ($playerinfo['ship_ore']);
$variables['number_playerinfo_ship_organics'] = NUMBER ($playerinfo['ship_organics']);
$variables['number_playerinfo_ship_goods'] = NUMBER ($playerinfo['ship_goods']);
$variables['number_playerinfo_ship_energy'] = NUMBER ($playerinfo['ship_energy']);
$variables['number_playerinfo_ship_colonists'] = NUMBER ($playerinfo['ship_colonists']);
$variables['number_playerinfo_ship_fighters'] = NUMBER ($playerinfo['ship_fighters']);
$variables['number_playerinfo_armor_pts'] = NUMBER ($playerinfo['armor_pts']);
$variables['number_playerinfo_torps'] = NUMBER ($playerinfo['torps']);
$variables['number_torps_max'] = NUMBER ($torps_max);
$variables['number_energy_max'] = NUMBER ($energy_max);
$variables['number_armor_pts_max'] = NUMBER ($armor_pts_max);
$variables['number_ship_fighters_max'] = NUMBER ($ship_fighters_max);
$variables['playerinfo_dev_beacon'] = NUMBER ($playerinfo['dev_beacon']);
$variables['playerinfo_dev_warpedit'] = NUMBER ($playerinfo['dev_warpedit']);
$variables['playerinfo_dev_genesis'] = NUMBER ($playerinfo['dev_genesis']);
$variables['playerinfo_dev_minedeflector'] = NUMBER ($playerinfo['dev_minedeflector']);
$variables['playerinfo_dev_emerwarp'] = NUMBER ($playerinfo['dev_emerwarp']);
$variables['escape_pod'] = $escape_pod;
$variables['fuel_scoop'] = $fuel_scoop;
$variables['lssd'] = $lssd;
$variables['ship_img'] = "images/" . $shiptypes[$shiplevel];

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvar";

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables('langvars', $langvars);
$template->AddVariables('variables', $variables);
$template->Display("test_report.tpl");
?>
