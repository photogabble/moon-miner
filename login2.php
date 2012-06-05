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
// File: login2.php

include("config.php");
include("languages/$lang");

// Test to see if server is closed to logins
$playerfound = false;

$email = $_POST['email'];
$pass = $_POST['pass'];

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$email'");
if($res)
{
    $playerfound = $res->RecordCount();
}
$playerinfo = $res->fields;

$lang=$playerinfo['lang'];
if(empty($lang))
{
    $lang=$default_lang;
}
setcookie("lang",$lang,time()+(3600*24)*365,$gamepath,$gamedomain);
include("languages/$lang" . ".inc");

// first placement of cookie - don't use updatecookie.
$userpass = $email."+".$pass;
setcookie("userpass",$userpass,time()+(3600*24)*365,$gamepath,$gamedomain);

if($server_closed)
{
    $title=$l_login_sclosed;
    include("header.php");
    echo "<div style='text-align:center; color:#ff0; font-size:20px;'><br>$l_login_closed_message</div><br>\n";

    TEXT_GOTOLOGIN();

    include("footer.php");
    die();
}

$title=$l_login_title2;

// Check Banned
$banned = 0;
$res = $db->Execute("SELECT * FROM $dbtables[ip_bans] WHERE '$ip' LIKE ban_mask OR '$playerinfo[ip_address]' LIKE ban_mask");
if($res->RecordCount() != 0)
{
    setcookie("userpass","",0,$gamepath,$gamedomain);
    setcookie("userpass","",0); // Delete from default path as well.
    setcookie("username","",0); // Legacy support, delete the old login cookies.
    setcookie("password","",0); // Legacy support, delete the old login cookies.
    setcookie("id","",0);
    setcookie("res","",0);
    $banned = 1;
}

include("header.php");
bigtitle();

if($playerfound)
{
    if($playerinfo['password'] == $pass)
    {
        if($playerinfo['ship_destroyed'] == "N")
        {
            // player's ship has not been destroyed
            playerlog($playerinfo['ship_id'], LOG_LOGIN, $ip);
            $stamp = date("Y-m-d H-i-s");
            $update = $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',ip_address='$ip' WHERE ship_id=$playerinfo[ship_id]");
            $_SESSION['logged_in'] = TRUE;
            TEXT_GOTOMAIN();
            echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface?id=" . $playerinfo['ship_id'] . "\">";
        }
        else
        {
            // player's ship has been destroyed
            if($playerinfo[dev_escapepod] == "Y")
            {
                $db->Execute("UPDATE $dbtables[ships] SET hull=0,engines=0,power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_ore=0,ship_organics=0,ship_energy=1000,ship_colonists=0,ship_goods=0,ship_fighters=100,ship_damage=0,on_planet='N',dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,ship_destroyed='N',dev_lssd='N' where ship_id=$playerinfo[ship_id]");
                echo $l_login_died;
            }
            else
            {
                echo "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.<BR><BR>";

                // Check if $newbie_nice is set, if so, verify ship limits
                if ($newbie_nice == "YES")
                {
                    $newbie_info = $db->Execute("SELECT hull,engines,power,computer,sensors,armor,shields,beams,torp_launchers,cloak FROM $dbtables[ships] WHERE ship_id='$playerinfo[ship_id]' AND hull<='$newbie_hull' AND engines<='$newbie_engines' AND power<='$newbie_power' AND computer<='$newbie_computer' AND sensors<='$newbie_sensors' AND armor<='$newbie_armor' AND shields<='$newbie_shields' AND beams<='$newbie_beams' AND torp_launchers<='$newbie_torp_launchers' AND cloak<='$newbie_cloak'");
                    $num_rows = $newbie_info->RecordCount();

                    if ($num_rows)
                    {
                        echo "<BR><BR>$l_login_newbie<BR><BR>";
                        $db->Execute("UPDATE $dbtables[ships] SET hull=0,engines=0,power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_ore=0,ship_organics=0,ship_energy=1000,ship_colonists=0,ship_goods=0,ship_fighters=100,ship_damage=0,credits=1000,on_planet='N',dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,ship_destroyed='N',dev_lssd='N' where ship_id=$playerinfo[ship_id]");

                        echo $l_login_newlife;
                    }
                    else
                    {
                        echo "<BR><BR>$l_login_looser";
                    }

                } // End if $newbie_nice
                else
                {
                    echo "<BR><BR>$l_login_looser";
                }
            }
        }
    }
    else
    {
        // password is incorrect
        echo "$l_login_4gotpw1 <A HREF=mail.php?mail=$email>$l_clickme</A> $l_login_4gotpw2 <a href=login.php>$l_clickme</a> $l_login_4gotpw3 $ip...";
        playerlog($playerinfo['ship_id'], LOG_BADLOGIN, $ip);
        adminlog((1000 + LOG_BADLOGIN), "{$ip}|{$email}|{$pass}");
    }
}
else
{
    echo "<B>$l_login_noone</B><BR>";
}

include("footer.php");

?>
