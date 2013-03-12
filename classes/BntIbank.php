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
// File: classes/BntIbank.php

if (strpos ($_SERVER['PHP_SELF'], 'BntIbank.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntIbank
{
    static function isLoanPending ($db, $ship_id, $ibank_lrate)
    {
        $res = $db->Execute ("SELECT loan, UNIX_TIMESTAMP(loantime) AS time FROM {$db->prefix}ibank_accounts WHERE ship_id = ?", array ($ship_id));
        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
        if ($res)
        {
            $account = $res->fields;

            if ($account['loan'] == 0)
            {
                return false;
            }

            $curtime = time ();
            $difftime = ($curtime - $account['time']) / 60;
            if ($difftime > $ibank_lrate)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

	static function deposit ($db, $lang, $account, $playerinfo, $langvars, $local_number_thousands_sep, $local_number_dec_point)
	{
	    // New database driven language entries
    	load_languages ($db, $lang, array ('igb'), $langvars);

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

    	echo "<tr><td height=53 colspan=2 align=center valign=top>" . $langvars['l_ibank_depositfunds'] . "<br>---------------------------------</td></tr>" .
	         "<tr valign=top>" .
    	     "<td height=30>" . $langvars['l_ibank_fundsavailable'] . " :</td>" .
        	 "<td align=right>" . number_format ($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) ." C<br></td>" .
         	 "</tr><tr valign=top>" .
         	 "<td height=90>" . $langvars['l_ibank_seldepositamount'] . " :</td><td align=right>" .
         	 "<form action='igb.php?command=deposit2' method=post>" .
         	 "<input class=term type=text size=15 maxlength=20 name=amount value=0>" .
	         "<br><br><input class=term type=submit value=" . $langvars['l_ibank_deposit'] . ">" .
    	     "</form>" .
        	 "</td></tr>" .
         	 "<tr>" .
         	 "  <td height=30  colspan=2 align=left>" .
	         "    <span style='color:\"#00ff00\";'>You can deposit only ". number_format ($credit_space, 0, $local_number_dec_point, $local_number_thousands_sep)." credits.</span><br>" .
    	     "  </td>" .
        	 "</tr>" .
	         "<tr valign=bottom>" .
    	     "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
        	 "</tr>";
	}
}
?>
