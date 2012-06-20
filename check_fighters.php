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
// File: check_fighters.php

if (preg_match("/check_fighters.php/i", $_SERVER['PHP_SELF'])) {
    echo "You can not access this file directly!";
    die();
}

include "languages/$lang";

$result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
// Put the sector information into the array "sectorinfo"
$sectorinfo=$result2->fields;
$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='F' ORDER BY quantity DESC");
// Put the defence information into the array "defenceinfo"
$i = 0;
$total_sector_fighters = 0;
$owner = true;
while (!$result3->EOF)
{
    $row = $result3->fields;
    $defences[$i] = $row;
    $total_sector_fighters += $defences[$i]['quantity'];
    if ($defences[$i][ship_id] != $playerinfo[ship_id])
    {
        $owner = false;
    }
    $i++;
    $result3->MoveNext();
}

$num_defences = $i;
if ($num_defences > 0 && $total_sector_fighters > 0 && !$owner)
{
    // Find out if the fighter owner and player are on the same team
    // All sector defences must be owned by members of the same team
    $fm_owner = $defences[0]['ship_id'];
    $result2 = $db->Execute("SELECT * from $dbtables[ships] where ship_id=$fm_owner");
    $fighters_owner = $result2->fields;
    if ($fighters_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
    {
        switch ($response)
        {
            case "fight":
                $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $playerinfo[ship_id]");
                bigtitle();
                include_once "includes/sector_fighters.php";
                break;

            case "retreat":
                $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $playerinfo[ship_id]");
                $stamp = date("Y-m-d H-i-s");
                $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',turns=turns-2, turns_used=turns_used+2, sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
                bigtitle();
                echo "$l_chf_youretreatback<br>";
                TEXT_GOTOMAIN();
                die();
                break;

            case "pay":
                $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $playerinfo[ship_id]");
                $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
                if ($playerinfo[credits] < $fighterstoll)
                {
                    echo "$l_chf_notenoughcreditstoll<br>";
                    echo "$l_chf_movefailed<br>";
                    // Undo the move
                    $db->Execute("UPDATE $dbtables[ships] SET sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
                    $ok=0;
                }
                else
                {
                    $tollstring = NUMBER($fighterstoll);
                    $l_chf_youpaidsometoll = str_replace("[chf_tollstring]", $tollstring, $l_chf_youpaidsometoll);
                    echo "$l_chf_youpaidsometoll<br>";
                    $db->Execute("UPDATE $dbtables[ships] SET credits=credits-$fighterstoll where ship_id=$playerinfo[ship_id]");
                    distribute_toll($sector,$fighterstoll,$total_sector_fighters);
                    playerlog ($db, $dbtables, $playerinfo['ship_id'], LOG_TOLL_PAID, "$tollstring|$sector");
                    $ok=1;
                }
                break;

            case "sneak":
                $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $playerinfo[ship_id]");
                $success = SCAN_SUCCESS($fighters_owner[sensors], $playerinfo[cloak]);
                if ($success < 5)
                {
                    $success = 5;
                }
                if ($success > 95)
                {
                   $success = 95;
                }
                $roll = mt_rand(1, 100);
                if ($roll < $success)
                {
                    // Sector defences detect incoming ship
                    bigtitle();
                    echo "$l_chf_thefightersdetectyou<br>";
                    include_once "includes/sector_fighters.php";
                    break;
                }
                else
                {
                    // Sector defences don't detect incoming ship
                    $ok=1;
                }
                break;

            default:
                $interface_string = $calledfrom . '?sector='.$sector.'&destination='.$destination.'&engage='.$engage;
                $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = '$interface_string' WHERE ship_id = $playerinfo[ship_id]");
                $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
                bigtitle();
                echo "<FORM ACTION=$calledfrom METHOD=POST>";
                $l_chf_therearetotalfightersindest = str_replace("[chf_total_sector_fighters]", $total_sector_fighters, $l_chf_therearetotalfightersindest);
                echo "$l_chf_therearetotalfightersindest<br>";
                if ($defences[0]['fm_setting'] == "toll")
                {
                    $l_chf_creditsdemanded = str_replace("[chf_number_fighterstoll]", NUMBER($fighterstoll), $l_chf_creditsdemanded);
                    echo "$l_chf_creditsdemanded<br>";
                }

                $l_chf_youcanretreat = str_replace("[retreat]", "<b>Retreat</b>", $l_chf_youcanretreat);
                echo $l_chf_youcan . " <br><input type=radio name=response value=retreat>" . $l_chf_youcanretreat . "<br></input>";
                if ($defences[0]['fm_setting'] == "toll")
                {
                    $l_chf_inputpay = str_replace("[pay]", "<b>Pay</b>", $l_chf_inputpay);
                    echo "<input type=radio name=response checked value=pay>" . $l_chf_inputpay . "<br></input>";
                }

                echo "<input type=radio name=response checked value=fight>";
                $l_chf_inputfight = str_replace("[fight]", "<b>Fight</b>", $l_chf_inputfight);
                echo $l_chf_inputfight . "<br></input>";

                echo "<input type=radio name=response checked value=sneak>";
                $l_chf_inputcloak = str_replace("[cloak]", "<b>Cloak</b>", $l_chf_inputcloak);
                echo $l_chf_inputcloak . "<br></input><br>";

                echo "<INPUT TYPE=SUBMIT VALUE=$l_chf_go><br><br>";
                echo "<input type=hidden name=sector value=$sector>";
                echo "<input type=hidden name=engage value=1>";
                echo "<input type=hidden name=destination value=$destination>";
                echo "</FORM>";
                die();
                break;

        }
        // Clean up any sectors that have used up all mines or fighters
        $db->Execute("delete from $dbtables[sector_defence] where quantity <= 0 ");
    }
}
?>
