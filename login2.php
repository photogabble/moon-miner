<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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

require_once './common.php';
// Test to see if server is closed to logins
$playerfound = false;

if (array_key_exists('email', $_POST) && $_POST['email'] != null)
{
    $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_POST['email']));
    Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
    if ($res)
    {
        $playerfound = $res->RecordCount();
    }
    $playerinfo = $res->fields;
    $lang = $playerinfo['lang'];
}

if (!array_key_exists('lang', $_GET))
{
    $_GET['lang'] = null;
    $lang = $bntreg->default_lang;
    $link = null;
}
else
{
    $lang = $_GET['lang'];
    $link = "?lang=" . $lang;
}

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array ('login2', 'login', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));

if ($bntreg->game_closed)
{
    $title = $langvars['l_login_sclosed'];
    Bnt\Header::display($pdo_db, $lang, $template, $title);
    echo "<div style='text-align:center; color:#ff0; font-size:20px;'><br>" . $langvars['l_login_closed_message'] . "</div><br>\n";
    echo str_replace("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
    Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
    die ();
}

$title = $langvars['l_login_title2'];

// Check Banned
$banned = 0;

if (isset ($playerinfo) && $playerfound != false)
{
    $res = $db->Execute("SELECT * FROM {$db->prefix}ip_bans WHERE ? LIKE ban_mask OR ? LIKE ban_mask;", array ($_SERVER['REMOTE_ADDR'], $playerinfo['ip_address']));
    Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
    if ($res->RecordCount() != 0)
    {
        $banned = 1;
    }
}

Bnt\Header::display($pdo_db, $lang, $template, $title);
echo "<h1>" . $title . "</h1>\n";

if ($playerfound)
{
    if (password_verify($_POST['pass'], $playerinfo['password']))
    {
        $ban_result = Bnt\CheckBan::isBanned($pdo_db, $lang, null, $playerinfo);
        if ($ban_result === false ||  (array_key_exists('ban_type', $ban_result) && $ban_result['ban_type'] === ID_WATCH))
        {

            if ($playerinfo['ship_destroyed'] == "N")
            {
                // Player's ship has not been destroyed
                Bnt\PlayerLog::writeLog($db, $playerinfo['ship_id'], LOG_LOGIN, $_SERVER['REMOTE_ADDR']);
                $stamp = date("Y-m-d H:i:s");
                $update = $db->Execute("UPDATE {$db->prefix}ships SET last_login = ?, ip_address = ? WHERE ship_id = ?;", array ($stamp, $_SERVER['REMOTE_ADDR'], $playerinfo['ship_id']));
                Bnt\Db::logDbErrors($db, $update, __LINE__, __FILE__);

                $_SESSION['logged_in'] = true;
                $_SESSION['password'] = $_POST['pass'];
                $_SESSION['username'] = $playerinfo['email'];
                Bnt\Text::gotoMain($db, $lang, $langvars);

                // They have logged in successfully, so update their session ID as well
                $bnt_session->regen();
                header("Location: main.php"); // This redirect avoids any rendering for the user of login2. Its a direct transition, visually
            }
            else
            {
                // Player's ship has been destroyed
                if ($playerinfo['dev_escapepod'] == "Y")
                {
                    $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull=0, engines=0, power=0, computer=0, sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=1, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N', dev_lssd='N' WHERE ship_id = ?", array ($playerinfo['ship_id']));
                    Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
                    $langvars['l_login_died'] = str_replace("[here]", "<a href='main.php'>" . $langvars['l_here'] . "</a>", $langvars['l_login_died']);
                    echo $langvars['l_login_died'];
                }
                else
                {
                    echo "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.<br><br>";

                    // Check if $newbie_nice is set, if so, verify ship limits
                    if ($bntreg->newbie_nice)
                    {
                        $newbie_info = $db->Execute ("SELECT hull, engines, power, computer, sensors, armor, shields, beams, torp_launchers, cloak FROM {$db->prefix}ships WHERE ship_id = ? AND hull <= ? AND engines <= ? AND power <= ? AND computer <= ? AND sensors <= ? AND armor <= ? AND shields <= ? AND beams <= ? AND torp_launchers <= ? AND cloak <= ?;", array ($playerinfo['ship_id'], $newbie_hull, $newbie_engines, $newbie_power, $newbie_computer, $newbie_sensors, $newbie_armor, $newbie_shields, $newbie_beams, $newbie_torp_launchers, $newbie_cloak));
                        Bnt\Db::logDbErrors($db, $newbie_info, __LINE__, __FILE__);
                        $num_rows = $newbie_info->RecordCount();

                        if ($num_rows)
                        {
                            echo "<br><br>" . $langvars['l_login_newbie'] . "<br><br>";
                            $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull=0, engines=0, power=0, computer=0, sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, credits=1000, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N', dev_lssd='N' WHERE ship_id = ?", array ($playerinfo['ship_id']));
                            Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);

                            $langvars['l_login_newlife'] = str_replace("[here]", "<a href='main.php'>" . $langvars['l_here'] . "</a>", $langvars['l_login_newlife']);
                            echo $langvars['l_login_newlife'];
                        }
                        else
                        {
                            echo "<br><br>" . $langvars['l_login_looser'] . "<br><br>" . $langvars['l_login_looser2'];
                        }

                    }
                    else
                    {
                        echo "<br><br>" . $langvars['l_login_looser'] . "<br><br>" . $langvars['l_login_looser2'];
                    }
                }
            }
        }
        else
        {
            echo "<div style='font-size:18px; color:#FF0000;'>\n";
            if ( array_key_exists('ban_type', $ban_result) && $ban_result['ban_type'] == ID_LOCKED)
            {
                echo "Your account has been Locked";
            }
            else
            {
                echo "Your account has been Banned";
            }

            if (array_key_exists('public_info', $ban_result) && mb_strlen(trim($ban_result['public_info']))>0)
            {
                echo " for the following:<br>\n";
                echo "<br>\n";
                echo "<div style='font-size:16px; color:#FFFF00;'>{$ban_result['public_info']}</div>\n";
            }
            echo "</div>\n";
            echo "<br>\n";
            echo "<div style='color:#FF0000;'>Maybe you will behave yourself next time.</div>\n";
            echo "<br>\n";
            echo str_replace("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
        }
    }
    else
    {
        // password is incorrect
        echo $langvars['l_login_4gotpw1a'] . "<br><br>" . $langvars['l_login_4gotpw1b'] . " <a href='mail.php?mail=" . $_POST['email'] . "'>" . $langvars['l_clickme'] . "</a> " . $langvars['l_login_4gotpw2a'] . "<br><br>" . $langvars['l_login_4gotpw2b'] . " <a href='index.php'>" . $langvars['l_clickme'] . "</a> " . $langvars['l_login_4gotpw3'] . " " . $_SERVER['REMOTE_ADDR'] . "...";
        Bnt\PlayerLog::writeLog($db, $playerinfo['ship_id'], LOG_BADLOGIN, $_SERVER['REMOTE_ADDR']);
        Bnt\AdminLog::writeLog($db, (1000 + LOG_BADLOGIN), "{$_SERVER['REMOTE_ADDR']}|{$_POST['email']}|{$_POST['pass']}");
    }
}
else
{
    $langvars['l_login_noone'] = str_replace("[here]", "<a href='new.php" . $link . "'>" . $langvars['l_here'] . "</a>", $langvars['l_login_noone']);
    echo "<strong>" . $langvars['l_login_noone'] . "</strong><br>";
}

Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
