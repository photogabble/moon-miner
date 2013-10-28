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

// Todo: Database escaping on the query with minimum / maximum credits
if (strpos ($_SERVER['PHP_SELF'], 'ibank_consolidate3.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function ibank_consolidate3 ($db, $langvars, $playerinfo)
{
    global $dplanet_id, $minimum, $maximum, $ibank_tconsolidate, $ibank_paymentfee;

    $res = $db->Execute ("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id = ?", array ($dplanet_id));
    BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
    if (!$res || $res->EOF)
    {
        include_once './includes/ibank_error.php';
        ibank_error ($active_template, $active_template, $langvars, $langvars['l_ibank_errunknownplanet'], "igb.php?command=transfer");
    }

    $dest = $res->fields;

    if (empty ($dest['name']))
    {
        $dest['name'] = $langvars['l_ibank_unnamed'];
    }

    if ($dest['owner'] != $playerinfo['ship_id'])
    {
        include_once './includes/ibank_error.php';
        ibank_error ($active_template, $active_template, $langvars, $langvars['l_ibank_errnotyourplanet'], "igb.php?command=transfer");
    }

    $minimum = preg_replace ("/[^0-9]/", "", $minimum);
    $maximum = preg_replace ("/[^0-9]/", "", $maximum);

    $query = "SELECT SUM(credits) as total, COUNT(*) AS count FROM {$db->prefix}planets WHERE owner=? AND credits != 0 AND planet_id != ?";

    if ($minimum != 0)
    {
        $query .= " AND credits >= $minimum";
    }

    if ($maximum != 0)
    {
        $query .= " AND credits <= $maximum";
    }

    $res = $db->Execute ($query, array ($playerinfo['ship_id'], $dplanet_id));
    BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
    $amount = $res->fields;

    $fee = $ibank_paymentfee * $amount['total'];

    $tcost = ceil ($amount['count'] / $ibank_tconsolidate);
    $transfer = $amount['total'] - $fee;

    $cplanet = $transfer + $dest['credits'];

    if ($tcost > $playerinfo['turns'])
    {
        include_once './includes/ibank_error.php';
        ibank_error ($active_template, $active_template, $langvars, $langvars['l_ibank_notenturns'], "igb.php?command=transfer");
    }

    echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_transfersuccessful'] . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td>" . $langvars['l_ibank_currentpl'] . " " . $dest['name'] . " " . $langvars['l_ibank_in'] . " " . $dest['sector_id'] . " :<br><br>" .
         $langvars['l_ibank_turncost'] . " :</td>" .
         "<td align=right>" . number_format ($cplanet, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C<br><br>" .
         number_format ($tcost, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . "</td>" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout ']. "</a></td>" .
         "</tr>";

    $query = "UPDATE {$db->prefix}planets SET credits=0 WHERE owner=? AND credits != 0 AND planet_id != ?";

    if ($minimum != 0)
    {
        $query .= " AND credits >= $minimum";
    }

    if ($maximum != 0)
    {
        $query .= " AND credits <= $maximum";
    }

    $res = $db->Execute ($query, array ($playerinfo['ship_id'], $dplanet_id));
    BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
    $res = $db->Execute ("UPDATE {$db->prefix}planets SET credits=credits + ? WHERE planet_id=?", array ($transfer, $dplanet_id));
    BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
    $res = $db->Execute ("UPDATE {$db->prefix}ships SET turns=turns - ? WHERE ship_id=?", array ($tcost, $playerinfo['ship_id']));
    BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
}
?>
