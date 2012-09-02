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
// File: includes/ibank_repay.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_repay.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function ibank_repay ($db)
{
    global $playerinfo, $account, $amount;
    global $l_ibank_notrepay, $l_ibank_notenoughrepay, $l_ibank_payloan;
    global $l_ibank_shipaccount, $l_ibank_currentloan, $l_ibank_loanthanks;
    global $l_ibank_invalidamount;
    global $l_ibank_back, $l_ibank_logout;

    $amount = strip_non_num ($amount);
    if (($amount * 1) != $amount)
    {
        ibank_error ($l_ibank_invalidamount, "igb.php?command=loans");
    }

    if ($amount == 0)
    {
        ibank_error ($l_ibank_invalidamount, "igb.php?command=loans");
    }

    if ($account['loan'] == 0)
    {
        ibank_error ($l_ibank_notrepay, "igb.php?command=loans");
    }

    if ($amount > $account['loan'])
    {
        $amount = $account['loan'];
    }

    if ($amount > $playerinfo['credits'])
    {
        ibank_error ($l_ibank_notenoughrepay, "igb.php?command=loans");
    }

    $playerinfo['credits'] -= $amount;
    $account['loan'] -= $amount;

    echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_payloan . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td colspan=2 align=center>" . $l_ibank_loanthanks . "</td>" .
         "<tr valign=top>" .
         "<td colspan=2 align=center>---------------------------------</td>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_shipaccount . " :</td><td nowrap align=right>" . NUMBER ($playerinfo['credits']) . " C<br>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_payloan . " :</td><td nowrap align=right>" . NUMBER ($amount) . " C<br>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_currentloan . " :</td><td nowrap align=right>" . NUMBER ($account['loan']) . " C<br>" .
         "<tr valign=top>" .
         "<td colspan=2 align=center>---------------------------------</td>" .
         "<tr valign=top>" .
         "<td nowrap><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td nowrap align=right>&nbsp;<a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>";

    $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET loan=loan - ?, loantime=? WHERE ship_id = ?", array ($amount, $account['loantime'], $playerinfo['ship_id']));
    db_op_result ($db, $resx, __LINE__, __FILE__);
    $resx = $db->Execute("UPDATE {$db->prefix}ships SET credits=credits - ? WHERE ship_id = ?" , array ($amount, $playerinfo['ship_id']));
    db_op_result ($db, $resx, __LINE__, __FILE__);
}
?>
