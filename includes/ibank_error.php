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
// File: ibank_error.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_error.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function ibank_error ($errmsg, $backlink, $title="Error!")
{
    global $l_ibank_ibankerrreport, $l_ibank_back, $l_ibank_logout;

    $title = $l_ibank_ibankerrreport;
    echo "<tr><td colspan=2 align=center valign=top>" . $title . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td colspan=2 align=center>" . $errmsg . "</td>" .
         "</tr>" .
         "<tr valign=bottom>" .
         "<td><a href=" . $backlink . ">" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>" .
         "</table>" .
         "</td></tr>" .
         "</table>" .
         "<img width=600 height=21 src=images/div2.png>" .
         "</center>";

    include '../footer.php';
    die();
}
?>
