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
// File: includes/ibank_transfer3.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_transfer3.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function ibank_transfer3 ($db)
{
    global $playerinfo, $account, $ship_id, $splanet_id, $dplanet_id, $ibank_min_turns, $ibank_svalue;
    global $ibank_paymentfee, $amount, $ibank_trate;
    global $l_ibank_errsendyourself, $l_ibank_unknowntargetship, $l_ibank_min_turns3, $l_ibank_min_turns4, $l_ibank_mustwait2;
    global $l_ibank_invalidtransferinput, $l_ibank_nozeroamount, $l_ibank_notenoughcredits, $l_ibank_notenoughcredits2, $l_ibank_in, $l_ibank_to;
    global $l_ibank_amounttoogreat, $l_ibank_transfersuccessful, $l_ibank_creditsto, $l_ibank_transferamount, $l_ibank_amounttransferred;
    global $l_ibank_transferfee, $l_ibank_ibankaccount, $l_ibank_back, $l_ibank_logout, $l_ibank_errplanetsrcanddest, $l_ibank_errnotyourplanet;
    global $l_ibank_errunknownplanet, $l_ibank_unnamed, $l_ibank_ctransferredfrom, $l_ibank_srcplanet, $l_ibank_destplanet;

    $amount = strip_non_num ($amount);

    if ($amount < 0)
    {
        $amount = 0;
    }

    if (isset ($ship_id)) //ship transfer
    {
        // Need to check again to prevent cheating by manual posts

        $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id = ? AND ship_destroyed ='N' AND turns_used > ?", array ($ship_id, $ibank_min_turns));
        db_op_result ($db, $res, __LINE__, __FILE__);

        if ($playerinfo['ship_id'] == $ship_id)
        {
            ibank_error ($l_ibank_errsendyourself, "igb.php?command=transfer");
        }

        if (!$res || $res->EOF)
        {
            ibank_error ($l_ibank_unknowntargetship, "igb.php?command=transfer");
        }

        $target = $res->fields;

        if ($target['turns_used'] < $ibank_min_turns)
        {
            $l_ibank_min_turns3 = str_replace("[ibank_min_turns]", $ibank_min_turns, $l_ibank_min_turns3);
            $l_ibank_min_turns3 = str_replace("[ibank_target_char_name]", $target['character_name'], $l_ibank_min_turns3);
            ibank_error ($l_ibank_min_turns3, "igb.php?command=transfer");
        }

        if ($playerinfo['turns_used'] < $ibank_min_turns)
        {
            $l_ibank_min_turns4 = str_replace("[ibank_min_turns]", $ibank_min_turns, $l_ibank_min_turns4);
            ibank_error ($l_ibank_min_turns4, "igb.php?command=transfer");
        }

        if ($ibank_trate > 0)
        {
            $curtime = time ();
            $curtime -= $ibank_trate * 60;
            $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM {$db->prefix}IGB_transfers WHERE UNIX_TIMESTAMP(time) > ? AND source_id = ? AND dest_id = ?", array ($curtime, $playerinfo['ship_id'], $target['ship_id']));
            db_op_result ($db, $res, __LINE__, __FILE__);
            if (!$res->EOF)
            {
                $time = $res->fields;
                $difftime = ($time['time'] - $curtime) / 60;
                $l_ibank_mustwait2 = str_replace("[ibank_target_char_name]", $target['character_name'], $l_ibank_mustwait2);
                $l_ibank_mustwait2 = str_replace("[ibank_trate]", NUMBER ($ibank_trate), $l_ibank_mustwait2);
                $l_ibank_mustwait2 = str_replace("[ibank_difftime]", NUMBER ($difftime), $l_ibank_mustwait2);
                ibank_error ($l_ibank_mustwait2, "igb.php?command=transfer");
            }
        }

        if (($amount * 1) != $amount)
        {
            ibank_error ($l_ibank_invalidtransferinput, "igb.php?command=transfer");
        }

        if ($amount == 0)
        {
            ibank_error ($l_ibank_nozeroamount, "igb.php?command=transfer");
        }

        if ($amount > $account['balance'])
        {
            ibank_error ($l_ibank_notenoughcredits, "igb.php?command=transfer");
        }

        if ($ibank_svalue != 0)
        {
            $percent = $ibank_svalue * 100;
            include_once './calc_score.php';
            $score = calc_score ($db, $playerinfo['ship_id']);
            $maxtrans = $score * $score * $ibank_svalue;

            if ($amount > $maxtrans)
            {
                ibank_error ($l_ibank_amounttoogreat, "igb.php?command=transfer");
            }
        }

        $account['balance'] -= $amount;
        $amount2 = $amount * $ibank_paymentfee;
        $transfer = $amount - $amount2;

        echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_transfersuccessful . "<br>---------------------------------</td></tr>" .
             "<tr valign=top><td colspan=2 align=center>" . NUMBER ($transfer) . " " . $l_ibank_creditsto . " " . $target['character_name'] . " .</tr>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_transferamount . " :</td><td align=right>" . NUMBER ($amount) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_transferfee . " :</td><td align=right>" . NUMBER ($amount2) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_amounttransferred . " :</td><td align=right>" . NUMBER ($transfer) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_ibankaccount . " :</td><td align=right>" . NUMBER ($account['balance']) . " C<br>" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
             "</tr>";

        $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance = balance - ? WHERE ship_id = ?", array ($amount, $playerinfo['ship_id']));
        db_op_result ($db, $resx, __LINE__, __FILE__);
        $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance = balance + ? WHERE ship_id = ?", array ($transfer, $target['ship_id']));
        db_op_result ($db, $resx, __LINE__, __FILE__);

        $resx = $db->Execute("INSERT INTO {$db->prefix}IGB_transfers VALUES (NULL, ?, ?, NOW(), ?)", array ($playerinfo['ship_id'], $target['ship_id'], $transfer));
        db_op_result ($db, $resx, __LINE__, __FILE__);
    }
    else
    {
        if ($splanet_id == $dplanet_id)
        {
            ibank_error ($l_ibank_errplanetsrcanddest, "igb.php?command=transfer");
        }

        $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id = ?", array ($splanet_id));
        db_op_result ($db, $res, __LINE__, __FILE__);
        if (!$res || $res->EOF)
        {
            ibank_error ($l_ibank_errunknownplanet, "igb.php?command=transfer");
        }

        $source = $res->fields;

        if (empty ($source['name']))
        {
            $source['name'] = $l_ibank_unnamed;
        }

        $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id = ?", array ($dplanet_id));
        db_op_result ($db, $res, __LINE__, __FILE__);
        if (!$res || $res->EOF)
        {
            ibank_error ($l_ibank_errunknownplanet, "igb.php?command=transfer");
        }

        $dest = $res->fields;

        if (empty ($dest['name']))
        {
            $dest['name'] = $l_ibank_unnamed;
        }

        if ($source['owner'] != $playerinfo['ship_id'] || $dest['owner'] != $playerinfo['ship_id'])
        {
            ibank_error ($l_ibank_errnotyourplanet, "igb.php?command=transfer");
        }

        if ($amount > $source['credits'])
        {
            ibank_error ($l_ibank_notenoughcredits2, "igb.php?command=transfer");
        }

        $percent = $ibank_paymentfee * 100;

        $source['credits'] -= $amount;
        $amount2 = $amount * $ibank_paymentfee;
        $transfer = $amount - $amount2;
        $dest['credits'] += $transfer;

        echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_transfersuccessful . "<br>---------------------------------</td></tr>" .
             "<tr valign=top><td colspan=2 align=center>" . NUMBER ($transfer) . " " . $l_ibank_ctransferredfrom . " " . $source['name'] . " " . $l_ibank_to . " " . $dest['name'] . ".</tr>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_transferamount . " :</td><td align=right>" . NUMBER ($amount) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_transferfee . " :</td><td align=right>" . NUMBER ($amount2) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_amounttransferred . " :</td><td align=right>" . NUMBER ($transfer) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_srcplanet . " " . $source['name'] . " " . $l_ibank_in . " " . $source['sector_id'] . " :</td><td align=right>" . NUMBER ($source['credits']) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_destplanet . " " . $dest['name'] . " " . $l_ibank_in . " " . $dest['sector_id'] . " :</td><td align=right>" . NUMBER ($dest['credits']) . " C<br>" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
             "</tr>";

        $resx = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits - ? WHERE planet_id = ?", array ($amount, $splanet_id));
        db_op_result ($db, $resx, __LINE__, __FILE__);
        $resx = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits + ? WHERE planet_id = ?", array ($transfer, $dplanet_id));
        db_op_result ($db, $resx, __LINE__, __FILE__);
    }
}
?>
