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
// File: admin/sector_editor.php

if (strpos ($_SERVER['PHP_SELF'], 'sector_editor.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

echo "<h2>" . $langvars['l_admin_sector_editor'] . "</h2>";
echo "<form action='admin.php' method='post'>";
if (empty ($sector))
{
    echo "<select size='20' name='sector'>";
    $res = $db->Execute("SELECT sector_id FROM {$db->prefix}universe ORDER BY sector_id");
    db_op_result ($db, $res, __LINE__, __FILE__);
    while (!$res->EOF)
    {
        $row=$res->fields;
        echo "<option value='" . $row['sector_id'] . "'>" . $row['sector_id'] . "</option>";
        $res->MoveNext();
    }
    echo "</select>";
    echo "&nbsp;<input type='submit' value='" . $langvars['l_edit'] . "'>";
}
else
{
    if (empty ($operation))
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array ($sector));
        db_op_result ($db, $res, __LINE__, __FILE__);
        $row = $res->fields;

        echo "<table border='0' cellspacing='2' cellpadding='2'>";
        echo "<tr><td><tt>" . $langvars['l_admin_sector_id'] . "</tt></td><td><font color='#6f0'>" . $sector . "</font></td>";
        echo "<td align='right'><tt>" . $langvars['l_admin_sector_name'] . "</tt></td><td><input type='text' size='15' name='sector_name' value=\"" . $row['sector_name'] . "\"></td>";
        echo "<td align='right'><tt>" . $langvars['l_admin_zone_id'] . "</tt></td><td>";
        echo "<select size='1' name='zone_id'>";
        $ressubb = $db->Execute("SELECT zone_id,zone_name FROM {$db->prefix}zones ORDER BY zone_name");
        db_op_result ($db, $ressubb, __LINE__, __FILE__);
        while (!$ressubb->EOF)
        {
            $rowsubb=$ressubb->fields;
            if ($rowsubb['zone_id'] == $row['zone_id'])
            {
                echo "<option selected='" . $rowsubb['zone_id'] . "' value='" . $rowsubb['zone_id'] . "'>" . $rowsubb['zone_name'] . "</option>";
            }
            else
            {
                echo "<option value='" . $rowsubb['zone_id'] . "'>" . $rowsubb['zone_name'] . "</option>";
            }
            $ressubb->MoveNext();
        }

        echo "</select></td></tr>";
        echo "<tr><td><tt>" . $langvars['l_admin_beacon'] . "</tt></td><td colspan='5'><input type='text' size='70' name='beacon' value=\"" . $row['beacon'] . "\"></td></tr>";
        echo "<tr><td><tt>" . $langvars['l_admin_distance'] . "</tt></td><td><input type='text' size='9' name='distance' value=\"" . $row['distance'] . "\"></td>";
        echo "<td align='right'><tt>" . $langvars['l_admin_angle1'] . "</tt></td><td><input type='text' size='9' name='angle1' value=\"" . $row['angle1'] . "\"></td>";
        echo "<td align='right'><tt>" . $langvars['l_admin_angle2'] . "</tt></td><td><input type='text' size='9' name='angle2' value=\"" . $row['angle2'] . "\"></td></tr>";
        echo "<tr><td colspan='6'><hr></td></tr>";
        echo "</table>";

        echo "<table border='0' cellspacing='2' cellpadding='2'>";
        echo "<tr><td><tt>" . $langvars['l_admin_port_type'] . "</tt></td><td>";
        echo "<select size='1' name='port_type'>";
        $oportnon = $oportspe = $oportorg = $oportore = $oportgoo = $oportene = "value";
        if ($row['port_type'] == "none") $oportnon = "selected='none' value";
        if ($row['port_type'] == "special") $oportspe = "selected='special' value";
        if ($row['port_type'] == "organics") $oportorg = "selected='organics' value";
        if ($row['port_type'] == "ore") $oportore = "selected='ore' value";
        if ($row['port_type'] == "goods") $oportgoo = "selected='goods' value";
        if ($row['port_type'] == "energy") $oportene = "selected='energy' value";
        echo "<option $oportnon='none'>" . $langvars['l_none'] . "</option>";
        echo "<option $oportspe='special'>" . $langvars['l_special'] . "</option>";
        echo "<option $oportorg='organics'>" . $langvars['l_organics'] . "</option>";
        echo "<option $oportore='ore'>" . $langvars['l_ore'] . "</option>";
        echo "<option $oportgoo='goods'>" . $langvars['l_goods'] . "</option>";
        echo "<option $oportene='energy'>" . $langvars['l_energy'] . "</option>";
        echo "</select></td>";
        echo "<td align='right'><tt>" . $langvars['l_organics'] . "</tt></td><td><input type='text' size='9' name='port_organics' value=\"$row[port_organics]\"></td>";
        echo "<td align='right'><tt>" . $langvars['l_ore'] . "</tt></td><td><input type='text' size='9' name='port_ore' value=\"$row[port_ore]\"></td>";
        echo "<td align='right'><tt>" . $langvars['l_goods'] . "</tt></td><td><input type='text' size='9' name='port_goods' value=\"$row[port_goods]\"></td>";
        echo "<td align='right'><tt>" . $langvars['l_energy'] . "</tt></td><td><input type='text' size='9' name='port_energy' value=\"$row[port_energy]\"></td></tr>";
        echo "<tr><td colspan='10'><hr></td></tr>";
        echo "</table>";

        echo "<br>";
        echo "<input type='hidden' name='sector' value='" . $sector . "'>";
        echo "<input type='hidden' name='operation' value='save'>";
        echo "<input type='submit' size='1' value='" . $langvars['l_save'] . "'>";
    }
    elseif ($operation == "save")
    {
        // Update database
        $secupdate = $db->Execute("UPDATE {$db->prefix}universe SET sector_name=?, zone_id=?, beacon=?, port_type=?, port_organics=?, port_ore=?, port_goods=?, port_energy=?, distance=?, angle1=?, angle2=? WHERE sector_id=?;", array ($sector_name, $zone_id, $beacon, $port_type, $port_organics, $port_ore, $port_goods, $port_energy, $distance, $angle1, $angle2, $sector));

        db_op_result ($db, $secupdate, __LINE__, __FILE__);
        if (!$secupdate)
        {
            echo $langvars['l_admin_sector_editor_failed'] . "<br><br>";
            echo $db->ErrorMsg() . "<br>";
        }
        else
        {
            echo $langvars['l_admin_sector_editor_saved'] . "<br><br>";
        }

        echo "<input type='submit' value=\"" . $langvars['l_admin_return_sector_editor'] . "\">";
        $button_main = false;
    }
    else
    {
        echo $langvars['l_admin_invalid_operation'];
    }
}
echo "<input type='hidden' name='menu' value='sectedit'>";
echo "<input type='hidden' name='swordfish' value='" . $_POST['swordfish'] . "'>";
echo "</form>";
?>
