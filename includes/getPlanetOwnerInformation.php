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
// File: includes/getPlanetOwnerInformation.php

if (preg_match("/getPlanetOwnerInformation.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function getPlanetOwnerInformation ($db = NULL, $dbtables, $planetID = NULL, &$ownerInfo = NULL)
{
    $ownerInfo = NULL;
    if(!is_null($planetID) && is_numeric($planetID) && $planetID >0)
    {
        $sql  = "SELECT ship_id, character_name, team FROM $dbtables[planets] ";
        $sql .= "LEFT JOIN $dbtables[ships] ON $dbtables[ships].ship_id = $dbtables[planets].owner ";
        $sql .= "WHERE $dbtables[planets].planet_id=?;";
        $res = $db->Execute($sql, array($planetID));
        if($res->RecordCount() > 0 )
        {
            $ownerInfo = (array)$res->fields;
            return true;
        }
    }
    return false;
}
?>
