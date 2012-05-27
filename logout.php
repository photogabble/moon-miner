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
// File: logout.php

include("config.php");
include("languages/$lang");
$title=$l_logout;
setcookie("userpass","",0,$gamepath,$gamedomain);
setcookie("userpass","",0); // Delete from default path as well.
setcookie("username","",0); // Legacy support, delete the old login cookies.
setcookie("password","",0); // Legacy support, delete the old login cookies.
setcookie("id","",0);
setcookie("res","",0);
include("header.php");

$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

$current_score = gen_score($playerinfo[ship_id]);
playerlog($playerinfo[ship_id], LOG_LOGOUT, $ip);

bigtitle();
echo "$l_logout_score $current_score.<BR>";
$l_logout_text=str_replace("[name]",$username,$l_logout_text);
echo $l_logout_text;

include("footer.php");
?>
