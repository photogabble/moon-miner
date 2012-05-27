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
// File: beacon.php

include("config.php");
updatecookie();
include("languages/$lang");
$title=$l_beacon_title;
include("header.php");

if (checklogin())
{
    die();
}

$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo=$result->fields;

$result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
$sectorinfo=$result2->fields;

$allowed_rsw = "N";

bigtitle();

if($playerinfo[dev_beacon] > 0)
{
    $res = $db->Execute("SELECT allow_beacon FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
    $zoneinfo = $res->fields;
    if($zoneinfo[allow_beacon] == 'N')
    {
        echo "$l_beacon_notpermitted<BR><BR>";
    }
    elseif($zoneinfo[allow_beacon] == 'L')
    {
        $result3 = $db->Execute("SELECT * FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
        $zoneowner_info = $result3->fields;
        $result5 = $db->Execute("SELECT team FROM $dbtables[ships] WHERE ship_id='$zoneowner_info[owner]'");
        $zoneteam = $result5->fields;

        if($zoneowner_info[owner] != $playerinfo[ship_id])
        {
            if(($zoneteam[team] != $playerinfo[team]) || ($playerinfo[team] == 0))
            {
                echo "$l_beacon_notpermitted<BR><BR>";
            }
            else
            {
                $allowed_rsw = "Y";
            }
        }
        else
        {
            $allowed_rsw = "Y";
        }
    }
    else
    {
        $allowed_rsw = "Y";
    }

    if($allowed_rsw == "Y")
    {
        if($beacon_text == "")
        {
            if($sectorinfo[beacon] != "")
            {
                echo "$l_beacon_reads: \"$sectorinfo[beacon]\"<BR><BR>";
            }
            else
            {
                echo "$l_beacon_none<BR><BR>";
            }
            echo "<form action=beacon.php method=post>";
            echo "<table>";
            echo "<tr><td>$l_beacon_enter:</td><td><input type=text name=beacon_text size=40 maxlength=80></td></tr>";
            echo "</table>";
            echo "<input type=submit value=$l_submit><input type=reset value=$l_reset>";
            echo "</form>";
        }
        else
        {
            $beacon_text = trim(strip_tags($beacon_text));
            echo "$l_beacon_nowreads: \"$beacon_text\".<BR><BR>";
            $update = $db->Execute("UPDATE $dbtables[universe] SET beacon='$beacon_text' WHERE sector_id=$sectorinfo[sector_id]");
            $update = $db->Execute("UPDATE $dbtables[ships] SET dev_beacon=dev_beacon-1 WHERE ship_id=$playerinfo[ship_id]");
        }
    }
}
else
{
    echo "$l_beacon_donthave<BR><BR>";
}

TEXT_GOTOMAIN();
include("footer.php");
?>
