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
// File: classes/BntShip.php

class BntShip
{
    public static function leavePlanet($db, $ship_id)
    {
        $own_pl_result = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE owner = ?", array ($ship_id));
        BntDb::logDbErrors ($db, $own_pl_result, __LINE__, __FILE__);

        if ($own_pl_result instanceof ADORecordSet)
        {
            while (!$own_pl_result->EOF)
            {
                $row = $own_pl_result->fields;
                $on_pl_result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE on_planet = 'Y' AND planet_id = ? AND ship_id <> ?", array ($row['planet_id'], $ship_id));
                BntDb::logDbErrors ($db, $on_pl_result, __LINE__, __FILE__);
                if ($on_pl_result instanceof ADORecordSet)
                {
                    while (!$on_pl_result->EOF )
                    {
                        $cur = $on_pl_result->fields;
                        $uppl_res = $db->Execute ("UPDATE {$db->prefix}ships SET on_planet = 'N',planet_id = '0' WHERE ship_id = ?", array ($cur['ship_id']));
                        BntDb::logDbErrors ($db, $uppl_res, __LINE__, __FILE__);
                        BntPlayerLog::writeLog ($db, $cur['ship_id'], LOG_PLANET_EJECT, $cur['sector'] ."|". $row['character_name']);
                        $on_pl_result->MoveNext();
                    }
                }
                $own_pl_result->MoveNext();
            }
        }
    }
}
?>
