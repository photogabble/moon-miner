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
// File: includes/ibank_transfer2.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_transfer2.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function ibank_transfer2 ($db)
{
    global $playerinfo, $account, $ship_id, $splanet_id, $dplanet_id, $ibank_min_turns, $ibank_svalue;
    global $ibank_paymentfee, $ibank_trate;
    global $l_ibank_sendyourself, $l_ibank_unknowntargetship, $l_ibank_min_turns2;
    global $l_ibank_mustwait, $l_ibank_shiptransfer, $l_ibank_ibankaccount, $l_ibank_maxtransfer;
    global $l_ibank_unlimited, $l_ibank_maxtransferpercent, $l_ibank_transferrate, $l_ibank_recipient;
    global $l_ibank_seltransferamount, $l_ibank_transfer, $l_ibank_back, $l_ibank_logout, $l_ibank_in;
    global $l_ibank_errplanetsrcanddest, $l_ibank_errunknownplanet, $l_ibank_unnamed;
    global $l_ibank_errnotyourplanet, $l_ibank_planettransfer, $l_ibank_srcplanet, $l_ibank_destplanet;
    global $l_ibank_transferrate2, $l_ibank_seltransferamount, $l_ibank_errnobase;

    if (isset ($ship_id)) // Ship transfer
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=? AND ship_destroyed ='N' AND turns_used > ?;", array ($ship_id, $ibank_min_turns));
        db_op_result ($db, $res, __LINE__, __FILE__);

        if ($playerinfo['ship_id'] == $ship_id)
        {
            ibank_error ($l_ibank_sendyourself, "igb.php?command=transfer");
        }

        if (!$res instanceof ADORecordSet || $res->EOF)
        {
            ibank_error ($l_ibank_unknowntargetship, "igb.php?command=transfer");
        }

        $target = $res->fields;

        if ($target['turns_used'] < $ibank_min_turns)
        {
            $l_ibank_min_turns = str_replace ("[ibank_min_turns]", $ibank_min_turns, $l_ibank_min_turns);
            $l_ibank_min_turns = str_replace ("[ibank_target_char_name]", $target['character_name'], $l_ibank_min_turns);
            ibank_error ($l_ibank_min_turns, "igb.php?command=transfer");
        }

        if ($playerinfo['turns_used'] < $ibank_min_turns)
        {
            $l_ibank_min_turns2 = str_replace ("[ibank_min_turns]", $ibank_min_turns, $l_ibank_min_turns2);
            ibank_error ($l_ibank_min_turns2, "igb.php?command=transfer");
        }

        if ($ibank_trate > 0)
        {
            $curtime = time();
            $curtime -= $ibank_trate * 60;
            $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM {$db->prefix}IGB_transfers WHERE UNIX_TIMESTAMP(time) > ? AND source_id = ? AND dest_id = ?", array ($curtime, $playerinfo['ship_id'], $target['ship_id']));
            db_op_result ($db, $res, __LINE__, __FILE__);
            if (!$res->EOF)
            {
                $time = $res->fields;
                $difftime = ($time['time'] - $curtime) / 60;
                $l_ibank_mustwait = str_replace ("[ibank_target_char_name]", $target['character_name'], $l_ibank_mustwait);
                $l_ibank_mustwait = str_replace ("[ibank_trate]", NUMBER ($ibank_trate), $l_ibank_mustwait);
                $l_ibank_mustwait = str_replace ("[ibank_difftime]", NUMBER ($difftime), $l_ibank_mustwait);
                ibank_error ($l_ibank_mustwait, "igb.php?command=transfer");
            }
        }

        echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_shiptransfer . "<br>---------------------------------</td></tr>" .
             "<tr valign=top><td>" . $l_ibank_ibankaccount . " :</td><td align=right>" . NUMBER ($account['balance']) . " C</td></tr>";

        if ($ibank_svalue == 0)
        {
            echo "<tr valign=top><td>" . $l_ibank_maxtransfer . " :</td><td align=right>" . $l_ibank_unlimited . "</td></tr>";
        }
        else
        {
            $percent = $ibank_svalue * 100;
            include_once './gen_score.php';
            $score = gen_score ($db, $playerinfo['ship_id']);
            $maxtrans = $score * $score * $ibank_svalue;

            $l_ibank_maxtransferpercent = str_replace("[ibank_percent]", $percent, $l_ibank_maxtransferpercent);
            echo "<tr valign=top><td nowrap>" . $l_ibank_maxtransferpercent . " :</td><td align=right>" . NUMBER ($maxtrans) . " C</td></tr>";
        }

        $percent = $ibank_paymentfee * 100;

        $l_ibank_transferrate = str_replace("[ibank_num_percent]", NUMBER ($percent, 1), $l_ibank_transferrate);
        echo "<tr valign=top><td>" . $l_ibank_recipient . " :</td><td align=right>" . $target['character_name'] . "&nbsp;&nbsp;</td></tr>" .
             "<form action='igb.php?command=transfer3' method=post>" .
             "<tr valign=top>" .
             "<td><br>" . $l_ibank_seltransferamount . " :</td>" .
             "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
             "<br><input class=term type=submit value='" . $l_ibank_transfer . "'></td>" .
             "<input type=hidden name=ship_id value='" . $ship_id . "'>" .
             "</form>" .
             "<tr><td colspan=2 align=center>" . $l_ibank_transferrate .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=transfer'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
             "</tr>";
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

        $res = $db->Execute("SELECT name, credits, owner, sector_id, base FROM {$db->prefix}planets WHERE planet_id = ?", array ($dplanet_id));
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

        if ($dest['base'] == 'N')
        {
            ibank_error ($l_ibank_errnobase, "igb.php?command=transfer");
        }

        if ($source['owner'] != $playerinfo['ship_id'] || $dest['owner'] != $playerinfo['ship_id'])
        {
            ibank_error ($l_ibank_errnotyourplanet, "igb.php?command=transfer");
        }

        $percent = $ibank_paymentfee * 100;

        $l_ibank_transferrate2 = str_replace ("[ibank_num_percent]", NUMBER ($percent, 1), $l_ibank_transferrate2);
        echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_planettransfer . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_srcplanet . " " . $source['name'] . " " . $l_ibank_in . " " . $source['sector_id'] . " :" .
             "<td align=right>" . NUMBER ($source['credits']) . " C" .
             "<tr valign=top>" .
             "<td>" . $l_ibank_destplanet . " " . $dest['name'] . " " . $l_ibank_in . " " . $dest['sector_id'] . " :" .
             "<td align=right>" . NUMBER ($dest['credits']) . " C" .
             "<form action='igb.php?command=transfer3' method=post>" .
             "<tr valign=top>" .
             "<td><br>" . $l_ibank_seltransferamount . " :</td>" .
             "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
             "<br><input class=term type=submit value='" . $l_ibank_transfer . "'></td>" .
             "<input type=hidden name=splanet_id value='" . $splanet_id . "'>" .
             "<input type=hidden name=dplanet_id value='" . $dplanet_id . "'>" .
             "</form>" .
             "<tr><td colspan=2 align=center>" . $l_ibank_transferrate2 .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=transfer'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
             "</tr>";
    }
}
?>
