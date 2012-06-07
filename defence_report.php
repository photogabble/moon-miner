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
// File: defence_report.php

include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_sdf_title;
include("header.php");

if (checklogin())
{
    die();
}

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

$query = "SELECT * FROM $dbtables[sector_defence] WHERE ship_id=$playerinfo[ship_id]";
if (!empty($sort))
{
  $query .= " ORDER BY";
  if ($sort == "quantity")
  {
    $query .= " quantity ASC";
  }
  elseif ($sort == "mode")
  {
    $query .= " fm_setting ASC";
  }
  elseif ($sort == "type")
  {
    $query .= " defence_type ASC";
  }
  else
  {
    $query .= " sector_id ASC";
  }
}

$res = $db->Execute($query);

bigtitle();

$i = 0;
if ($res)
{
  while (!$res->EOF)
  {
    $sector[$i] = $res->fields;
    $i++;
    $res->MoveNext();
  }
}

$num_sectors = $i;
if ($num_sectors < 1)
{
  echo "<BR>$l_sdf_none";
}
else
{
  echo "$l_pr_clicktosort<BR><BR>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=2>";
  echo "<TR BGCOLOR=\"$color_header\">";
  echo "<TD><B><A HREF=defence_report.php?sort=sector>$l_sector</A></B></TD>";
  echo "<TD><B><A HREF=defence_report.php?sort=quantity>$l_qty</A></B></TD>";
  echo "<TD><B><A HREF=defence_report.php?sort=type>$l_sdf_type</A></B></TD>";
  echo "<TD><B><A HREF=defence_report.php?sort=mode>$l_sdf_mode</A></B></TD>";
  echo "</TR>";
  $color = $color_line1;
  for ($i = 0; $i < $num_sectors; $i++) {

    echo "<TR BGCOLOR=\"$color\">";
    echo "<TD><A HREF=rsmove.php?engage=1&destination=". $sector[$i][sector_id] . ">". $sector[$i][sector_id] ."</A></TD>";
    echo "<TD>" . NUMBER($sector[$i]['quantity']) . "</TD>";
    $defence_type = $sector[$i]['defence_type'] == 'F' ? $l_fighters : $l_mines;
    echo "<TD> $defence_type </TD>";
    $mode = $sector[$i]['defence_type'] == 'F' ? $sector[$i]['fm_setting'] : $l_n_a;
    if ($mode == 'attack')
      $mode = $l_md_attack;
    else
      $mode = $l_md_toll;
    echo "<TD> $mode </TD>";
    echo "</TR>";

    if ($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
  }
  echo "</TABLE>";
}
echo "<BR><BR>";

TEXT_GOTOMAIN();

include("footer.php");

?>
