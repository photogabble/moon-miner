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
// File: emerwarp.php

include "config.php";
updatecookie();
include "languages/$lang";
$title = $l_ewd_title;
include "header.php";
if (checklogin())
{
    die();
}

$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;
srand((double)microtime()*1000000);
bigtitle();
if ($playerinfo['dev_emerwarp'] > 0)
{
    $dest_sector = rand(0, $sector_max-1);
    $result_warp = $db->Execute ("UPDATE $dbtables[ships] SET sector=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$playerinfo[ship_id]");
    log_move($playerinfo['ship_id'], $dest_sector);
    $l_ewd_used = str_replace("[sector]", $dest_sector, $l_ewd_used);
    echo $l_ewd_used . "<br><br>";
}
else
{
    echo $l_ewd_none . "<br><br>";
}

TEXT_GOTOMAIN();
include "footer.php";
?>
