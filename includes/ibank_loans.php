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
// File: includes/ibank_loans.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_loans.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function ibank_loans ($db)
{
    global $playerinfo, $account;
    global $ibank_loanlimit, $ibank_loanfactor, $ibank_loaninterest;
    global $l_ibank_loanstatus,$l_ibank_shipaccount, $l_ibank_currentloan, $l_ibank_repay;
    global $l_ibank_maxloanpercent, $l_ibank_loanamount, $l_ibank_borrow, $l_ibank_loanrates;
    global $l_ibank_back, $l_ibank_logout, $ibank_lrate, $l_ibank_loantimeleft, $l_ibank_loanlate, $l_ibank_repayamount;

    echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_loanstatus . "<br>---------------------------------</td></tr>" .
         "<tr valign=top><td>" . $l_ibank_shipaccount . " :</td><td align=right>" . NUMBER ($playerinfo['credits']) . " C</td></tr>" .
         "<tr valign=top><td>" . $l_ibank_currentloan . " :</td><td align=right>" . NUMBER ($account['loan']) . " C</td></tr>";

    if ($account['loan'] != 0)
    {
        $curtime = time();
        $res = $db->Execute("SELECT UNIX_TIMESTAMP(loantime) as time FROM {$db->prefix}ibank_accounts WHERE ship_id = ?", array ($playerinfo['ship_id']));
        db_op_result ($db, $res, __LINE__, __FILE__);
        if (!$res->EOF)
        {
            $time = $res->fields;
        }

        $difftime = ($curtime - $time['time']) / 60;

        echo "<tr valign=top><td nowrap>" . $l_ibank_loantimeleft . " :</td>";

        if ($difftime > $ibank_lrate)
        {
            echo "<td align=right>" . $l_ibank_loanlate . "</td></tr>";
        }
        else
        {
            $difftime = $ibank_lrate - $difftime;
            $hours = $difftime / 60;
            $hours = (int) $hours;
            $mins = $difftime % 60;
            echo "<td align=right>{$hours}h {$mins}m</td></tr>";
        }

        $factor = $ibank_loanfactor *= 100;
        $interest = $ibank_loaninterest *= 100;

        $l_ibank_loanrates = str_replace ("[factor]", $factor, $l_ibank_loanrates);
        $l_ibank_loanrates = str_replace ("[interest]", $interest, $l_ibank_loanrates);

        echo "<form action='igb.php?command=repay' method=post>" .
             "<tr valign=top>" .
             "<td><br>" . $l_ibank_repayamount . " :</td>" .
             "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
             "<br><input class=term type=submit value='" . $l_ibank_repay . "'></td>" .
             "</form>" .
             "<tr><td colspan=2 align=center>" . $l_ibank_loanrates;
    }
    else
    {
        $percent = $ibank_loanlimit * 100;
        include_once './gen_score.php';
        $score = gen_score ($db, $playerinfo['ship_id']);
        $maxloan = $score * $score * $ibank_loanlimit;

        $l_ibank_maxloanpercent = str_replace ("[ibank_percent]", $percent, $l_ibank_maxloanpercent);
        echo "<tr valign=top><td nowrap>" . $l_ibank_maxloanpercent . " :</td><td align=right>" . NUMBER ($maxloan) . " C</td></tr>";

        $factor = $ibank_loanfactor *= 100;
        $interest = $ibank_loaninterest *= 100;

        $l_ibank_loanrates = str_replace ("[factor]", $factor, $l_ibank_loanrates);
        $l_ibank_loanrates = str_replace ("[interest]", $interest, $l_ibank_loanrates);

        echo "<form action='igb.php?command=borrow' method=post>" .
             "<tr valign=top>" .
             "<td><br>" . $l_ibank_loanamount . " :</td>" .
             "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
             "<br><input class=term type=submit value='" . $l_ibank_borrow . "'></td>" .
             "</form>" .
             "<tr><td colspan=2 align=center>" . $l_ibank_loanrates;
    }

    echo "<tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>";
}
?>
