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
// File: warpedit2.php

include("config.php");
updatecookie();
include("languages/$lang");
$title=$l_warp_title;
include("header.php");

if (checklogin())
{
    die();
}

$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo=$result->fields;

if ($playerinfo[turns] < 1)
{
    echo $l_warp_turn . "<br><br>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
}

if ($playerinfo[dev_warpedit] < 1)
{
    echo $l_warp_none . "<br><br>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
}

$res = $db->Execute("SELECT allow_warpedit,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE sector_id=$playerinfo[sector] AND $dbtables[universe].zone_id=$dbtables[zones].zone_id");
$zoneinfo = $res->fields;
if ($zoneinfo[allow_warpedit] == 'N')
{
    echo $l_warp_forbid . "<br><br>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
}

$target_sector=round($target_sector);
$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

bigtitle();

$result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id=$target_sector");
$row = $result2->fields;
if (!$row)
{
    echo $l_warp_nosector . "<br><br>";
    TEXT_GOTOMAIN();
    die();
}

$res = $db->Execute("SELECT allow_warpedit,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE sector_id=$target_sector AND $dbtables[universe].zone_id=$dbtables[zones].zone_id");
$zoneinfo = $res->fields;
if ($zoneinfo[allow_warpedit] == 'N' && !$oneway)
{
    $l_warp_twoerror = str_replace("[target_sector]", $target_sector, $l_warp_twoerror);
    echo $l_warp_twoerror . "<br><br>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
}

$res = $db->Execute("SELECT COUNT(*) as count FROM $dbtables[links] WHERE link_start=$playerinfo[sector]");
$row = $res->fields;
$numlink_start=$row[count];

if ($numlink_start>=$link_max)
{
    echo $l_warp_sectex . "<br><br>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
}

$result3 = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start=$playerinfo[sector]");
if ($result3 > 0)
{
    while (!$result3->EOF)
    {
        $row = $result3->fields;
        if ($target_sector == $row[link_dest])
        {
            $flag = 1;
        }
        $result3->MoveNext();
    }

    if ($flag == 1)
    {
        $l_warp_linked = str_replace("[target_Sector]", $target_sector, $l_warp_linked);
        echo $l_warp_linked . "<br><br>";
    }
    elseif ($playerinfo[sector] == $target_sector)
    {
        echo $l_warp_cantsame;
    }
    else
    {
        $insert1 = $db->Execute ("INSERT INTO $dbtables[links] SET link_start=$playerinfo[sector], link_dest=$target_sector");
        $update1 = $db->Execute ("UPDATE $dbtables[ships] SET dev_warpedit=dev_warpedit - 1, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
        if ($oneway)
        {
            echo "$l_warp_coneway $target_sector.<br><br>";
        }
        else
        {
            $result4 = $db->Execute ("SELECT * FROM $dbtables[links] WHERE link_start=$target_sector");
            if ($result4)
            {
                while (!$result4->EOF)
                {
                    $row = $result4->fields;
                    if ($playerinfo[sector] == $row[link_dest])
                    {
                        $flag2 = 1;
                    }
                    $result4->MoveNext();
                }
            }
            if ($flag2 != 1)
            {
                $insert2 = $db->Execute ("INSERT INTO $dbtables[links] SET link_start=$target_sector, link_dest=$playerinfo[sector]");
            }
            echo "$l_warp_ctwoway $target_sector.<br><br>";
        }
    }
}

TEXT_GOTOMAIN();
include("footer.php");
?>
