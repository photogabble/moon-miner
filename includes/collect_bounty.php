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
// File: includes/collect_bounty.php

if (strpos ($_SERVER['PHP_SELF'], 'collect_bounty.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

function collect_bounty ($db, $attacker, $bounty_on)
{
    $res = $db->Execute("SELECT * FROM {$db->prefix}bounty,{$db->prefix}ships WHERE bounty_on = ? AND bounty_on = ship_id and placed_by <> 0", array ($bounty_on));
    if ($res)
    {
        while (!$res->EOF)
        {
            $bountydetails = $res->fields;
            if ($res->fields['placed_by'] == 0)
            {
                $placed = $langvars['l_by_thefeds'];
            }
            else
            {
                $res2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id = ?", array ($bountydetails['placed_by']));
                \bnt\dbop::dbresult ($db, $res2, __LINE__, __FILE__);
                $placed = $res2->fields['character_name'];
            }

            $update = $db->Execute("UPDATE {$db->prefix}ships SET credits = credits + ? WHERE ship_id = ?", array ($bountydetails['amount'], $attacker));
            \bnt\dbop::dbresult ($db, $update, __LINE__, __FILE__);
            $delete = $db->Execute("DELETE FROM {$db->prefix}bounty WHERE bounty_id = ?", array ($bountydetails['bounty_id']));
            \bnt\dbop::dbresult ($db, $delete, __LINE__, __FILE__);

            \bnt\PlayerLog::writeLog ($db, $attacker, LOG_BOUNTY_CLAIMED, "$bountydetails[amount]|$bountydetails[character_name]|$placed");
            \bnt\PlayerLog::writeLog ($db, $bountydetails['placed_by'], LOG_BOUNTY_PAID, "$bountydetails[amount]|$bountydetails[character_name]");
            $res->MoveNext();
        }
   }
   $resa = $db->Execute("DELETE FROM {$db->prefix}bounty WHERE bounty_on = ?", array ($bounty_on));
   \bnt\dbop::dbresult ($db, $resa, __LINE__, __FILE__);
}
?>
