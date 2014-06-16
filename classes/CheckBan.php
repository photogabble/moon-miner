<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: classes/CheckBan.php
//
// Returns a Boolean false when no account info or no ban found.
// Returns an array which contains the ban information when it has found something.
// Calling code needs to act on the returned information (boolean false or array of ban info).

namespace Bnt;

class CheckBan
{
    public static function isBanned($db, $lang, $langvars, $player_acc = false)
    {
        // Check to see if we have valid player info.
        if (is_bool($player_acc) && $player_acc == false)
        {
            // Nope we do not have valid player info so we return a Boolean false.
            // This needs to be a Boolean false not just a false.
            return (boolean) false;
        }

        // Check for IP Ban
        $ipbans_res = $db->Execute("SELECT * FROM {$db->prefix}bans WHERE (ban_type = ? AND ban_mask = ?) OR (ban_mask = ? AND ? != NULL);", array(IP_BAN, $_SERVER['REMOTE_ADDR'], $player_acc['ip_address'], $player_acc['ip_address']));
        Db::logDbErrors($db, $ipbans_res, __LINE__, __FILE__);
        if ($ipbans_res instanceof ADORecordSet && $ipbans_res->RecordCount() > 0)
        {
            // Ok, we have a ban record matching the players current IP Address, so return the BanType.
            return (array) $ipbans_res->fields;
        }

        // Check for ID Watch, Ban, Lock, 24H Ban etc linked to the platyers ShipID.
        $idbans_res = $db->Execute("SELECT * FROM {$db->prefix}bans WHERE ban_ship = ?;", array($player_acc['ship_id']));
        Db::logDbErrors($db, $idbans_res, __LINE__, __FILE__);
        if ($idbans_res instanceof ADORecordSet && $idbans_res->RecordCount() > 0)
        {
            // Now return the highest ban type (i.e. worst type of ban)
            $ban_type = array('ban_type' => 0);
            while (!$idbans_res->EOF)
            {
                if ($idbans_res->fields['ban_type'] > $ban_type['ban_type'])
                {
                    $ban_type = $idbans_res->fields;
                }
                $idbans_res->MoveNext();
            }

            return (array) $ban_type;
        }

        // Check for Multi Ban (IP, ID)
        $multiban_res = $db->Execute("SELECT * FROM {$db->prefix}bans WHERE ban_type = ? AND (ban_mask = ? OR ban_mask = ? OR ban_ship = ?)", array(MULTI_BAN, $player_acc['ip_address'], $_SERVER['REMOTE_ADDR'], $player_acc['ship_id']));
        Db::logDbErrors($db, $multiban_res, __LINE__, __FILE__);
        if ($multiban_res instanceof ADORecordSet && $multiban_res->RecordCount() > 0)
        {
            // Ok, we have a ban record matching the players current IP Address or their ShipID, so return the BanType.
            return (array) $multiban_res->fields;
        }

        // Well we got here, so we haven't found anything, so we return a Boolean false.
        return (boolean) false;
    }
}
?>
