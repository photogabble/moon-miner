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
// File: includes/kick_off_planet.php

if (strpos ($_SERVER['PHP_SELF'], 'kick_off_planet.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

function kick_off_planet ($db, $ship_id, $whichteam)
{
    $result1 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE owner = ?", array ($ship_id));
    \bnt\dbop::dbresult ($db, $result1, __LINE__, __FILE__);

    if ($result1 instanceof ADORecordSet)
    {
        while (!$result1->EOF)
        {
            $row = $result1->fields;
            $result2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE on_planet = 'Y' AND planet_id = ? AND ship_id <> ?", array ($row['planet_id'], $ship_id));
            \bnt\dbop::dbresult ($db, $result2, __LINE__, __FILE__);
            if ($result2 instanceof ADORecordSet)
            {
                while (!$result2->EOF )
                {
                    $cur = $result2->fields;
                    $resa = $db->Execute("UPDATE {$db->prefix}ships SET on_planet = 'N',planet_id = '0' WHERE ship_id = ?", array ($cur['ship_id']));
                    \bnt\dbop::dbresult ($db, $resa, __LINE__, __FILE__);
                    \bnt\PlayerLog::writeLog ($db, $cur['ship_id'], LOG_PLANET_EJECT, $cur['sector'] ."|". $row['character_name']);
                    $result2->MoveNext();
                }
            }
            $result1->MoveNext();
        }
    }
}
?>
