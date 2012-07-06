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
// File: includes/defence_vs_defence.php

if (preg_match("/defence_vs_defence.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function defence_vs_defence ($db, $ship_id)
{
    $result1 = $db->Execute("SELECT * from {$db->prefix}sector_defence where ship_id = $ship_id");
    db_op_result ($db, $result1, __LINE__, __FILE__, $db_logging);
    if ($result1 > 0)
    {
        while (!$result1->EOF)
        {
            $row = $result1->fields;
            $deftype = $row['defence_type'] == 'F' ? 'Fighters' : 'Mines';
            $qty = $row['quantity'];
            $result2 = $db->Execute("SELECT * from {$db->prefix}sector_defence where sector_id = $row[sector_id] and ship_id <> $ship_id ORDER BY quantity DESC");
            db_op_result ($db, $result2, __LINE__, __FILE__, $db_logging);
            if ($result2 > 0)
            {
                while (!$result2->EOF && $qty > 0)
                {
                    $cur = $result2->fields;
                    $targetdeftype = $cur['defence_type'] == 'F' ? $l_fighters : $l_mines;
                    if ($qty > $cur['quantity'])
                    {
                        $resa = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE defence_id = $cur[defence_id]");
                        db_op_result ($db, $resa, __LINE__, __FILE__, $db_logging);
                        $qty -= $cur['quantity'];
                        $resb = $db->Execute("UPDATE {$db->prefix}sector_defence SET quantity = $qty where defence_id = $row[defence_id]");
                        db_op_result ($db, $resb, __LINE__, __FILE__, $db_logging);
                        playerlog ($db, $cur['ship_id'], LOG_DEFS_DESTROYED, "$cur[quantity]|$targetdeftype|$row[sector_id]");
                        playerlog ($db, $row['ship_id'], LOG_DEFS_DESTROYED, "$cur[quantity]|$deftype|$row[sector_id]");
                    }
                    else
                    {
                        $resc = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE defence_id = $row[defence_id]");
                        db_op_result ($db, $resc, __LINE__, __FILE__, $db_logging);
                        $resd = $db->Execute("UPDATE {$db->prefix}sector_defence SET quantity=quantity - $qty WHERE defence_id = $cur[defence_id]");
                        db_op_result ($db, $resd, __LINE__, __FILE__, $db_logging);
                        playerlog ($db, $cur['ship_id'], LOG_DEFS_DESTROYED, "$qty|$targetdeftype|$row[sector_id]");
                        playerlog ($db, $row['ship_id'], LOG_DEFS_DESTROYED, "$qty|$deftype|$row[sector_id]");
                        $qty = 0;
                    }

                    $result2->MoveNext();
                }
            }

            $result1->MoveNext();
        }

        $rese = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE quantity <= 0");
        db_op_result ($db, $rese, __LINE__, __FILE__, $db_logging);
    }
}
?>
