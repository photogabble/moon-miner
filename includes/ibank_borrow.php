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
// File: includes/ibank_borrow.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_borrow.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function ibank_borrow ($db)
{
    global $playerinfo, $account, $amount, $ibank_loanlimit, $ibank_loanfactor;
    global $l_ibank_invalidamount,$l_ibank_notwoloans, $l_ibank_loantoobig;
    global $l_ibank_takenaloan, $l_ibank_loancongrats, $l_ibank_loantransferred;
    global $l_ibank_loanfee, $l_ibank_amountowned, $ibank_lrate, $l_ibank_loanreminder, $l_ibank_loanreminder2;
    global $l_ibank_back, $l_ibank_logout;

    $amount = strip_non_num ($amount);
    if (($amount * 1) != $amount)
    {
        ibank_error ($l_ibank_invalidamount, "igb.php?command=loans");
    }

    if ($amount <= 0)
    {
        ibank_error ($l_ibank_invalidamount, "igb.php?command=loans");
    }

    if ($account['loan'] != 0)
    {
        ibank_error($l_ibank_notwoloans, "igb.php?command=loans");
    }

    include_once './calc_score.php';
    $score = calc_score ($db, $playerinfo['ship_id']);
    $maxtrans = $score * $score * $ibank_loanlimit;

    if ($amount > $maxtrans)
    {
        ibank_error($l_ibank_loantoobig, "igb.php?command=loans");
    }

    $amount2 = $amount * $ibank_loanfactor;
    $amount3 = $amount + $amount2;

    $hours = $ibank_lrate / 60;
    $mins = $ibank_lrate % 60;

    $l_ibank_loanreminder = str_replace("[hours]", $hours, $l_ibank_loanreminder);
    $l_ibank_loanreminder = str_replace("[mins]", $mins, $l_ibank_loanreminder);

    echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_takenaloan . "<br>---------------------------------</td></tr>" .
         "<tr valign=top><td colspan=2 align=center>" . $l_ibank_loancongrats . "<br><br></tr>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_loantransferred . " :</td><td nowrap align=right>" . NUMBER ($amount) . " C<br>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_loanfee . " :</td><td nowrap align=right>" . NUMBER ($amount2) . " C<br>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_amountowned . " :</td><td nowrap align=right>" . NUMBER ($amount3) . " C<br>" .
         "<tr valign=top>" .
         "<td colspan=2 align=center>---------------------------------<br><br>" . $l_ibank_loanreminder . "<br><br>\"" . $l_ibank_loanreminder2 ."\"</td>" .
         "<tr valign=top>" .
         "<td nowrap><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td nowrap align=right>&nbsp;<a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>";

    $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET loan = ?, loantime = NOW() WHERE ship_id = ?", array ($amount3, $playerinfo['ship_id']));
    db_op_result ($db, $resx, __LINE__, __FILE__);

    $resx = $db->Execute("UPDATE {$db->prefix}ships SET credits = credits + ? WHERE ship_id = ?", array ($amount, $playerinfo['ship_id']));
    db_op_result ($db, $resx, __LINE__, __FILE__);
}
?>
