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
// File: ibank_consolidate.php

function ibank_consolidate()
{
    global $db, $playerinfo, $account;
    global $l_igb_errunknownplanet, $l_igb_errnotyourplanet, $l_igb_transferrate3;
    global $l_igb_planettransfer, $l_igb_destplanet, $l_igb_in, $ibank_tconsolidate;
    global $dplanet_id, $l_igb_unnamed, $l_igb_currentpl, $l_igb_consolrates;
    global $l_igb_minimum, $l_igb_maximum, $l_igb_back, $l_igb_logout;
    global $l_igb_planetconsolidate, $l_igb_compute, $ibank_paymentfee;

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate3 = str_replace("[igb_num_percent]", NUMBER ($percent, 1), $l_igb_transferrate3);
    $l_igb_transferrate3 = str_replace("[nbplanets]", $ibank_tconsolidate, $l_igb_transferrate3);

    echo "<tr><td colspan=2 align=center valign=top>" . $l_igb_planetconsolidate . "<br>---------------------------------</td></tr>" .
         "<form action='igb.php?command=consolidate2' method=POST>" .
         "<tr valign=top>" .
         "<td colspan=2>" . $l_igb_consolrates . " :</td>" .
         "<tr valign=top>" .
         "<td>" . $l_igb_minimum . " :<br>" .
         "<br>" . $l_igb_maximum . " :</td>" .
         "<td align=right>" .
         "<input class=term type=text size=15 maxlength=20 name=minimum value=0><br><br>" .
         "<input class=term type=text size=15 maxlength=20 name=maximum value=0><br><br>" .
         "<input class=term type=submit value=\"" . $l_igb_compute . "\"></td>" .
         "<input type=hidden name=dplanet_id value=" . $dplanet_id . ">" .
         "</form>" .
         "<tr><td colspan=2 align=center>" .
         "$l_igb_transferrate3" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=transfer'>" . $l_igb_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_igb_logout . "</a></td>" .
         "</tr>";
}
?>
