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
// File: classes/BntDefense.php

if (strpos ($_SERVER['PHP_SELF'], 'BntDefense.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntDefense
{
    static function defence_vs_defence ($db, $ship_id, $langvars)
    {
        $secdef_result = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE ship_id = ?;", array ($ship_id));
        BntDb::logDbErrors ($db, $secdef_result, __LINE__, __FILE__);

        if ($secdef_result instanceof ADORecordSet)
        {
            while (!$secdef_result->EOF)
            {
                $row = $secdef_result->fields;
                $deftype = $row['defence_type'] == 'F' ? 'Fighters' : 'Mines';
                $qty = $row['quantity'];
                $other_secdef_res = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id = ? AND ship_id <> ? ORDER BY quantity DESC", array ($row['sector_id'], $ship_id));
                BntDb::logDbErrors ($db, $other_secdef_res, __LINE__, __FILE__);
                if ($other_secdef_res instanceof ADORecordSet)
                {
                    while (!$other_secdef_res->EOF && $qty > 0)
                    {
                        $cur = $other_secdef_res->fields;
                        $targetdeftype = $cur['defence_type'] == 'F' ? $langvars['l_fighters'] : $langvars['l_mines'];
                        if ($qty > $cur['quantity'])
                        {
                            $del_secdef_res = $db->Execute ("DELETE FROM {$db->prefix}sector_defence WHERE defence_id = ?", array ($cur['defence_id']));
                            BntDb::logDbErrors ($db, $del_secdef_res, __LINE__, __FILE__);
                            $qty -= $cur['quantity'];
                            $up_secdef_res = $db->Execute ("UPDATE {$db->prefix}sector_defence SET quantity = ? WHERE defence_id = ?", array ($qty, $row['defence_id']));
                            BntDb::logDbErrors ($db, $up_secdef_res, __LINE__, __FILE__);
                            BntPlayerLog::writeLog ($db, $cur['ship_id'], LOG_DEFS_DESTROYED, $cur['quantity'] ."|". $targetdeftype ."|". $row['sector_id']);
                            BntPlayerLog::writeLog ($db, $row['ship_id'], LOG_DEFS_DESTROYED, $cur['quantity'] ."|". $deftype ."|". $row['sector_id']);
                        }
                        else
                        {
                            $del_secdef_res2 = $db->Execute ("DELETE FROM {$db->prefix}sector_defence WHERE defence_id = ?", array ($row['defence_id']));
                            BntDb::logDbErrors ($db, $del_secdef_res2, __LINE__, __FILE__);

                            $up_secdef_res2 = $db->Execute ("UPDATE {$db->prefix}sector_defence SET quantity=quantity - ? WHERE defence_id = ?", array ($qty, $cur['defence_id']));
                            BntDb::logDbErrors ($db, $up_secdef_res2, __LINE__, __FILE__);
                            BntPlayerLog::writeLog ($db, $cur['ship_id'], LOG_DEFS_DESTROYED, $qty ."|". $targetdeftype ."|". $row['sector_id']);
                            BntPlayerLog::writeLog ($db, $row['ship_id'], LOG_DEFS_DESTROYED, $qty ."|". $deftype ."|". $row['sector_id']);
                            $qty = 0;
                        }
                        $other_secdef_res->MoveNext();
                    }
                }
                $secdef_result->MoveNext();
            }
            $del_secdef_res3 = $db->Execute ("DELETE FROM {$db->prefix}sector_defence WHERE quantity <= 0");
            BntDb::logDbErrors ($db, $del_secdef_res3, __LINE__, __FILE__);
        }
    }
}
?>
