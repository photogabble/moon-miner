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
// File: includes/ibank_transfer.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_transfer.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function ibank_transfer ($db, $langvars)
{
    global $playerinfo, $ibank_min_turns;

    $res = $db->Execute ("SELECT character_name, ship_id FROM {$db->prefix}ships WHERE email not like '%@xenobe' AND ship_destroyed ='N' AND turns_used > ? ORDER BY character_name ASC", array ($ibank_min_turns));
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);

    $ships = array();
    while (!$res->EOF)
    {
        $ships[] = $res->fields;
        $res->MoveNext();
    }

    $res = $db->Execute ("SELECT name, planet_id, sector_id FROM {$db->prefix}planets WHERE owner=? ORDER BY sector_id ASC", array ($playerinfo['ship_id']));
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);
    while (!$res->EOF)
    {
        $planets[] = $res->fields;
        $res->MoveNext();
    }

    echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_transfertype'] . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<form action='igb.php?command=transfer2' method=post>" .
         "<td>" . $langvars['l_ibank_toanothership'] . " :<br><br>" .
         "<select class=term name=ship_id style='width:200px;'>";

    foreach ($ships as $ship)
    {
        echo "<option value='" . $ship['ship_id'] . "'>" . $ship['character_name'] . "</option>";
    }

    echo "</select></td><td valign=center align=right>" .
         "<input class=term type=submit name=shipt value='" . $langvars['l_ibank_shiptransfer'] . "'>" .
         "</form>" .
         "</td></tr>" .
         "<tr valign=top>" .
         "<td><br>" . $langvars['l_ibank_fromplanet'] . " :<br><br>" .
         "<form action='igb.php?command=transfer2' method=post>" .
         $langvars['l_ibank_source'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select class=term name=splanet_id>";

    if (isset ($planets))
    {
        foreach ($planets as $planet)
        {
            if (empty ($planet['name']))
            {
                $planet['name'] = $langvars['l_ibank_unnamed'];
            }
            echo "<option value=" . $planet['planet_id'] . ">" . $planet['name'] . " " . $langvars['l_ibank_in'] . " " . $planet['sector_id'] . "</option>";
        }
    }
    else
    {
        echo "<option value=none>" . $langvars['l_ibank_none'] . "</option>";
    }

    echo "</select><br>" . $langvars['l_ibank_destination'] . "<select class=term name=dplanet_id>";

    if (isset ($planets))
    {
        foreach ($planets as $planet)
        {
            if (empty ($planet['name']))
            {
                $planet['name'] = $langvars['l_ibank_unnamed'];
            }
            echo "<option value=" . $planet['planet_id'] . ">" . $planet['name'] . " " . $langvars['l_ibank_in'] . " " . $planet['sector_id'] . "</option>";
        }
    }
    else
    {
        echo "<option value=none>" . $langvars['l_ibank_none'] . "</option>";
    }

    echo "</select></td><td valign=center align=right>" .
         "<br><input class=term type=submit name=planett value='" . $langvars['l_ibank_planettransfer'] . "'>" .
         "</td></tr>" .
         "</form>";

// ---- begin Consol Credits form    // ---- added by Torr
    echo "<tr valign=top>" .
         "<td><br>" . $langvars['l_ibank_conspl'] . " :<br><br>" .
         "<form action='igb.php?command=consolidate' method=post>" .
         $langvars['l_ibank_destination'] . " <select class=term name=dplanet_id>";

    if (isset ($planets))
    {
        foreach ($planets as $planet)
        {
            if (empty ($planet['name']))
            {
                $planet['name'] = $langvars['l_ibank_unnamed'];
            }
            echo "<option value=" . $planet['planet_id'] . ">" . $planet['name'] . " " . $langvars['l_ibank_in'] . " " . $planet['sector_id'] . "</option>";
        }
    }
    else
    {
        echo "<option value=none>" . $langvars['l_ibank_none'] . "</option>";
    }

    echo "</select></td><td valign=top align=right>" .
         "<br><input class=term type=submit name=planetc value='" . $langvars['l_ibank_consolidate'] . "'>" .
         "</td></tr>" .
         "</form>";
// ---- End Consol Credits form ---

    echo "</form><tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
         "</tr>";
}
?>
