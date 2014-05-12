<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: planet_report.php

include './global_includes.php';

BntLogin::checkLogin ($db, $pdo_db, $lang, $langvars, $bntreg, $template);

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('main', 'planet', 'port', 'common', 'global_includes', 'global_funcs', 'footer', 'planet_report', 'regional'));
$title = $langvars['l_pr_title'];
include './header.php';

$body_class = 'bnt';
echo "<body class=" . $body_class . "><br>";

$preptype = null;
if (array_key_exists ('preptype', $_GET) == true) // !isset ($_GET['preptype']))
{
    $preptype = $_GET['preptype'];
}

// Get data about planets
$res = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;

// Determine what type of report is displayed and display it's title
if ($preptype == 1 || !isset ($preptype)) // Display the commodities on the planets
{
    $title = $title .": Status";
    echo "<h1>" . $title . "</h1>\n";
    BadPlanetReport::standardReport ($db, $langvars, $playerinfo);
}
elseif ($preptype == 2)                  // Display the production values of your planets and allow changing
{
    $title = $title .": Production";
    echo "<h1>" . $title . "</h1>\n";
    BadPlanetReport::planetProductionChange ($db, $langvars, $playerinfo);
}
elseif ($preptype == 0)                  // For typing in manually to get a report menu
{
    $title = $title . ": Menu";
    echo "<h1>" . $title . "</h1>\n";
    BadPlanetReport::planetReportMenu ($playerinfo);
}
else                                  // Display the menu if no valid options are passed in
{
    $title = $title . ": Status";
    echo "<h1>" . $title . "</h1>\n";
    BadPlanetReport::planetReport ();
}

echo "<br><br>";
BntText::gotoMain ($db, $lang, $langvars);
BadFooter::display($pdo_db, $lang, $bntreg, $template);
?>
