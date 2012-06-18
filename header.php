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
<style type="text/css">
<!--
body {font-family: Verdana, "DejaVu Sans", sans-serif;}
a.new_link {font-size: 8Pt; color:#0f0; font-weight:bold;}
a.new_link:hover {font-size: 8Pt; color:#36f; font-weight:bold;}
body {font-size: 85%; line-height:1.125em; color: #ccc;}
-->
</style>

<?php
if (!isset($interface) || $interface == "")
{
  $interface = "main.php";
}

if (isset($interface) && $interface == "main.php")
{
    echo "<link rel='stylesheet' type='text/css' href='templates/classic/styles/main.css'></link>";
}

echo "<script type='text/javascript' src='backends/javascript/newsticker.js'></script>";

// Java functions for index.php used for button images
if (isset($interface) && $interface == "index.php")
{
    echo "<script type='text/javascript' src='backends/javascript/imageswap.js'></script>";
}

echo "</head>";

if (empty($no_body))
{
    if (isset($interface) && $interface=="main.php")
    {
        echo "<body style=\"background-image: url('images/bgoutspace1.png'); background-color:#000; color:#C0C0C0;\" link=\"#0f0\" vlink=\"#0f0\" alink=\"#f00\">";
    }
    else
    {
        echo "<body background=\"\" bgcolor=\"#000\" text=\"#c0c0c0\" link=\"#0f0\" vlink=\"#808080\" alink=\"#f00\">";
    }
}
else
{
//    echo "<body bgcolor=\"#666\" text=\"#f0f0f0\" link=\"#0f0\" vlink=\"#0f0\" alink=\"#f00\">";
}

echo "\n";

// include "server_ticker.php";
?>
