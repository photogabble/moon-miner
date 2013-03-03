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
// File: ibank_consolidate.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_consolidate.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function ibank_consolidate ()
{
    global $dplanet_id, $l_ibank_transferrate3, $ibank_tconsolidate, $l_ibank_consolrates;
    global $l_ibank_minimum, $l_ibank_maximum, $l_ibank_back, $l_ibank_logout;
    global $l_ibank_planetconsolidate, $l_ibank_compute, $ibank_paymentfee;

    $percent = $ibank_paymentfee * 100;

    $l_ibank_transferrate3 = str_replace ("[ibank_num_percent]", number_format ($percent, 1, $local_number_dec_point, $local_number_thousands_sep), $l_ibank_transferrate3);
    $l_ibank_transferrate3 = str_replace ("[nbplanets]", $ibank_tconsolidate, $l_ibank_transferrate3);

    echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_planetconsolidate . "<br>---------------------------------</td></tr>" .
         "<form action='igb.php?command=consolidate2' method=post>" .
         "<tr valign=top>" .
         "<td colspan=2>" . $l_ibank_consolrates . " :</td>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_minimum . " :<br>" .
         "<br>" . $l_ibank_maximum . " :</td>" .
         "<td align=right>" .
         "<input class=term type=text size=15 maxlength=20 name=minimum value=0><br><br>" .
         "<input class=term type=text size=15 maxlength=20 name=maximum value=0><br><br>" .
         "<input class=term type=submit value=\"" . $l_ibank_compute . "\"></td>" .
         "<input type=hidden name=dplanet_id value=" . $dplanet_id . ">" .
         "</form>" .
         "<tr><td colspan=2 align=center>" .
         $l_ibank_transferrate3 .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=transfer'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>";
}
?>
