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
// File: device.php

include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_device_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

bigtitle();

echo "$l_device_expl<BR><BR>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_device</B></TD><TD><B>$l_qty</B></TD><TD><B>$l_usage</B></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD><A HREF=beacon.php>$l_beacons</A></TD><TD>" . NUMBER($playerinfo[dev_beacon]) . "</TD><TD>$l_manual</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD><A HREF=warpedit.php>$l_warpedit</A></TD><TD>" . NUMBER($playerinfo[dev_warpedit]) . "</TD><TD>$l_manual</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD><A HREF=genesis.php>$l_genesis</A></TD><TD>" . NUMBER($playerinfo[dev_genesis]) . "</TD><TD>$l_manual</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD>$l_deflect</TD><TD>" . NUMBER($playerinfo[dev_minedeflector]) . "</TD><TD>$l_automatic</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD><A HREF=mines.php?op=1>$l_mines</A></TD><TD>" . NUMBER($playerinfo[torps]) . "</TD><TD>$l_manual</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD><A HREF=mines.php?op=2>$l_fighters</A></TD><TD>" . NUMBER($playerinfo[ship_fighters]) . "</TD><TD>$l_manual</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD><A HREF=emerwarp.php>$l_ewd</A></TD><TD>" . NUMBER($playerinfo[dev_emerwarp]) . "</TD><TD>$l_manual/$l_automatic</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD>$l_escape_pod</TD><TD>" . (($playerinfo[dev_escapepod] == 'Y') ? $l_yes : $l_no) . "</TD><TD>$l_automatic</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>$l_fuel_scoop</TD><TD>" . (($playerinfo[dev_fuelscoop] == 'Y') ? $l_yes : $l_no) . "</TD><TD>$l_automatic</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD>$l_lssd</TD><TD>" . (($playerinfo[dev_lssd] == 'Y') ? $l_yes : $l_no) . "</TD><TD>$l_automatic</TD>";
echo "</TR>";
echo "</TABLE>";
echo "<BR>";

TEXT_GOTOMAIN();

include("footer.php");

?>
