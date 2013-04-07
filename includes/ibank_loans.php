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
    include_once './error.php';
}

function ibank_loans ($db)
{
    global $playerinfo, $account, $langvars;
    global $ibank_loanlimit, $ibank_loanfactor, $ibank_loaninterest, $local_number_dec_point, $local_number_thousands_sep;

    echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_loanstatus'] . "<br>---------------------------------</td></tr>" .
         "<tr valign=top><td>" . $langvars['l_ibank_shipaccount'] . " :</td><td align=right>" . number_format ($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C</td></tr>" .
         "<tr valign=top><td>" . $langvars['l_ibank_currentloan'] . " :</td><td align=right>" . number_format ($account['loan'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C</td></tr>";

    if ($account['loan'] != 0)
    {
        $curtime = time();
        $res = $db->Execute ("SELECT UNIX_TIMESTAMP(loantime) as time FROM {$db->prefix}ibank_accounts WHERE ship_id = ?", array ($playerinfo['ship_id']));
        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
        if (!$res->EOF)
        {
            $time = $res->fields;
        }

        $difftime = ($curtime - $time['time']) / 60;

        echo "<tr valign=top><td nowrap>" . $langvars['l_ibank_loantimeleft'] . " :</td>";

        if ($difftime > $ibank_lrate)
        {
            echo "<td align=right>" . $langvars['l_ibank_loanlate'] . "</td></tr>";
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

        $langvars['l_ibank_loanrates'] = str_replace ("[factor]", $factor, $langvars['l_ibank_loanrates']);
        $langvars['l_ibank_loanrates'] = str_replace ("[interest]", $interest, $langvars['l_ibank_loanrates']);

        echo "<form action='igb.php?command=repay' method=post>" .
             "<tr valign=top>" .
             "<td><br>" . $langvars['l_ibank_repayamount'] . " :</td>" .
             "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
             "<br><input class=term type=submit value='" . $langvars['l_ibank_repay'] . "'></td>" .
             "</form>" .
             "<tr><td colspan=2 align=center>" . $langvars['l_ibank_loanrates'];
    }
    else
    {
        $percent = $ibank_loanlimit * 100;
        include_once './includes/calc_score.php';
        $score = calc_score ($db, $playerinfo['ship_id']);
        $maxloan = $score * $score * $ibank_loanlimit;

        $langvars['l_ibank_maxloanpercent'] = str_replace ("[ibank_percent]", $percent, $langvars['l_ibank_maxloanpercent']);
        echo "<tr valign=top><td nowrap>" . $langvars['l_ibank_maxloanpercent'] . " :</td><td align=right>" . number_format ($maxloan, 0, $local_number_dec_point, $local_number_thousands_sep) . " C</td></tr>";

        $factor = $ibank_loanfactor *= 100;
        $interest = $ibank_loaninterest *= 100;

        $langvars['l_ibank_loanrates'] = str_replace ("[factor]", $factor, $langvars['l_ibank_loanrates']);
        $langvars['l_ibank_loanrates'] = str_replace ("[interest]", $interest, $langvars['l_ibank_loanrates']);

        echo "<form action='igb.php?command=borrow' method=post>" .
             "<tr valign=top>" .
             "<td><br>" . $langvars['l_ibank_loanamount'] . " :</td>" .
             "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
             "<br><input class=term type=submit value='" . $langvars['l_ibank_borrow'] . "'></td>" .
             "</form>" .
             "<tr><td colspan=2 align=center>" . $langvars['l_ibank_loanrates'];
    }

    echo "<tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
         "</tr>";
}
?>
