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
// File: includes/ibank_login.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_login.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function ibank_login ($langvars)
{
    global $playerinfo, $account;
	global $local_number_thousands_sep, $local_number_dec_point;

    echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_welcometoibank'] . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td width=150 align=right>" . $langvars['l_ibank_accountholder'] . " :<br><br>" . $langvars['l_ibank_shipaccount'] . " :<br>" . $langvars['l_ibank_ibankaccount'] . "&nbsp;&nbsp;:</td>" .
         "<td style='max-width:550px; padding-right:4px;' align=right>" . $playerinfo['character_name'] . "&nbsp;&nbsp;<br><br>" . number_format ($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " " . $langvars['l_ibank_credit_symbol'] . "<br>" . number_format ($account['balance'], 0, $local_number_dec_point, $local_number_thousands_sep) . " " . $langvars['l_ibank_credit_symbol'] . "<br></td>" .
         "</tr>" .
         "<tr><td colspan=2 align=center>" . $langvars['l_ibank_operations'] . "<br>---------------------------------<br><br><a href=\"igb.php?command=withdraw\">" . $langvars['l_ibank_withdraw'] . "</a><br><a href=\"igb.php?command=deposit\">" . $langvars['l_ibank_deposit'] . "</a><br><a href=\"igb.php?command=transfer\">" . $langvars['l_ibank_transfer'] . "</a><br><a href=\"igb.php?command=loans\">" . $langvars['l_ibank_loans'] . "</a><br>&nbsp;</td></tr>" .
         "<tr valign=bottom>" .
         "<td align='left'><a href='igb.php'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
         "</tr>";
}
?>
