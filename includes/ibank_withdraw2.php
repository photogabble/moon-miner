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
// File: includes/ibank_withdraw2.php

function ibank_withdraw2 ($db)
{
    global $playerinfo, $amount, $account;
    global $l_ibank_invalidwithdrawinput, $l_ibank_nozeroamount3, $l_ibank_notenoughcredits, $l_ibank_accounts;
    global $l_ibank_operationsuccessful, $l_ibank_creditstoyourship, $l_ibank_ibankaccount, $l_ibank_back, $l_ibank_logout;

    $amount = strip_non_num ($amount);
    if (($amount * 1) != $amount)
    {
        ibank_error ($l_ibank_invalidwithdrawinput, "igb.php?command=withdraw");
    }

    if ($amount == 0)
    {
        ibank_error ($l_ibank_nozeroamount3, "igb.php?command=withdraw");
    }

    if ($amount > $account['balance'])
    {
        ibank_error ($l_ibank_notenoughcredits, "igb.php?command=withdraw");
    }

    $account['balance'] -= $amount;
    $playerinfo['credits'] += $amount;

    echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_operationsuccessful . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td colspan=2 align=center>" . NUMBER ($amount) ." " . $l_ibank_creditstoyourship . "</td>" .
         "<tr><td colspan=2 align=center>" . $l_ibank_accounts . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td>Ship Account :<br>" . $l_ibank_ibankaccount . " :</td>" .
         "<td align=right>" . NUMBER ($playerinfo['credits']) . " C<br>" . NUMBER ($account['balance']) . " C</tr>" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>";

    $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance=balance-? WHERE ship_id=?", array ($amount, $playerinfo['ship_id']));
    db_op_result ($db, $resx, __LINE__, __FILE__);
    $resx = $db->Execute("UPDATE {$db->prefix}ships SET credits=credits+? WHERE ship_id=?", array ($amount, $playerinfo['ship_id']));
    db_op_result ($db, $resx, __LINE__, __FILE__);
}
?>
