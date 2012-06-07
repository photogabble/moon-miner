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
// File: move.php

include("config.php");
updatecookie();
include("languages/$lang");
$title=$l_move_title;
include("header.php");

// Check to see if the user is logged in
if (checklogin())
{
    die();
}

//Retrieve the user and ship information
$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE email='$username'");
// Put the player information into the array: "playerinfo"
$playerinfo=$result->fields;

// Check to see if the player has less than one turn available
// and if so return to the main menu
if ($playerinfo['turns']<1)
{
    echo "$l_move_turn<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
}

// Retrieve all the sector information about the current sector
$result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
// Put the sector information into the array "sectorinfo"
$sectorinfo=$result2->fields;

if (!isset($_GET['sector']))
{
    $_GET['sector'] = '';
}

// Retrive all the warp links out of the current sector
$result3 = $db->Execute ("SELECT * FROM $dbtables[links] WHERE link_start='$playerinfo[sector]'");
$i=0;
$flag=0;

// Loop through the available warp links to make sure it's a valid move
while (!$result3->EOF)
{
    $row = $result3->fields;
    if ($row['link_dest']==$_GET['sector'] && $row['link_start']==$playerinfo['sector'])
    {
        $flag=1;
    }
    $i++;
    $result3->MoveNext();
}

// Check if there was a valid warp link to move to
if ($flag==1)
{
    $ok=1;
    $calledfrom = "move.php";
    include("check_fighters.php");
    if ($ok > 0)
    {
       $stamp = date("Y-m-d H-i-s");
       $query="UPDATE $dbtables[ships] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$sector where ship_id=$playerinfo[ship_id]";
       log_move($playerinfo['ship_id'],$sector);
       $move_result = $db->Execute ("$query");
      if (!$move_result)
    {
    // is this really STILL needed?
        $error = $db->ErrorMsg();
        mail ($admin_mail,"Move Error", "Start Sector: $sectorinfo[sector_id]\nEnd Sector: $sector\nPlayer: $playerinfo[character_name] - $playerinfo[ship_id]\n\nQuery:  $query\n\nSQL error: $error");
    }
    }
    // Enter code for checking dangers in new sector
    include("check_mines.php");
    if ($ok==1) {echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface\">";} else
    {
        TEXT_GOTOMAIN();
    }
}
else
{
    echo "$l_move_failed<BR><BR>";
    $db->Execute("UPDATE $dbtables[ships] SET cleared_defences=' ' where ship_id=$playerinfo[ship_id]");

    TEXT_GOTOMAIN();
}

echo "</body></html>";
?>
