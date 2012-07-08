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
// File: includes/checklogin.php

if (preg_match("/checklogin.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function checklogin ()
{
    $flag = 0;

    global $username, $l_global_needlogin, $l_global_died, $l_global_died2;
    global $password, $l_login_died, $l_die_please, $l_logout, $l_here;
    global $db, $db_logging;

    $result1 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email=? LIMIT 1", array($username));
    db_op_result ($db, $result1, __LINE__, __FILE__, $db_logging);
    $playerinfo = $result1->fields;

    // Check the cookie to see if username/password are empty - check password against database
    if ($username == "" or $password == "" or $password != $playerinfo['password'])
    {
        $l_global_needlogin = str_replace("[here]", "<a href='login.php'>" . $l_here . "</a>", $l_global_needlogin);
        $title = $l_error;
        include "header.php";
        echo $l_global_needlogin;
        include "footer.php";
        $flag = 1;
    }

    // Check for destroyed ship
    if ($playerinfo['ship_destroyed'] == "Y")
    {
        // if the player has an escapepod, set the player up with a new ship
        if ($playerinfo['dev_escapepod'] == "Y")
        {
            $result2 = $db->Execute("UPDATE {$db->prefix}ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N',dev_lssd='N' where email='$username'");
            db_op_result ($db, $result2, __LINE__, __FILE__, $db_logging);
            $l_login_died = str_replace("[here]", "<a href='main.php'>" . $l_here . "</a>", $l_login_died);
            echo $l_login_died;
            $flag = 1;
        }
        else
        {
            // if the player doesn't have an escapepod - they're dead, delete them. But we can't delete them yet.
            // (This prevents the self-distruct inherit bug)
            $l_global_died = str_replace("[here]", "<a href='log.php'>" . ucfirst($l_here) . "</a>", $l_global_died);
            echo $l_global_died . "<br><br>" . $l_global_died2;

            $l_die_please = str_replace("[logout]", "<a href='logout.php'>" . $l_logout . "</a>", $l_die_please);
            echo $l_die_please;
            $flag = 1;
        }
    }

    global $server_closed, $l_login_closed_message;
    if ($server_closed && $flag == 0)
    {
        echo $l_login_closed_message;
        $flag = 1;
    }

    return $flag;
}
?>
