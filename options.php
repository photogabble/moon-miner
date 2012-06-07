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
// File: options.php

include("config.php");
include("languages/$lang");
updatecookie();
$title="$l_opt_title";
include("header.php");

if (checklogin())
{
    die();
}

bigtitle();
$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

echo "<FORM ACTION=option2.php METHOD=POST>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=2><B>$l_opt_chpass</B></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>$l_opt_curpass</TD>";
echo "<TD><INPUT TYPE=PASSWORD NAME=oldpass SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD>$l_opt_newpass</TD>";
echo "<TD><INPUT TYPE=PASSWORD NAME=newpass1 SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>$l_opt_newpagain</TD>";
echo "<TD><INPUT TYPE=PASSWORD NAME=newpass2 SIZE=16 MAXLENGTH=16 VALUE=\"\"></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=2><B>$l_opt_userint</B></TD>";
echo "</TR>";
$intrf = ($playerinfo['interface'] == 'N') ? "CHECKED" : "";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>$l_opt_usenew</TD><TD><INPUT TYPE=CHECKBOX NAME=intrf VALUE=N $intrf></INPUT></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=2><B>$l_opt_lang</B></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>$l_opt_select</TD><TD><select NAME=newlang>";

foreach ($avail_lang as $curlang)
{
    if ($curlang['file'] == $playerinfo[lang])
    {
        $selected = "selected";
    }
    else
    {
        $selected = "";
    }
    echo "<option value=$curlang[file] $selected>$curlang[name]</option>";
}

echo "</select></td>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD COLSPAN=2><B>DHTML</B></TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
$dhtml = ($playerinfo['dhtml'] == 'Y') ? "CHECKED" : "";
echo "<TD>$l_opt_enabled</TD><TD><INPUT TYPE=CHECKBOX NAME=dhtml VALUE=Y $dhtml></INPUT></TD>";
echo "</TABLE>";
echo "<BR>";
echo "<INPUT TYPE=SUBMIT value=$l_opt_save>";
echo "</FORM>";

TEXT_GOTOMAIN();
include("footer.php");
?>

