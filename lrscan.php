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
// File: lrscan.php

include "config.php";
updatecookie();
include "languages/$lang";
$title = $l_lrs_title;
include "header.php";
if (checklogin())
{
    die();
}

bigtitle();

srand((double)microtime() * 1000000);
$link_bnthelper_string = '';
$port_bnthelper_string = '';
$planet_bnthelper_string = '';

// Get user info
$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

if ($sector == "*")
{
    if (!$allow_fullscan)
    {
        echo $l_lrs_nofull . "<br><br>";
        TEXT_GOTOMAIN();
        include "footer.php";
        die();
    }
    if ($playerinfo['turns'] < $fullscan_cost)
    {
        $l_lrs_noturns=str_replace("[turns]",$fullscan_cost,$l_lrs_noturns);
        echo $l_lrs_noturns . "<br><br>";
        TEXT_GOTOMAIN();
        include "footer.php";
        die();
    }

    echo "$l_lrs_used " . NUMBER($fullscan_cost) . " $l_lrs_turns. " . NUMBER($playerinfo['turns'] - $fullscan_cost) . " $l_lrs_left.<br><br>";

    // Deduct the appropriate number of turns
    $db->Execute("UPDATE $dbtables[ships] SET turns=turns-$fullscan_cost, turns_used=turns_used+$fullscan_cost where ship_id='$playerinfo[ship_id]'");

    // User requested a full long range scan
    $l_lrs_reach=str_replace("[sector]",$playerinfo['sector'],$l_lrs_reach);
    echo "$l_lrs_reach<br><br>";

    // Get sectors which can be reached from the player's current sector
    $result = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start='$playerinfo[sector]' ORDER BY link_dest");
    echo "<table border=0 cellspacing=0 cellpadding=0 width=\"100%\">";
    echo "<tr bgcolor=\"$color_header\"><td><b>$l_sector</b><td></td></td><td><b>$l_lrs_links</b></td><td><b>$l_lrs_ships</b></td><td colspan=2><b>$l_port</b></td><td><b>$l_planets</b></td><td><b>$l_mines</b></td><td><b>$l_fighters</b></td>";
    if ($playerinfo['dev_lssd'] == 'Y')
    {
        echo "<td><b>$l_lss</b></td>";
    }
    echo "</tr>";
    $color = $color_line1;
    while (!$result->EOF)
    {
        $row = $result->fields;
        // Get number of sectors which can be reached from scanned sector
        $result2 = $db->Execute("SELECT COUNT(*) AS count FROM $dbtables[links] WHERE link_start='$row[link_dest]'");
        $row2 = $result2->fields;
        $num_links = $row2['count'];

        // Get number of ships in scanned sector
        $result2 = $db->Execute("SELECT COUNT(*) AS count FROM $dbtables[ships] WHERE sector='$row[link_dest]' AND on_planet='N' and ship_destroyed='N'");
        $row2 = $result2->fields;
        $num_ships = $row2['count'];

        // Get port type and discover the presence of a planet in scanned sector
        $result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$row[link_dest]'");
        $result3 = $db->Execute("SELECT planet_id FROM $dbtables[planets] WHERE sector_id='$row[link_dest]'");
        $resultSDa = $db->Execute("SELECT SUM(quantity) as mines from $dbtables[sector_defence] WHERE sector_id='$row[link_dest]' and defence_type='M'");
        $resultSDb = $db->Execute("SELECT SUM(quantity) as fighters from $dbtables[sector_defence] WHERE sector_id='$row[link_dest]' and defence_type='F'");

        $sectorinfo = $result2->fields;
        $defM = $resultSDa->fields;
        $defF = $resultSDb->fields;
        $port_type = $sectorinfo['port_type'];
        $has_planet = $result3->RecordCount();
        $has_mines = NUMBER($defM['mines']);
        $has_fighters = NUMBER($defF['fighters']);

        if ($port_type != "none")
        {
            $icon_alt_text = ucfirst(t_port($port_type));
            $icon_port_type_name = $port_type . ".png";
            $image_string = "<img align=absmiddle height=12 width=12 alt=\"$icon_alt_text\" src=\"images/$icon_port_type_name\">&nbsp;";
        }
        else
        {
            $image_string = "&nbsp;";
        }


        echo "<tr bgcolor=\"$color\"><td><a href=move.php?sector=$row[link_dest]>$row[link_dest]</a></td><td><a href=lrscan.php?sector=$row[link_dest]>Scan</a></td><td>$num_links</td><td>$num_ships</td><td width=12>$image_string</td><td>" . t_port($port_type) . "</td><td>$has_planet</td><td>$has_mines</td><td>$has_fighters</td>";
        if ($playerinfo['dev_lssd'] == 'Y')
        {
            $resx = $db->Execute("SELECT * from $dbtables[movement_log] WHERE ship_id <> $playerinfo[ship_id] AND sector_id = $row[link_dest] ORDER BY time DESC LIMIT 1");
            if (!$resx)
            {
                echo "<td>None</td>";
            }
            else
            {
                $myrow = $resx->fields;
                echo "<td>" . get_player($myrow['ship_id']) . "</td>";
            }
        }

        echo "</tr>";
        if ($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }
        $result->MoveNext();
    }
    echo "</table>";

    if ($num_links == 0)
    {
        echo "$l_none.";
    }
    else
    {
        echo "<br>$l_lrs_click";
    }
}
else
{
    // User requested a single sector (standard) long range scan
    // Get scanned sector information
    $result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
    $sectorinfo = $result2->fields;

    // Get sectors which can be reached through scanned sector
    $result3 = $db->Execute("SELECT link_dest FROM $dbtables[links] WHERE link_start='$sector' ORDER BY link_dest ASC");
    $i=0;

    while (!$result3->EOF)
    {
        $links[$i] = $result3->fields['link_dest'];
        $i++;
        $result3->MoveNext();
    }
    $num_links=$i;

    // Get sectors which can be reached from the player's current sector
    $result3a = $db->Execute("SELECT link_dest FROM $dbtables[links] WHERE link_start='$playerinfo[sector]'");
    $i=0;
    $flag=0;

    while (!$result3a->EOF)
    {
        if ($result3a->fields['link_dest'] == $sector)
        {
            $flag=1;
        }
        $i++;
        $result3a->MoveNext();
    }

    if ($flag == 0)
    {
        echo "$l_lrs_cantscan<br><br>";
        TEXT_GOTOMAIN();
        die();
    }

    echo "<table border=0 cellspacing=0 cellpadding=0 width=\"100%\">";
    echo "<tr bgcolor=\"$color_header\"><td><b>$l_sector $sector";
    if ($sectorinfo['sector_name'] != "")
    {
        echo " ($sectorinfo[sector_name])";
    }
    echo "</b></tr>";
    echo "</table><br>";

    echo "<table border=0 cellspacing=0 cellpadding=0 width=\"100%\">";
    echo "<tr bgcolor=\"$color_line2\"><td><b>$l_links</b></td></tr>";
    echo "<tr><td>";
    if ($num_links == 0)
    {
        echo "$l_none";
        $link_bnthelper_string="<!--links:N:-->";
    }
    else
    {
        $link_bnthelper_string="<!--links:Y";
        for ($i = 0; $i < $num_links; $i++)
        {
            echo "$links[$i]";
            $link_bnthelper_string=$link_bnthelper_string . ":" . $links[$i];
            if ($i + 1 != $num_links)
            {
                echo ", ";
            }
        }
        $link_bnthelper_string=$link_bnthelper_string . ":-->";
    }

    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td><b>$l_ships</b></td></tr>";
    echo "<tr><td>";
    if ($sector != 0)
    {
        // Get ships located in the scanned sector
        $result4 = $db->Execute("SELECT ship_id,ship_name,character_name,cloak FROM $dbtables[ships] WHERE sector='$sector' AND on_planet='N'");
        if ($result4->EOF)
        {
            echo "$l_none";
        }
        else
        {
            $num_detected = 0;
            while (!$result4->EOF)
            {
                $row = $result4->fields;
                // Display other ships in sector - unless they are successfully cloaked
                $success = SCAN_SUCCESS($playerinfo['sensors'], $row['cloak']);
                if ($success < 5)
                {
                    $success = 5;
                }
                if ($success > 95)
                {
                    $success = 95;
                }
                $roll = rand(1, 100);
                if ($roll < $success)
                {
                    $num_detected++;
                    echo $row['ship_name'] . "(" . $row['character_name'] . ")<br>";
                }
                $result4->MoveNext();
            }
            if (!$num_detected)
            {
                echo "$l_none";
            }
        }
    }
    else
    {
        echo "$l_lrs_zero";
    }

    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td><b>$l_port</b></td></tr>";
    echo "<tr><td>";
    if ($sectorinfo['port_type'] == "none")
    {
        echo "$l_none";
        $port_bnthelper_string="<!--port:none:0:0:0:0:-->";
    }
    else
    {
        if ($sectorinfo['port_type'] != "none")
        {
            $port_type = $sectorinfo['port_type'];
            $icon_alt_text = ucfirst(t_port($port_type));
            $icon_port_type_name = $port_type . ".png";
            $image_string = "<img align=absmiddle height=12 width=12 alt=\"$icon_alt_text\" src=\"images/$icon_port_type_name\">";
        }
        echo "$image_string " . t_port($sectorinfo['port_type']);
        $port_bnthelper_string="<!--port:" . $sectorinfo['port_type'] . ":" . $sectorinfo['port_ore'] . ":" . $sectorinfo['port_organics'] . ":" . $sectorinfo['port_goods'] . ":" . $sectorinfo['port_energy'] . ":-->";
    }
    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td><b>$l_planets</b></td></tr>";
    echo "<tr><td>";
    $query = $db->Execute("SELECT name, owner FROM $dbtables[planets] WHERE sector_id=$sectorinfo[sector_id]");

    if ($query->EOF)
    {
        echo "$l_none";
        $planet_bnthelper_string="<!--planet:N:::-->";
    }

    while (!$query->EOF)
    {
        $planet = $query->fields;
        if (empty($planet['name']))
        {
            echo "$l_unnamed";
        }
        else
        {
            echo "$planet[name]";
        }

        if ($planet['owner'] == 0)
        {
            echo " ($l_unowned)";
        }
        else
        {
            $result5 = $db->Execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id=$planet[owner]");
            $planet_owner_name = $result5->fields;
            echo " ($planet_owner_name[character_name])";
        }
        $query->MoveNext();
    }

    $resultSDa = $db->Execute("SELECT SUM(quantity) as mines from $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type='M'");
    $resultSDb = $db->Execute("SELECT SUM(quantity) as fighters from $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type='F'");
    $defM = $resultSDa->fields;
    $defF = $resultSDb->fields;

    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line1\"><td><b>$l_mines</b></td></tr>";
    $has_mines =  NUMBER($defM['mines']);
    echo "<tr><td>" . $has_mines;
    echo "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td><b>$l_fighters</b></td></tr>";
    $has_fighters =  NUMBER($defF['fighters']);
    echo "<tr><td>" . $has_fighters;
    echo "</td></tr>";
    if ($playerinfo['dev_lssd'] == 'Y')
    {
        echo "<tr bgcolor=\"$color_line2\"><td><b>$l_lss</b></td></tr>";
        echo "<tr><td>";
        $resx = $db->Execute("SELECT * from $dbtables[movement_log] WHERE ship_id <> $playerinfo[ship_id] AND sector_id = $sector ORDER BY time DESC LIMIT 1");
        if (!$resx)
        {
            echo "None";
        }
        else
        {
            $myrow = $resx->fields;
            echo get_player($myrow['ship_id']);
        }
    }
    else
    {
        echo "<tr><td>";
    }
    echo "</td></tr>";
    echo "</table><br>";
    echo "<a href=move.php?sector=$sector>$l_clickme</a> $l_lrs_moveto $sector.";
}

$rspace_bnthelper_string="<!--rspace:" . $sectorinfo['distance'] . ":" . $sectorinfo['angle1'] . ":" . $sectorinfo['angle2'] . ":-->";
echo $link_bnthelper_string;
echo $port_bnthelper_string;
echo $planet_bnthelper_string;
echo $rspace_bnthelper_string;
echo "<br><br>";
TEXT_GOTOMAIN();

include "footer.php";
?>
