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
// File: classes/BadIbank.php

if (strpos ($_SERVER['PHP_SELF'], 'BadIbank.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BadIbank
{
    public static function ibankBorrow($db, $langvars, $playerinfo, $active_template)
    {
        global $account, $amount, $ibank_loanlimit, $ibank_loanfactor, $ibank_lrate;

        $amount = preg_replace ("/[^0-9]/", "", $amount);
        if (($amount * 1) != $amount)
        {
            ibank_error ($active_template, $langvars, $langvars['l_ibank_invalidamount'], "igb.php?command=loans");
        }

        if ($amount <= 0)
        {
            ibank_error ($active_template, $langvars, $langvars['l_ibank_invalidamount'], "igb.php?command=loans");
        }

        if ($account['loan'] != 0)
        {
            ibank_error ($active_template, $langvars, $langvars['l_ibank_notwoloans'], "igb.php?command=loans");
        }

        $score = BntScore::updateScore ($db, $playerinfo['ship_id'], $bntreg);
        $maxtrans = $score * $score * $ibank_loanlimit;

        if ($amount > $maxtrans)
        {
            ibank_error ($active_template, $langvars, $langvars['l_ibank_loantoobig'], "igb.php?command=loans");
        }

        $amount2 = $amount * $ibank_loanfactor;
        $amount3 = $amount + $amount2;

        $hours = $ibank_lrate / 60;
        $mins = $ibank_lrate % 60;

        $langvars['l_ibank_loanreminder'] = str_replace ("[hours]", $hours, $langvars['l_ibank_loanreminder']);
        $langvars['l_ibank_loanreminder'] = str_replace ("[mins]", $mins, $langvars['l_ibank_loanreminder']);

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_takenaloan'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top><td colspan=2 align=center>" . $langvars['l_ibank_loancongrats'] . "<br><br></tr>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_loantransferred'] . " :</td><td nowrap align=right>" . number_format ($amount, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_loanfee'] . " :</td><td nowrap align=right>" . number_format ($amount2, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_amountowned'] . " :</td><td nowrap align=right>" . number_format ($amount3, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td colspan=2 align=center>---------------------------------<br><br>" . $langvars['l_ibank_loanreminder'] . "<br><br>\"" . $langvars['l_ibank_loanreminder2'] ."\"</td>" .
             "<tr valign=top>" .
             "<td nowrap><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td nowrap align=right>&nbsp;<a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";

        $resx = $db->Execute ("UPDATE {$db->prefix}ibank_accounts SET loan = ?, loantime = NOW() WHERE ship_id = ?", array ($amount3, $playerinfo['ship_id']));
        BntDb::logDbErrors ($db, $resx, __LINE__, __FILE__);

        $resx = $db->Execute ("UPDATE {$db->prefix}ships SET credits = credits + ? WHERE ship_id = ?", array ($amount, $playerinfo['ship_id']));
        BntDb::logDbErrors ($db, $resx, __LINE__, __FILE__);
    }

    public static function ibankLogin($langvars, $playerinfo, $account)
    {
        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_welcometoibank'] . "<br>---------------------------------</td></tr>" .
            "<tr valign=top>" .
             "<td width=150 align=right>" . $langvars['l_ibank_accountholder'] . " :<br><br>" . $langvars['l_ibank_shipaccount'] . " :<br>" . $langvars['l_ibank_ibankaccount'] . "&nbsp;&nbsp;:</td>" .
             "<td style='max-width:550px; padding-right:4px;' align=right>" . $playerinfo['character_name'] . "&nbsp;&nbsp;<br><br>" . number_format ($playerinfo['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_ibank_credit_symbol'] . "<br>" . number_format ($account['balance'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_ibank_credit_symbol'] . "<br></td>" .
             "</tr>" .
             "<tr><td colspan=2 align=center>" . $langvars['l_ibank_operations'] . "<br>---------------------------------<br><br><a href=\"igb.php?command=withdraw\">" . $langvars['l_ibank_withdraw'] . "</a><br><a href=\"igb.php?command=deposit\">" . $langvars['l_ibank_deposit'] . "</a><br><a href=\"igb.php?command=transfer\">" . $langvars['l_ibank_transfer'] . "</a><br><a href=\"igb.php?command=loans\">" . $langvars['l_ibank_loans'] . "</a><br>&nbsp;</td></tr>" .
             "<tr valign=bottom>" .
             "<td align='left'><a href='igb.php'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";
    }
}
?>
