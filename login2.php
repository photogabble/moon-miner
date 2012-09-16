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

include './global_includes.php';

// Test to see if server is closed to logins
$playerfound = false;

if ($_POST['email'] != null)
{
    $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_POST['email']));
    db_op_result ($db, $res, __LINE__, __FILE__);
    if ($res)
    {
        $playerfound = $res->RecordCount();
    }
    $playerinfo = $res->fields;
    $lang = $playerinfo['lang'];
}

if (!isset($_GET['lang']))
{
    $_GET['lang'] = null;
    $lang = $default_lang;
    $link = '';
}
else
{
    $lang = $_GET['lang'];
    $link = "?lang=" . $lang;
}

// New database driven language entries
load_languages($db, $lang, array('login2', 'login', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars);

// first placement of cookie - don't use update cookie.
//$userpass = $_POST['email']."+".$_POST['pass'];
//setcookie("userpass", $userpass, time() + (3600*24)*365, $gamepath, $gamedomain);

if ($server_closed)
{
    $title = $l_login_sclosed;
    include './header.php';
    echo "<div style='text-align:center; color:#ff0; font-size:20px;'><br>$l_login_closed_message</div><br>\n";
    echo str_replace("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);

    include './footer.php';
    die();
}

$title = $l_login_title2;

// Check Banned
$banned = 0;

if (isset($playerinfo))
{
    $res = $db->Execute("SELECT * FROM {$db->prefix}ip_bans WHERE ? LIKE ban_mask OR ? LIKE ban_mask;", array ($ip, $playerinfo['ip_address']));
    db_op_result ($db, $res, __LINE__, __FILE__);
    if ($res->RecordCount() != 0)
    {
//        setcookie("userpass", "", 0, $gamepath, $gamedomain);
//        setcookie("userpass", "", 0); // Delete from default path as well.
        $banned = 1;
    }
}

include './header.php';
echo "<h1>" . $title . "</h1>\n";

if ($playerfound)
{
    // Initialize the hasher, with the hash strength for password stretching set from the admin define file and without less-secure portable hashes for older systems
    $hasher = new PasswordHash (HASH_STRENGTH, false);

    if ($hasher->CheckPassword ($_POST['pass'], $playerinfo['password']))
    {
        if ($playerinfo['ship_destroyed'] == "N")
        {
            // player's ship has not been destroyed
            player_log ($db, $playerinfo['ship_id'], LOG_LOGIN, $ip);
            $stamp = date("Y-m-d H-i-s");
            $update = $db->Execute("UPDATE {$db->prefix}ships SET last_login = ?, ip_address = ? WHERE ship_id = ?;", array ($stamp, $ip, $playerinfo['ship_id']));
            db_op_result ($db, $update, __LINE__, __FILE__);
            $_SESSION['logged_in'] = true;
            $_SESSION['password'] = $_POST['pass'];
            $_SESSION['username'] = $playerinfo['email'];
            TEXT_GOTOMAIN();
            header("Location: main.php"); // This redirect avoids any rendering for the user of login2. Its a direct transition, visually
        }
        else
        {
            // player's ship has been destroyed
            if ($playerinfo['dev_escapepod'] == "Y")
            {
                $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull=0, engines=0, power=0, computer=0, sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N', dev_lssd='N' WHERE ship_id = ?", array ($playerinfo['ship_id']));
                db_op_result ($db, $resx, __LINE__, __FILE__);
                $l_login_died = str_replace("[here]", "<a href='main.php'>" . $l_here . "</a>", $l_login_died);
                echo $l_login_died;
            }
            else
            {
                echo "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.<br><br>";

                // Check if $newbie_nice is set, if so, verify ship limits
                if ($newbie_nice == "YES")
                {
                    $newbie_info = $db->Execute("SELECT hull, engines, power, computer, sensors, armor, shields, beams, torp_launchers, cloak FROM {$db->prefix}ships WHERE ship_id = ? AND hull <= ? AND engines <= ? AND power <= ? AND computer <= ? AND sensors <= ? AND armor <= ? AND shields <= ? AND beams <= ? AND torp_launchers <= ? AND cloak <= ?;", array ($playerinfo['ship_id'], $newbie_hull, $newbie_engines, $newbie_power, $newbie_computer, $newbie_sensors, $newbie_armor, $newbie_shields, $newbie_beams, $newbie_torp_launchers, $newbie_cloak));
                    db_op_result ($db, $newbie_info, __LINE__, __FILE__);
                    $num_rows = $newbie_info->RecordCount();

                    if ($num_rows)
                    {
                        echo "<br><br>" . $l_login_newbie . "<br><br>";
                        $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull=0, engines=0, power=0, computer=0, sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, credits=1000, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N', dev_lssd='N' WHERE ship_id = ?", array ($playerinfo['ship_id']));
                        db_op_result ($db, $resx, __LINE__, __FILE__);

                        $l_login_newlife = str_replace("[here]", "<a href='main.php'>" . $l_here . "</a>", $l_login_newlife);
                        echo $l_login_newlife;
                    }
                    else
                    {
                        echo "<br><br>" . $l_login_looser . "<br><br>" . $l_login_looser2;
                    }

                } // End if $newbie_nice
                else
                {
                    echo "<br><br>" . $l_login_looser . "<br><br>" . $l_login_looser2;
                }
            }
        }
    }
    else
    {
        // password is incorrect
        echo $l_login_4gotpw1a . "<br><br>" . $l_login_4gotpw1b . " <a href='mail.php?mail=" . $_POST['email'] . "'>" . $l_clickme . "</a> " . $l_login_4gotpw2a . "<br><br>" . $l_login_4gotpw2b . " <a href='index.php'>" . $l_clickme . "</a> " . $l_login_4gotpw3 . " " . $ip . "...";
        player_log ($db, $playerinfo['ship_id'], LOG_BADLOGIN, $ip);
        admin_log($db, (1000 + LOG_BADLOGIN), "{$ip}|{$_POST['email']}|{$_POST['pass']}");
    }
}
else
{
    $l_login_noone = str_replace("[here]", "<a href='new.php" . $link . "'>" . $l_here . "</a>", $l_login_noone);
    echo "<strong>" . $l_login_noone . "</strong><br>";
}

include './footer.php';
?>
