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
// File: includes/ibank_consolidate3.php

function ibank_consolidate3 ()
{
    global $db, $playerinfo;
    global $dplanet_id, $minimum, $maximum, $ibank_tconsolidate, $ibank_paymentfee;
    global $l_ibank_notenturns, $l_ibank_back, $l_ibank_logout, $l_ibank_transfersuccessful;
    global $l_ibank_currentpl, $l_ibank_in, $l_ibank_turncost, $l_ibank_unnamed;

    $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id=$dplanet_id");
    db_op_result ($db, $res, __LINE__, __FILE__);
    if (!$res || $res->EOF)
    {
        include_once './ibank_error.php';
        ibank_error ($l_ibank_errunknownplanet, "igb.php?command=transfer");
    }

    $dest = $res->fields;

    if (empty ($dest['name']))
    {
        $dest['name'] = $l_ibank_unnamed;
    }

    if ($dest['owner'] != $playerinfo['ship_id'])
    {
        include_once './ibank_error.php';
        ibank_error ($l_ibank_errnotyourplanet, "igb.php?command=transfer");
    }

    $minimum = strip_non_num ($minimum);
    $maximum = strip_non_num ($maximum);

    $query = "SELECT SUM(credits) as total, COUNT(*) AS count FROM {$db->prefix}planets WHERE owner=$playerinfo[ship_id] AND credits != 0";

    if ($minimum != 0)
    {
        $query .= " AND credits >= $minimum";
    }

    if ($maximum != 0)
    {
        $query .= " AND credits <= $maximum";
    }

    $query .= " AND planet_id != $dplanet_id";

    $res = $db->Execute($query);
    db_op_result ($db, $res, __LINE__, __FILE__);
    $amount = $res->fields;

    $fee = $ibank_paymentfee * $amount['total'];

    $tcost = ceil ($amount['count'] / $ibank_tconsolidate);
    $transfer = $amount['total'] - $fee;

    $cplanet = $transfer + $dest['credits'];

    if ($tcost > $playerinfo['turns'])
    {
        include_once './ibank_error.php';
        ibank_error ($l_ibank_notenturns, "igb.php?command=transfer");
    }

    echo "<tr><td colspan=2 align=center valign=top>" . $l_ibank_transfersuccessful . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td>" . $l_ibank_currentpl . " " . $dest['name'] . " " . $l_ibank_in . " " . $dest['sector_id'] . " :<br><br>" .
         $l_ibank_turncost . " :</td>" .
         "<td align=right>" . NUMBER ($cplanet) . " C<br><br>" .
         NUMBER ($tcost) . "</td>" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $l_ibank_back . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $l_ibank_logout . "</a></td>" .
         "</tr>";

    $query = "UPDATE {$db->prefix}planets SET credits=0 WHERE owner=$playerinfo[ship_id] AND credits != 0";

    if ($minimum != 0)
    {
        $query .= " AND credits >= $minimum";
    }

    if ($maximum != 0)
    {
        $query .= " AND credits <= $maximum";
    }

    $query .= " AND planet_id != $dplanet_id";

    $res = $db->Execute($query);
    db_op_result ($db, $res, __LINE__, __FILE__);
    $res = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits + $transfer WHERE planet_id=$dplanet_id");
    db_op_result ($db, $res, __LINE__, __FILE__);
    $res = $db->Execute("UPDATE {$db->prefix}ships SET turns=turns - $tcost WHERE ship_id = $playerinfo[ship_id]");
    db_op_result ($db, $res, __LINE__, __FILE__);
}
?>
