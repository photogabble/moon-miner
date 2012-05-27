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
// File: galaxy.php

include("config.php");
updatecookie();
include("languages/$lang");
$title=$l_map_title;
include("header.php");

if(checklogin())
{
    die();
}

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;
$result3 = $db->Execute("SELECT distinct $dbtables[movement_log].sector_id, port_type, beacon FROM $dbtables[movement_log], $dbtables[universe] WHERE ship_id = $playerinfo[ship_id] AND $dbtables[movement_log].sector_id=$dbtables[universe].sector_id order by sector_id ASC;");
$row = $result3->fields;

bigtitle();

$tile['special']="space261_md_blk.gif";
$tile['ore']="space262_md_blk.gif";
$tile['organics']="space263_md_blk.gif";
$tile['energy']="space264_md_blk.gif";
$tile['goods']="space265_md_blk.gif";
$tile['none']="space.gif";
$tile['unknown']="uspace.gif";

$cur_sector= 0;
$cur_index    = 0;

echo "cursector = $cur_sector max= $sector_max";

echo "<table style='background-color:#000000; border:#555555 1px solid;' border = '0' cellpadding = '0' cellspacing='1' >\n";
echo "<tr><td style='background-color:#111111; text-align:center; border:#555555 1px solid; color:#fff; font-size:26px; padding:4px;' colspan='52'>Blacknova Traders Galaxy Map</td></tr>\n";
while($cur_sector < $sector_max)
{
    $break = ($cur_sector +1 ) % 50;
    if ($break == 1)
    {
        print "<tr><td style='background-color:#111111; border:#555555 1px solid; text-align:right; padding:2px;'>$cur_sector</td> ";
        flush();
    }

    if(isset($row['sector_id']) && ($row['sector_id'] == $cur_sector) && $row != false )
    {
        $port = $row['port_type'];
        $alt  = "Sector: {$row['sector_id']}\nPort: {$row['port_type']}\n";
        if (!is_null($row['beacon']))
        {
            $alt .= "{$row['beacon']}";
        }
        $cur_index = $cur_index + 1;
        $result3->Movenext();
        $row = $result3->fields;
    }
    elseif($cur_sector == 0)
    {
        $port = "special";
        $alt = "Sector: $cur_sector\nPort: $port \nSol: Hub of the Universe.";
    }
    else
    {
        $port ="unknown";
        $alt = "$cur_sector - unknown";
    }

    print "<td style='background-color:#000000; border:#555555 1px solid;'><A HREF=rsmove.php?engage=1&destination=$cur_sector><img width='25' height='25' src='images/{$tile[$port]}' alt='$alt' border='0'></A></td>";
    flush();

    if ($break==0)
    {
        print "<td style='background-color:#111111; border:#555555 1px solid; text-align:right; padding:2px;'>$cur_sector</td></tr>\n";
        flush();
    }
    $cur_sector = $cur_sector + 1;
}

echo "<tr><td style='background-color:#111111; text-align:left; border:#555555 1px solid; color:#fff; font-size:12px; padding:4px;' colspan='52'>Galaxy Map</td></tr>\n";
echo "</table>\n";

echo "<BR><BR>";

echo "<table border='0' cellspacing='0' cellpadding='0'>\n";
echo "  <tr>\n";
echo "    <td width='30'><img src=images/{$tile['special']}></td>\n";
echo "    <td width='170'> &lt;- Special Port</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td width='30'><img src=images/{$tile['ore']}></td>\n";
echo "    <td width='170'> &lt;- Ore Port</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td width='30'><img src=images/{$tile['organics']}></td>\n";
echo "    <td width='170'> &lt;- Organics Port</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td width='30'><img src=images/{$tile['energy']}></td>\n";
echo "    <td width='170'> &lt;- Energy Port</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td width='30'><img src=images/{$tile['goods']}></td>\n";
echo "    <td width='170'> &lt;- Goods Port</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td width='30'><img src=images/{$tile['none']}></td>\n";
echo "    <td width='170'> &lt;- No Port</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "    <td width='30'><img src=images/{$tile['unknown']}></td>\n";
echo "    <td width='170'> &lt;- Unexplored</td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "<BR><BR>";

echo "Click <a href=main.php>here</a> to return to main menu.";
include("footer.php");
?>
