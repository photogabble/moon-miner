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
// File: includes/ibank_withdraw.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_withdraw.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

function ibank_withdraw ()
{
    global $playerinfo, $account;
    global $l_ibank_withdrawfunds, $l_ibank_fundsavailable, $l_ibank_selwithdrawamount;
    global $l_ibank_withdraw, $l_ibank_back, $l_ibank_logout;

    echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_withdrawfunds . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_fundsavailable . ":</td>" .
         "<td align=right>" . number_format ($account['balance'], 0, $local_number_dec_point, $local_number_thousands_sep) ." C<br></td>" .
         "</tr><tr valign=top>" .
         "<td>" . $l_ibank_selwithdrawamount . ":</td><td align=right>" .
         "<form action='igb.php?command=withdraw2' method=post>" .
         "<input class=term type=text size=15 maxlength=20 name=amount value=0>" .
         "<br><br><input class=term type=submit value='" . $l_ibank_withdraw . "'>" .
         "</form></td></tr>" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>";
}
?>
