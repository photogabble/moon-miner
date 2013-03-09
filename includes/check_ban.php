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
// File: includes/check_ban.php

if (strpos ($_SERVER['PHP_SELF'], 'check_ban.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

// All defines _may_ need to be put into the global_defines.php file.
define ('ID_WATCH',     0x00, true);    // Player flagged as being watched.
define ('ID_LOCKED',    0x01, true);    // Player flagged as being Locked.
define ('24HR_BAN',     0x02, true);    // Player flagged with a 24 Hour Ban.
define ('ID_BAN',       0x03, true);    // Player flagged as banned by ShipID.
define ('IP_BAN',       0x04, true);    // Player flagged as banned by IP Address.
define ('MULTI_BAN',    0x05, true);    // Player flagged as banned by either IP or ShipID.

// Returns a Boolean False when no account info or no ban found.
// Returns an array which contains the ban information when it has found something.
// Calling code needs to act on the returned information (boolean false or array of ban info).
function check_ban ($db, $lang, $langvars, $player_acc = false)
{
    // Check to see if we have valid player info.
    if(is_bool($player_acc) && $player_acc == false)
    {
        // Nope we do not have valid player info so we return a Boolean False.
        // This needs to be a Boolean false not just a false.
        return (boolean) false;
    }

    // Check for IP Ban
    $rs = $db->Execute ("SELECT * FROM {$db->prefix}bans WHERE (ban_type = ? AND ban_mask = ?) OR (ban_mask = ? AND ? != NULL);", array(IP_BAN, $_SERVER['REMOTE_ADDR'], $player_acc['ip_address'], $player_acc['ip_address']));
    \bnt\dbop::dbresult ($db, $rs, __LINE__, __FILE__);
    if($rs instanceof ADORecordSet && $rs->RecordCount() > 0)
    {
        // Ok, we have a ban record matching the players current IP Address, so return the BanType.
        return (array) $rs->fields;
    }

    // Check for ID Watch, Ban, Lock, 24H Ban etc linked to the platyers ShipID.
    $rs = $db->Execute ("SELECT * FROM {$db->prefix}bans WHERE ban_ship = ?;", array($player_acc['ship_id']));
    \bnt\dbop::dbresult ($db, $rs, __LINE__, __FILE__);
    if($rs instanceof ADORecordSet && $rs->RecordCount() > 0)
    {
        // Now return the highest ban type (i.e. worst type of ban)
        $ban_type = array("ban_type"=>0);
        while (!$rs->EOF)
        {
            if ($rs->fields['ban_type'] > $ban_type['ban_type'])
            {
                $ban_type = $rs->fields;
            }
            $rs->MoveNext();
        }

        return (array) $ban_type;
    }

    // Check for Multi Ban (IP, ID)
    $rs = $db->Execute ("SELECT * FROM {$db->prefix}bans WHERE ban_type = ? AND (ban_mask = ? OR ban_mask = ? OR ban_ship = ?)", array(MULTI_BAN, $player_acc['ip_address'], $_SERVER['REMOTE_ADDR'], $player_acc['ship_id']));
    \bnt\dbop::dbresult ($db, $rs, __LINE__, __FILE__);
    if($rs instanceof ADORecordSet && $rs->RecordCount() > 0)
    {
        // Ok, we have a ban record matching the players current IP Address or their ShipID, so return the BanType.
        return (array) $rs->fields;
    }

    // Well we got here, so we haven't found anything, so we return a Boolean false.
    return (boolean) false;
}

?>
