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
// File: includes/ibank_deposit.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_deposit.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function ibank_deposit ()
{
    global $account, $playerinfo;
    global $l_ibank_depositfunds, $l_ibank_fundsavailable, $l_ibank_seldepositamount;
    global $l_ibank_deposit, $l_ibank_back, $l_ibank_logout;

    $max_credits_allowed = 18446744073709000000;
    $credit_space = ($max_credits_allowed - $account['balance']);

    if ($credit_space > $playerinfo['credits'])
    {
        $credit_space = ($playerinfo['credits']);
    }

    if ($credit_space < 0)
    {
        $credit_space = 0;
    }

    echo "<tr><td height=53 colspan=2 align=center valign=top>" . $l_ibank_depositfunds . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td height=30>" . $l_ibank_fundsavailable . " :</td>" .
         "<td align=right>" . NUMBER ($playerinfo['credits']) ." C<br></td>" .
         "</tr><tr valign=top>" .
         "<td height=90>" . $l_ibank_seldepositamount . " :</td><td align=right>" .
         "<form action='igb.php?command=deposit2' method=post>" .
         "<input class=term type=text size=15 maxlength=20 name=amount value=0>" .
         "<br><br><input class=term type=submit value=" . $l_ibank_deposit . ">" .
         "</form>" .
         "</td></tr>" .
         "<tr>" .
         "  <td height=30  colspan=2 align=left>" .
         "    <span style='color:\"#00ff00\";'>You can deposit only ". NUMBER ($credit_space)." credits.</span><br>" .
         "  </td>" .
         "</tr>" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>";

}
?>
