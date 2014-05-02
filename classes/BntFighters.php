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
// File: classes/BntFighters.php

if (strpos ($_SERVER['PHP_SELF'], 'BntFighters.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntFighters
{
    public static function destroy($db, $sector, $num_fighters)
    {
        $secdef_res = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=? AND defence_type ='F' ORDER BY quantity ASC", array ($sector));
        BntDb::logDbErrors ($db, $secdef_res, __LINE__, __FILE__);

        // Put the defence information into the array "defenceinfo"
        if ($secdef_res instanceof ADORecordSet)
        {
            while (!$secdef_res->EOF && $num_fighters > 0)
            {
                $row = $secdef_res->fields;
                if ($row['quantity'] > $num_fighters)
                {
                    $update_res = $db->Execute("UPDATE {$db->prefix}sector_defence SET quantity=quantity - ? WHERE defence_id = ?", array ($num_fighters, $row['defence_id']));
                    BntDb::logDbErrors ($db, $update_res, __LINE__, __FILE__);
                    $num_fighters = 0;
                }
                else
                {
                    $update_res = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE defence_id = ?", array ($row['defence_id']));
                    BntDb::logDbErrors ($db, $update_res, __LINE__, __FILE__);
                    $num_fighters -= $row['quantity'];
                }
                $secdef_res->MoveNext();
            }
        }
    }
}
?>
