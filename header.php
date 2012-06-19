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
// File: header.php

header("Content-type: text/html; charset=utf-8");
header("Cache-Control: public"); // Tell the client (and any caches) that this information can be stored in public caches.
header("Connection: Keep-Alive"); // Tell the client to keep going until it gets all data, please.
header("Keep-Alive: timeout=15, max=100");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="Description" content="A free online game - Open source, web game, with multiplayer space exploration">
<meta name="Keywords" content="Free, online, game, Open source, web game, multiplayer, space, exploration, blacknova, traders">
<meta name="Rating" content="General">
<link rel="shortcut icon" href="images/bntfavicon.ico">
<title><?php echo $title; ?></title>
<link rel='stylesheet' type='text/css' href='templates/classic/styles/main.css'>
<script type='text/javascript' src='backends/javascript/newsticker.js'></script>
<script type='text/javascript' src='backends/javascript/imageswap.js'></script>
</head>

<?php
if ($no_body != 1)
{
    echo "<body style=\"background-image: url('images/bgoutspace1.png'); background-color:#000; color:#C0C0C0;\" link=\"#0f0\" vlink=\"#0f0\" alink=\"#f00\">";
}
?>
