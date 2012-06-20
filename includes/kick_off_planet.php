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

if (preg_match("/kick_off_planet.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function kick_off_planet ($ship_id, $whichteam)
{
    global $db, $dbtables;
    $result1 = $db->Execute("SELECT * from $dbtables[planets] where owner = '$ship_id' ");

    if ($result1 > 0)
    {
        while (!$result1->EOF)
        {
            $row = $result1->fields;
            $result2 = $db->Execute("SELECT * from $dbtables[ships] where on_planet = 'Y' and planet_id = '$row[planet_id]' and ship_id <> '$ship_id' ");
            if ($result2 > 0)
            {
                while (!$result2->EOF )
                {
                    $cur = $result2->fields;
                    $db->Execute("UPDATE $dbtables[ships] SET on_planet = 'N',planet_id = '0' WHERE ship_id='$cur[ship_id]'");
                    playerlog ($db, $dbtables, $cur['ship_id'], LOG_PLANET_EJECT, "$cur[sector]|$row[character_name]");
                    $result2->MoveNext();
                }
            }

            $result1->MoveNext();
        }
   }
}
?>
