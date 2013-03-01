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

if (strpos ($_SERVER['PHP_SELF'], 'defence_vs_defence.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

function defence_vs_defence ($db, $ship_id, $langvars)
{
    $result1 = $db->Execute("SELECT * FROM {$db->prefix}sector_defence WHERE ship_id = ?;", array ($ship_id));
    \bnt\dbop::dbresult ($db, $result1, __LINE__, __FILE__);

    if ($result1 instanceof ADORecordSet)
    {
        while (!$result1->EOF)
        {
            $row = $result1->fields;
            $deftype = $row['defence_type'] == 'F' ? 'Fighters' : 'Mines';
            $qty = $row['quantity'];
            $result2 = $db->Execute("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id = ? AND ship_id <> ? ORDER BY quantity DESC", array ($row['sector_id'], $ship_id));
            \bnt\dbop::dbresult ($db, $result2, __LINE__, __FILE__);
            if ($result2 instanceof ADORecordSet)
            {
                while (!$result2->EOF && $qty > 0)
                {
                    $cur = $result2->fields;
                    $targetdeftype = $cur['defence_type'] == 'F' ? $langvars['l_fighters'] : $langvars['l_mines'];
                    if ($qty > $cur['quantity'])
                    {
                        $resa = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE defence_id = ?", array ($cur['defence_id']));
                        \bnt\dbop::dbresult ($db, $resa, __LINE__, __FILE__);
                        $qty -= $cur['quantity'];
                        $resb = $db->Execute("UPDATE {$db->prefix}sector_defence SET quantity = ? WHERE defence_id = ?", array ($qty, $row['defence_id']));
                        \bnt\dbop::dbresult ($db, $resb, __LINE__, __FILE__);
                        \bnt\PlayerLog::writeLog ($db, $cur['ship_id'], LOG_DEFS_DESTROYED, $cur['quantity'] ."|". $targetdeftype ."|". $row['sector_id']);
                        \bnt\PlayerLog::writeLog ($db, $row['ship_id'], LOG_DEFS_DESTROYED, $cur['quantity'] ."|". $deftype ."|". $row['sector_id']);
                    }
                    else
                    {
                        $resc = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE defence_id = ?", array ($row['defence_id']));
                        \bnt\dbop::dbresult ($db, $resc, __LINE__, __FILE__);
                        $resd = $db->Execute("UPDATE {$db->prefix}sector_defence SET quantity=quantity - ? WHERE defence_id = ?", array ($qty, $cur['defence_id']));
                        \bnt\dbop::dbresult ($db, $resd, __LINE__, __FILE__);
                        \bnt\PlayerLog::writeLog ($db, $cur['ship_id'], LOG_DEFS_DESTROYED, $qty ."|". $targetdeftype ."|". $row['sector_id']);
                        \bnt\PlayerLog::writeLog ($db, $row['ship_id'], LOG_DEFS_DESTROYED, $qty ."|". $deftype ."|". $row['sector_id']);
                        $qty = 0;
                    }
                    $result2->MoveNext();
                }
            }
            $result1->MoveNext();
        }

        $rese = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE quantity <= 0");
        \bnt\dbop::dbresult ($db, $rese, __LINE__, __FILE__);
    }
}
?>
