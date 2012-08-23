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

function IGB_transfer2()
{
    global $db, $playerinfo, $account, $ship_id, $splanet_id, $dplanet_id, $ibank_min_turns, $ibank_svalue;
    global $ibank_paymentfee, $ibank_trate;
    global $l_igb_sendyourself, $l_igb_unknowntargetship, $l_ibank_min_turns, $l_ibank_min_turns2;
    global $l_igb_mustwait, $l_igb_shiptransfer, $l_igb_igbaccount, $l_igb_maxtransfer;
    global $l_igb_unlimited, $l_igb_maxtransferpercent, $l_igb_transferrate, $l_igb_recipient;
    global $l_igb_seltransferamount, $l_igb_transfer, $l_igb_back, $l_igb_logout, $l_igb_in;
    global $l_igb_errplanetsrcanddest, $l_igb_errunknownplanet, $l_igb_unnamed;
    global $l_igb_errnotyourplanet, $l_igb_planettransfer, $l_igb_srcplanet, $l_igb_destplanet;
    global $l_igb_transferrate2, $l_igb_seltransferamount, $l_igb_errnobase;

    if (isset ($ship_id)) // Ship transfer
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=? AND ship_destroyed ='N' AND turns_used > ?;", array($ship_id, $ibank_min_turns));
        db_op_result ($db, $res, __LINE__, __FILE__);

        if ($playerinfo['ship_id'] == $ship_id)
        {
            ibank_error ($l_igb_sendyourself, "igb.php?command=transfer");
        }

        if (!$res instanceof ADORecordSet || $res->EOF)
        {
            ibank_error ($l_igb_unknowntargetship, "igb.php?command=transfer");
        }

        $target = $res->fields;

        if ($target['turns_used'] < $ibank_min_turns)
        {
            $l_ibank_min_turns = str_replace ("[ibank_min_turns]", $ibank_min_turns, $l_ibank_min_turns);
            $l_ibank_min_turns = str_replace ("[igb_target_char_name]", $target['character_name'], $l_ibank_min_turns);
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
            $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM {$db->prefix}IGB_transfers WHERE UNIX_TIMESTAMP(time) > ? AND source_id=? AND dest_id=?", array($curtime, $playerinfo['ship_id'], $target['ship_id']));
            db_op_result ($db, $res, __LINE__, __FILE__);
            if (!$res->EOF)
            {
                $time = $res->fields;
                $difftime = ($time['time'] - $curtime) / 60;
                $l_igb_mustwait = str_replace ("[igb_target_char_name]", $target['character_name'], $l_igb_mustwait);
                $l_igb_mustwait = str_replace ("[ibank_trate]", NUMBER ($ibank_trate), $l_igb_mustwait);
                $l_igb_mustwait = str_replace ("[igb_difftime]", NUMBER ($difftime), $l_igb_mustwait);
                ibank_error ($l_igb_mustwait, "igb.php?command=transfer");
            }
        }

        echo "<tr><td colspan=2 align=center valign=top>$l_igb_shiptransfer<br>---------------------------------</td></tr>" .
             "<tr valign=top><td>$l_igb_igbaccount :</td><td align=right>" . NUMBER($account['balance']) . " C</td></tr>";

        if ($ibank_svalue == 0)
        {
            echo "<tr valign=top><td>$l_igb_maxtransfer :</td><td align=right>$l_igb_unlimited</td></tr>";
        }
        else
        {
            $percent = $ibank_svalue * 100;
            $score = gen_score ($playerinfo['ship_id']);
            $maxtrans = $score * $score * $ibank_svalue;

            $l_igb_maxtransferpercent = str_replace("[igb_percent]", $percent, $l_igb_maxtransferpercent);
            echo "<tr valign=top><td nowrap>$l_igb_maxtransferpercent :</td><td align=right>" . NUMBER ($maxtrans) . " C</td></tr>";
        }

        $percent = $ibank_paymentfee * 100;

        $l_igb_transferrate = str_replace("[igb_num_percent]", NUMBER ($percent,1), $l_igb_transferrate);
        echo "<tr valign=top><td>$l_igb_recipient :</td><td align=right>$target[character_name]&nbsp;&nbsp;</td></tr>" .
             "<form action='igb.php?command=transfer3' method=POST>" .
             "<tr valign=top>" .
             "<td><br>$l_igb_seltransferamount :</td>" .
             "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
             "<br><input class=term type=submit value=$l_igb_transfer></td>" .
             "<input type=hidden name=ship_id value=$ship_id>" .
             "</form>" .
             "<tr><td colspan=2 align=center>" .
             "$l_igb_transferrate" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=transfer'>$l_igb_back</a></td><td align=right>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
             "</tr>";
    }
    else
    {
        if ($splanet_id == $dplanet_id)
        {
            ibank_error ($l_igb_errplanetsrcanddest, "igb.php?command=transfer");
        }

        $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id=$splanet_id");
        db_op_result ($db, $res, __LINE__, __FILE__);
        if (!$res || $res->EOF)
        {
            ibank_error ($l_igb_errunknownplanet, "igb.php?command=transfer");
        }

        $source = $res->fields;

        if (empty ($source['name']))
        {
            $source['name'] = $l_igb_unnamed;
        }

        $res = $db->Execute("SELECT name, credits, owner, sector_id, base FROM {$db->prefix}planets WHERE planet_id=$dplanet_id");
        db_op_result ($db, $res, __LINE__, __FILE__);
        if (!$res || $res->EOF)
        {
            ibank_error ($l_igb_errunknownplanet, "igb.php?command=transfer");
        }

        $dest = $res->fields;

        if (empty ($dest['name']))
        {
            $dest['name'] = $l_igb_unnamed;
        }

        if ($dest['base'] == 'N')
        {
            ibank_error ($l_igb_errnobase, "igb.php?command=transfer");
        }

        if ($source['owner'] != $playerinfo['ship_id'] || $dest['owner'] != $playerinfo['ship_id'])
        {
            ibank_error ($l_igb_errnotyourplanet, "igb.php?command=transfer");
        }

        $percent = $ibank_paymentfee * 100;

        $l_igb_transferrate2 = str_replace ("[igb_num_percent]", NUMBER ($percent,1), $l_igb_transferrate2);
        echo "<tr><td colspan=2 align=center valign=top>$l_igb_planettransfer<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td>$l_igb_srcplanet $source[name] $l_igb_in $source[sector_id] :" .
             "<td align=right>" . NUMBER ($source['credits']) . " C" .
             "<tr valign=top>" .
             "<td>$l_igb_destplanet $dest[name] $l_igb_in $dest[sector_id] :" .
             "<td align=right>" . NUMBER ($dest['credits']) . " C" .
             "<form action='igb.php?command=transfer3' method=POST>" .
             "<tr valign=top>" .
             "<td><br>$l_igb_seltransferamount :</td>" .
             "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
             "<br><input class=term type=submit value=$l_igb_transfer></td>" .
             "<input type=hidden name=splanet_id value=$splanet_id>" .
             "<input type=hidden name=dplanet_id value=$dplanet_id>" .
             "</form>" .
             "<tr><td colspan=2 align=center>" .
             "$l_igb_transferrate2" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=transfer'>$l_igb_back</a></td><td align=right>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
             "</tr>";
    }
}
?>
