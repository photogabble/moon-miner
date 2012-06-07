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
// File: mailto.php

include("config.php");
updatecookie();
include("languages/$lang");
$title="$l_mt_title";
include("header.php");

if (checklogin())
{
    die();
}

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

bigtitle();

if (empty($content))
{
    $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_destroyed = 'N' AND turns_used > 0 ORDER BY character_name ASC");
    echo "<FORM ACTION=mailto2.php METHOD=POST>";
    echo "<TABLE>";
    echo "<TR><TD>To:</TD><TD><SELECT NAME=to style='width:200px;'>";
    while (!$res->EOF)
    {
        $row=$res->fields;
        if ($row[ship_id] == $to)
        {
            echo "\n<OPTION SELECTED>$row[character_name]</OPTION>";
        }
        else
        {
            echo "\n<OPTION>$row[character_name]</OPTION>";
        }
        $res->MoveNext();
    }
    echo "</SELECT></TD></TR>";
    echo "<TR><TD>$l_mt_from</TD><TD><INPUT DISABLED TYPE=TEXT NAME=dummy SIZE=40 MAXLENGTH=40 VALUE=\"$playerinfo[character_name]\"></TD></TR>";
    echo "<TR><TD>$l_mt_subject</TD><TD><INPUT TYPE=TEXT NAME=subject SIZE=40 MAXLENGTH=40></TD></TR>";
    echo "<TR><TD>$l_mt_message:</TD><TD><TEXTAREA NAME=content ROWS=5 COLS=40></TEXTAREA></TD></TR>";
    echo "<TR><TD></TD><TD><INPUT TYPE=SUBMIT VALUE=$l_mt_send><INPUT TYPE=RESET VALUE=Clear></TD>";
    echo "</TABLE>";
    echo "</FORM>";
}
else
{
    echo "$l_mt_sent<BR><BR>";
    $content = htmlspecialchars($content);
    $subject = htmlspecialchars($subject);

    $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE character_name='$to'");
    $target_info = $res->fields;
    $db->Execute("INSERT INTO messages (sender_id, recp_id, subject, message) VALUES ('".$playerinfo[ship_id]."', '".$target_info[ship_id]."', '".$subject."', '".$content."')");
    #using this three lines to get recipients ship_id and sending the message -- blindcoder
}

TEXT_GOTOMAIN();
include("footer.php");
?>
