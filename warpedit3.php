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
// File: warpedit3.php

include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_warp_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo=$result->fields;

if($playerinfo[turns] < 1)
{
  echo "$l_warp_turn<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

if($playerinfo[dev_warpedit] < 1)
{
  echo "$l_warp_none<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

$res = $db->Execute("SELECT allow_warpedit,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE sector_id=$playerinfo[sector] AND $dbtables[universe].zone_id=$dbtables[zones].zone_id");
$zoneinfo = $res->fields;
if($zoneinfo[allow_warpedit] == 'N')
{
  echo "$l_warp_forbid<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

$target_sector = round($target_sector);
$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

bigtitle();


$res = $db->Execute("SELECT allow_warpedit,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE sector_id=$target_sector AND $dbtables[universe].zone_id=$dbtables[zones].zone_id");
$zoneinfo = $res->fields;
if($zoneinfo[allow_warpedit] == 'N' && $bothway)
{
  $l_warp_forbidtwo = str_replace("[target_sector]", $target_sector, $l_warp_forbidtwo);
  echo "$l_warp_forbidtwo<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$target_sector");
$row = $result2->fields;
if(!$row)
{
  echo "$l_warp_nosector<BR><BR>";
  TEXT_GOTOMAIN();
  die();
}

$result3 = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start=$playerinfo[sector]");
if($result3 > 0)
{
  while(!$result3->EOF)
  {
    $row = $result3->fields;
    if($target_sector == $row[link_dest])
    {
      $flag = 1;
    }
    $result3->MoveNext();
  }
  if($flag != 1)
  {
    $l_warp_unlinked = str_replace("[target_sector]", $target_sector, $l_warp_unlinked);
    echo "$l_warp_unlinked<BR><BR>";
  }
  else
  {
    $delete1 = $db->Execute("DELETE FROM $dbtables[links] WHERE link_start=$playerinfo[sector] AND link_dest=$target_sector");
    $update1 = $db->Execute("UPDATE $dbtables[ships] SET dev_warpedit=dev_warpedit - 1, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    if(!$bothway)
    {
      echo "$l_warp_removed $target_sector.<BR><BR>";
    }
    else
    {
      $delete2 = $db->Execute("DELETE FROM $dbtables[links] WHERE link_start=$target_sector AND link_dest=$playerinfo[sector]");
      echo "$l_warp_removedtwo $target_sector.<BR><BR>";
    }
  }
}

TEXT_GOTOMAIN();

include("footer.php");

?>
