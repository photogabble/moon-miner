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
// File: classes/BntToll.php

if (strpos ($_SERVER['PHP_SELF'], 'BntToll.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntToll
{
	static function distribute ($db, $sector, $toll, $total_fighters)
	{
    	$result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=? AND defence_type ='F'", array ($sector));
    	DbOp::dbResult ($db, $result3, __LINE__, __FILE__);

	    // Put the defence information into the array "defenceinfo"
    	if ($result3 instanceof ADORecordSet)
	    {
    	    while (!$result3->EOF)
        	{
            	$row = $result3->fields;
	            $toll_amount = round (($row['quantity'] / $total_fighters) * $toll);
    	        $resa = $db->Execute ("UPDATE {$db->prefix}ships SET credits=credits + ? WHERE ship_id = ?", array ($toll_amount, $row['ship_id']));
        	    DbOp::dbResult ($db, $resa, __LINE__, __FILE__);
            	PlayerLog::writeLog ($db, $row['ship_id'], LOG_TOLL_RECV, "$toll_amount|$sector");
	            $result3->MoveNext ();
    	    }
	    }
	}
}
?>
