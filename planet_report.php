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
// File: planet_report.php

include "config.php";
updatecookie();
include "languages/$lang";
$title = $l_pr_title;
include "header.php";

if (checklogin())
{
    die();
}

$PRepType = '';
if (!isset($_GET['PRepType']))
{
    $_GET['PRepType'] = '';
}

$PRepType = $_GET['PRepType'];

// Get data about planets
$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

// Determine what type of report is displayed and display it's title
if ($PRepType==1 || !isset($PRepType)) // Display the commodities on the planets
{
    $title=$title .": Status";
    bigtitle();
    standard_report();
}
elseif ($PRepType==2)                  // Display the production values of your planets and allow changing
{
    $title=$title .": Production";
    bigtitle();
    planet_production_change();
}
elseif ($PRepType==0)                  // For typing in manually to get a report menu
{
    $title=$title . ": Menu";
    bigtitle();
    planet_report_menu();
}
else                                  // Display the menu if no valid options are passed in
{
    $title=$title . ": Status";
    bigtitle();
    planet_report();
}

// Begin functions
function planet_report_menu()
{
    global $playerinfo;
    global $l_pr_teamlink;

    echo "<div style='width:90%; margin:auto; font-size:14px;'>\n";

    echo "<b><a href=planet_report.php?PRepType=1 NAME=Planet Status>Planet Status</a></b><br>" .
         "Displays the number of each Commodity on the planet (Ore, Organics, Goods, Energy, Colonists, Credits, Fighters, and Torpedoes)<br>" .
         "<br>" .
         "<b><a href=planet_report.php?PRepType=2 NAME=Planet Status>Change Production</a></b> &nbsp;&nbsp; <b>Base Required</b> on Planet<br>" .
         "This Report allows you to change the rate of production of commondits on planets that have a base<br>" .
         "-- You must travel to the planet to build a base set the planet to coporate or change the name (celebrations and such)<br>";

    if ($playerinfo['team']>0)
    {
        echo "<br><b><a href=team_planets.php>$l_pr_teamlink</a></b><br> " .
             "Commondity Report (like Planet Status) for planets marked Corporate by you and/or your fellow team member<br><br>";
    }
    echo "</div>\n";
}

function standard_report()
{
    global $db;
    global $res;
    global $playerinfo;
    global $dbtables;
    global $username;
    global $sort;
    global $query;
    global $color_header, $color, $color_line1, $color_line2;
    global $l_pr_teamlink, $l_pr_clicktosort;
    global $l_sector, $l_name, $l_unnamed, $l_ore, $l_organics, $l_goods, $l_energy, $l_colonists, $l_credits, $l_fighters, $l_torps, $l_base, $l_selling, $l_pr_totals, $l_yes, $l_no;

    echo "<div style='width:90%; margin:auto; font-size:14px;'>\n";


    echo "Planetary report descriptions and <b><a href=planet_report.php?PRepType=0>menu</a></b><br><br>" .
         "<b><a href=planet_report.php?PRepType=2>Change Production</a></b> &nbsp;&nbsp; <b>Base Required</b> on Planet<br>";

    if ($playerinfo['team']>0)
    {
        echo "<br><b><a href=team_planets.php>$l_pr_teamlink</a></b><br> <br>";
    }

    $query = "SELECT * FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id]";

    if (!empty($sort))
    {
        $query .= " ORDER BY";
        if ($sort == "name")
        {
            $query .= " $sort ASC";
        }
        elseif ($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" ||$sort == "colonists" || $sort == "credits" || $sort == "fighters")
        {
            $query .= " $sort DESC, sector_id ASC";
        }
        elseif ($sort == "torp")
        {
            $query .= " torps DESC, sector_id ASC";
        }
        else
        {
            $query .= " sector_id ASC";
        }
    }
    else
    {
        $query .= " ORDER BY sector_id ASC";
    }

    $res = $db->Execute($query);

    $i = 0;
    if ($res)
    {
        while (!$res->EOF)
        {
            $planet[$i] = $res->fields;
            $i++;
            $res->MoveNext();
        }
    }

    global $l_pr_noplanet;
    $num_planets = $i;
    if ($num_planets < 1)
    {
        echo "<br>$l_pr_noplanet";
    }
    else
    {
        echo "<br>";
        echo "<form action=planet_report_ce.php method=post>";

        // Next block of echo's creates the header of the table
        echo "$l_pr_clicktosort<br><br>";
        echo "<b>WARNING:</b> \"Build\" and \"Take Credits\" will cause your ship to move. <br><br>";
        echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
        echo "<TR bgcolor=\"$color_header\" valign=bottom>";
        echo "<td><b><a href=planet_report.php?PRepType=1&sort=sector_id>$l_sector</a></b></td>";
        echo "<td><b><a href=planet_report.php?PRepType=1&sort=name>$l_name</a></b></td>";
        echo "<td><b><a href=planet_report.php?PRepType=1&sort=ore>$l_ore</a></b></td>";
        echo "<td><b><a href=planet_report.php?PRepType=1&sort=organics>$l_organics</a></b></td>";
        echo "<td><b><a href=planet_report.php?PRepType=1&sort=goods>$l_goods</a></b></td>";
        echo "<td><b><a href=planet_report.php?PRepType=1&sort=energy>$l_energy</a></b></td>";
        echo "<td align=center><b><a href=planet_report.php?PRepType=1&sort=colonists>$l_colonists</a></b></td>";
        echo "<td align=center><b><a href=planet_report.php?PRepType=1&sort=credits>$l_credits</a></b></td>";
        echo "<td align=center><b>Take<br>Credits</b></td>";
        echo "<td align=center><b><a href=planet_report.php?PRepType=1&sort=fighters>$l_fighters</a></b></td>";
        echo "<td align=center><b><a href=planet_report.php?PRepType=1&sort=torp>$l_torps</a></b></td>";
        echo "<td align=RIGHT><b>$l_base?</b></td>";
        if ($playerinfo[team] > 0)
        {
            echo "<td align=RIGHT><b>Corp?</b></td>";
        }
        echo "<td align=RIGHT><b>$l_selling?</b></td>";

        // Next block of echo's fils the table and calculates the totals of all the commoditites as well as counting the bases and selling planets
        echo "</TR>";
        $total_organics = 0;
        $total_ore = 0;
        $total_goods = 0;
        $total_energy = 0;
        $total_colonists = 0;
        $total_credits = 0;
        $total_fighters = 0;
        $total_torp = 0;
        $total_base = 0;
        $total_corp = 0;
        $total_selling = 0;
        $color = $color_line1;
        for ($i = 0; $i < $num_planets; $i++)
        {
            $total_organics += $planet[$i][organics];
            $total_ore += $planet[$i][ore];
            $total_goods += $planet[$i][goods];
            $total_energy += $planet[$i][energy];
            $total_colonists += $planet[$i][colonists];
            $total_credits += $planet[$i][credits];
            $total_fighters += $planet[$i][fighters];
            $total_torp += $planet[$i][torps];
            if ($planet[$i][base] == "Y")
            {
                $total_base += 1;
            }
            if ($planet[$i][corp] > 0)
            {
                $total_corp += 1;
            }
            if ($planet[$i][sells] == "Y")
            {
                $total_selling += 1;
            }
            if (empty($planet[$i][name]))
            {
                $planet[$i][name] = $l_unnamed;
            }

            echo "<TR bgcolor=\"$color\">";
            echo "<td><a href=rsmove.php?engage=1&destination=". $planet[$i][sector_id] . ">". $planet[$i][sector_id] ."</a></td>";
            echo "<td>" . $planet[$i][name] . "</td>";
            echo "<td>" . NUMBER($planet[$i][ore]) . "</td>";
            echo "<td>" . NUMBER($planet[$i][organics]) . "</td>";
            echo "<td>" . NUMBER($planet[$i][goods]) . "</td>";
            echo "<td>" . NUMBER($planet[$i][energy]) . "</td>";
            echo "<td align=RIGHT>" . NUMBER($planet[$i][colonists]) . "</td>";
            echo "<td align=RIGHT>" . NUMBER($planet[$i][credits]) . "</td>";
            echo "<td align=center>" . "<INPUT TYPE=CHECKBOX NAME=TPCreds[] VALUE=\"" . $planet[$i]["planet_id"] . "\">" . "</td>";
            echo "<td align=RIGHT>"  . NUMBER($planet[$i][fighters]) . "</td>";
            echo "<td align=RIGHT>"  . NUMBER($planet[$i][torps]) . "</td>";
            echo "<td align=center>" . base_build_check($planet, $i) . "</td>";
            if ($playerinfo[team] > 0)
            {
                echo "<td align=center>" . ($planet[$i][corp] > 0  ? "$l_yes" : "$l_no") . "</td>";
            }

            echo "<td align=center>" . ($planet[$i][sells] == 'Y' ? "$l_yes" : "$l_no") . "</td>";
            echo "</TR>";

            if ($color == $color_line1)
            {
                $color = $color_line2;
            }
            else
            {
                $color = $color_line1;
            }
        }

        // the next block displays the totals
        echo "<TR bgcolor=$color>";
        echo "<td COLSPAN=2 align=center>$l_pr_totals</td>";
        echo "<td>" . NUMBER($total_ore) . "</td>";
        echo "<td>" . NUMBER($total_organics) . "</td>";
        echo "<td>" . NUMBER($total_goods) . "</td>";
        echo "<td>" . NUMBER($total_energy) . "</td>";
        echo "<td align=RIGHT>" . NUMBER($total_colonists) . "</td>";
        echo "<td align=RIGHT>" . NUMBER($total_credits) . "</td>";
        echo "<td></td>";
        echo "<td align=RIGHT>"  . NUMBER($total_fighters) . "</td>";
        echo "<td align=RIGHT>"  . NUMBER($total_torp) . "</td>";
        echo "<td align=center>" . NUMBER($total_base) . "</td>";
        if ($playerinfo[team] > 0)
        {
            echo "<td align=center>" . NUMBER($total_corp) . "</td>";
        }

        echo "<td align=center>" . NUMBER($total_selling) . "</td>";
        echo "</TR>";
        echo "</table>";
        echo "<br>";
        echo "<INPUT TYPE=SUBMIT VALUE=\"Collect Credits\">  <INPUT TYPE=RESET VALUE=RESET>";
        echo "</form>";
    }

    echo "</div>\n";
}

function planet_production_change()
{
    global $db;
    global $res;
    global $playerinfo;
    global $dbtables;
    global $username;
    global $sort;
    global $query;
    global $color_header, $color, $color_line1, $color_line2;
    global $l_pr_teamlink, $l_pr_clicktosort;
    global $l_pr_noplanet;
    global $l_sector, $l_name, $l_unnamed, $l_ore, $l_organics, $l_goods, $l_energy, $l_colonists, $l_credits, $l_fighters, $l_torps, $l_base, $l_selling, $l_pr_totals, $l_yes, $l_no;

    $query = "SELECT * FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] AND base='Y'";
    echo "<div style='width:90%; margin:auto; font-size:14px;'>\n";

    echo "Planetary report <b><a href=planet_report.php?PRepType=0>menu</a></b><br><br>" .
         "<b><a href=planet_report.php?PRepType=1>Planet Status</a></b><br>";

    if ($playerinfo['team']>0)
    {
        echo "<br><b><a href=team_planets.php>$l_pr_teamlink</a></b><br> <br>";
    }

    if (!empty($sort))
    {
        $query .= " ORDER BY";
        if ($sort == "name")
        {
            $query .= " $sort ASC";
        }
        elseif ($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" || $sort == "fighters")
        {
            $query .= " prod_$sort DESC, sector_id ASC";
        }
        elseif ($sort == "colonists" || $sort == "credits")
        {
            $query .= " $sort DESC, sector_id ASC";
        }
        elseif ($sort == "torp")
        {
            $query .= " prod_torp DESC, sector_id ASC";
        }
        else
        {
            $query .= " sector_id ASC";
        }
    }
    else
    {
        $query .= " ORDER BY sector_id ASC";
    }

    $res = $db->Execute($query);

    $i = 0;
    if ($res)
    {
        while (!$res->EOF)
        {
            $planet[$i] = $res->fields;
            $i++;
            $res->MoveNext();
        }
    }

    $num_planets = $i;
    if ($num_planets < 1)
    {
        echo "<br>$l_pr_noplanet";
    }
    else
    {
        echo "<form action='planet_report_ce.php' method='post'>\n";

        // Next block of echo's creates the header of the table
        echo "$l_pr_clicktosort<br><br>\n";
        echo "<table width='100%' border='0' cellspacing='0' cellpadding='2'>\n";
        echo "<TR bgcolor='{$color_header}' valign='bottom'>\n";
        echo "<td align='LEFT'>  <b><a href='planet_report.php?PRepType=2&sort=sector_id'>$l_sector</a></b></td>\n";
        echo "<td align='LEFT'>  <b><a href='planet_report.php?PRepType=2&sort=name'>$l_name</a></b></td>\n";
        echo "<td align='center'><b><a href='planet_report.php?PRepType=2&sort=ore'>$l_ore</a></b></td>\n";
        echo "<td align='center'><b><a href='planet_report.php?PRepType=2&sort=organics'>$l_organics</a></b></td>\n";
        echo "<td align='center'><b><a href='planet_report.php?PRepType=2&sort=goods'>$l_goods</a></b></td>\n";
        echo "<td align='center'><b><a href='planet_report.php?PRepType=2&sort=energy'>$l_energy</a></b></td>\n";
        echo "<td align='RIGHT'> <b><a href='planet_report.php?PRepType=2&sort=colonists'>$l_colonists</a></b></td>\n";
        echo "<td align='RIGHT'> <b><a href='planet_report.php?PRepType=2&sort=credits'>$l_credits</a></b></td>\n";
        echo "<td align='center'><b><a href='planet_report.php?PRepType=2&sort=fighters'>$l_fighters</a></b></td>\n";
        echo "<td align='center'><b><a href='planet_report.php?PRepType=2&sort=torp'>$l_torps</a></b></td>\n";
        //    echo "<td align='center'><b>$l_base?</b></td>\n";
        if ($playerinfo[team] > 0)
        {
            echo "<td align='center'><b>Corp?</b></td>\n";
        }
        echo "<td align='center'><b>$l_selling?</b></td>\n";
        echo "</TR>\n";

        $total_colonists = 0;
        $total_credits = 0;
        $total_corp = 0;

        $temp_var = 0;

        $color = $color_line1;

        for ($i = 0; $i < $num_planets; $i++)
        {
            $total_colonists += $planet[$i][colonists];
            $total_credits += $planet[$i][credits];
            if (empty($planet[$i][name]))
            {
                $planet[$i][name] = $l_unnamed;
            }

            echo "<TR bgcolor=\"$color\">\n";
            echo "<td><a href=rsmove.php?engage=1&destination=". $planet[$i][sector_id] . ">". $planet[$i][sector_id] ."</a></td>\n";
            echo "<td>" . $planet[$i][name] . "</td>\n";
            echo "<td align=center>" . "<input size=6 type=text name=\"prod_ore["      . $planet[$i]["planet_id"] . "]\" value=\"" . $planet[$i]["prod_ore"]      . "\">" . "</td>\n";
            echo "<td align=center>" . "<input size=6 type=text name=\"prod_organics[" . $planet[$i]["planet_id"] . "]\" value=\"" . $planet[$i]["prod_organics"] . "\">" . "</td>\n";
            echo "<td align=center>" . "<input size=6 type=text name=\"prod_goods["    . $planet[$i]["planet_id"] . "]\" value=\"" . $planet[$i]["prod_goods"]    . "\">" . "</td>\n";
            echo "<td align=center>" . "<input size=6 type=text name=\"prod_energy["   . $planet[$i]["planet_id"] . "]\" value=\"" . $planet[$i]["prod_energy"]   . "\">" . "</td>\n";
            echo "<td align=RIGHT>"  . NUMBER($planet[$i][colonists])              . "</td>\n";
            echo "<td align=RIGHT>"  . NUMBER($planet[$i][credits])        . "</td>\n";
            echo "<td align=center>" . "<input size=6 type=text name=\"prod_fighters[" . $planet[$i]["planet_id"] . "]\" value=\"" . $planet[$i]["prod_fighters"] . "\">" . "</td>\n";
            echo "<td align=center>" . "<input size=6 type=text name=\"prod_torp["     . $planet[$i]["planet_id"] . "]\" value=\"" . $planet[$i]["prod_torp"]     . "\">" . "</td>\n";
            if ($playerinfo[team] > 0)
            {
                echo "<td align=center>" . corp_planet_checkboxes($planet, $i) . "</td>\n";
            }

            echo "<td align=center>" . selling_checkboxes($planet, $i)     . "</td>\n";
            echo "</TR>\n";

            if ($color == $color_line1)
            {
                $color = $color_line2;
            }
            else
            {
                $color = $color_line1;
            }
        }

        echo "<TR bgcolor=$color>\n";
        echo "<td COLSPAN=2 align=center>$l_pr_totals</td>\n";
        echo "<td>" . "" . "</td>\n";
        echo "<td>" . "" . "</td>\n";
        echo "<td>" . "" . "</td>\n";
        echo "<td>" . "" . "</td>\n";
        echo "<td align=RIGHT>" . NUMBER($total_colonists) . "</td>\n";
        echo "<td align=RIGHT>" . NUMBER($total_credits)   . "</td>\n";
        echo "<td>" . "" . "</td>\n";
        echo "<td>" . "" . "</td>\n";
        if ($playerinfo[team] > 0)
        {
            echo "<td></td>\n";
        }

        echo "<td></td>\n";
        echo "</TR>\n";
        echo "</table>\n";

        echo "<br>\n";
        echo "<INPUT TYPE=HIDDEN NAME=ship_id VALUE=$playerinfo[ship_id]>\n";
        echo "<INPUT TYPE=HIDDEN NAME=team_id   VALUE=$playerinfo[team]>\n";
        echo "<INPUT TYPE=SUBMIT VALUE=SUBMIT>  <INPUT TYPE=RESET VALUE=RESET>\n";
        echo "</form>\n";
    }

    echo "</div>\n";
}

function corp_planet_checkboxes($planet, $i)
{
    if ($planet[$i][corp] <= 0)
    {
        return("<INPUT TYPE='CHECKBOX' NAME='corp[{$i}]' VALUE='{$planet[$i]['planet_id']}' />");
    }
    elseif ($planet[$i][corp] > 0)
    {
        return("<INPUT TYPE='CHECKBOX' NAME='corp[{$i}]' VALUE='{$planet[$i]['planet_id']}' CHECKED />");
    }
}

function selling_checkboxes($planet, $i)
{
    if ($planet[$i][sells] != 'Y')
    {
        return("<INPUT TYPE='CHECKBOX' NAME='sells[{$i}]' VALUE='{$planet[$i]['planet_id']}' />");
    }
    elseif ($planet[$i][sells] == 'Y')
    {
        return("<INPUT TYPE='CHECKBOX' NAME='sells[{$i}]' VALUE='{$planet[$i]['planet_id']}' CHECKED />");
    }
}

function base_build_check($planet, $i)
{
    global $l_yes, $l_no;
    global $base_ore, $base_organics, $base_goods, $base_credits;

    if ($planet[$i][base] == 'Y')
    {
        return("$l_yes");
    }
    elseif ($planet[$i][ore] >= $base_ore && $planet[$i][organics] >= $base_organics && $planet[$i][goods] >= $base_goods && $planet[$i][credits] >= $base_credits)
    {
        return("<a href=planet_report_ce.php?buildp=" . $planet[$i]["planet_id"] . "&builds=" . $planet[$i]["sector_id"] . ">Build</a>");
    }
    else
    {
        return("$l_no");
    }
}

echo "<br><br>";
TEXT_GOTOMAIN();
include "footer.php";
?>
