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
// File: includes/check_login.php

if (strpos ($_SERVER['PHP_SELF'], 'check_login.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function check_login ($db, $lang, $langvars, $stop_die = true)
{
    // New database driven language entries
    load_languages ($db, $lang, array ('login', 'global_funcs', 'common', 'footer', 'self_destruct'), $langvars);

    $flag = 0;

    if (array_key_exists ('username', $_SESSION) === false)
    {
        $_SESSION['username'] = null;
    }

    if (array_key_exists ('password', $_SESSION) === false)
    {
        $_SESSION['password'] = null;
    }

    if (is_null ($_SESSION['username']) == false && is_null ($_SESSION['password']) == false)
    {
        $rs = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email=? LIMIT 1;", array ($_SESSION['username']));
        \bnt\dbop::dbresult ($db, $rs, __LINE__, __FILE__);
        if ($rs instanceof ADORecordSet && $rs->RecordCount() >0)
        {
            $playerinfo = $rs->fields;

            // Initialize the hasher, with 8 (a base-2 log iteration count) for password stretching and without less-secure portable hashes for older systems
            require_once './config/pw_hash.php';
            $hasher = new PasswordHash(HASH_STRENGTH, false);

            // Check the password against the stored hashed password
            $password_match = $hasher->CheckPassword ($_SESSION['password'], $playerinfo['password']);

            // Check the cookie to see if username/password are empty - check password against database
            if ($password_match == true)
            {
                $ip = $_SERVER['REMOTE_ADDR'];
                $stamp = date ("Y-m-d H:i:s");
                $timestamp['now']  = (int) strtotime ($stamp);
                $timestamp['last'] = (int) strtotime ($playerinfo['last_login']);

                // Update the players last_login ever 60 seconds to cut back SQL Queries.
                if ($timestamp['now'] >= ($timestamp['last'] +60))
                {
                    $update = $db->Execute ("UPDATE {$db->prefix}ships SET last_login = ?, ip_address = ? WHERE ship_id = ?;", array ($stamp, $ip, $playerinfo['ship_id']));
                    $_SESSION['last_activity'] = $timestamp['now']; // Reset the last activity time on the session so that the session renews - this is the replacement for the (now removed) update_cookie function.
                }

                $banned = 0;

                // Check to see if the player is banned every 60 seconds (may need to ajust this).
                if ($timestamp['now'] >= ($timestamp['last'] +60))
                {
                    include_once './includes/check_ban.php';

                    $ban_result = check_ban($db, $lang, null, $playerinfo);
                    if ($ban_result === false ||  (array_key_exists ('ban_type', $ban_result) && $ban_result['ban_type'] === ID_WATCH))
                    {
                        // do nothing
                    }
                    else
                    {
                        // Set login status to false, then clear the session array, and finally clear the session cookie
                        $_SESSION['logged_in'] = false;
                        $_SESSION = array ();
                        setcookie ("PHPSESSID", "", 0, "/");

                        // Destroy the session entirely
                        session_destroy ();

                        include_once './header.php';
                        echo "<div style='font-size:18px; color:#FF0000;'>\n";
                        if ( array_key_exists ('ban_type', $ban_result) && $ban_result['ban_type'] == ID_LOCKED )
                        {
                            echo "Your account has been Locked";
                        }
                        else
                        {
                            echo "Your account has been Banned";
                        }

                        if ( array_key_exists ('public_info', $ban_result) && strlen(trim ($ban_result['public_info'])) >0 )
                        {
                            echo " for the following:<br>\n";
                            echo "<br>\n";
                            echo "<div style='font-size:16px; color:#FFFF00;'>{$ban_result['public_info']}</div>\n";
                        }
                        echo "</div>\n";
                        echo "<br>\n";
                        echo "<div style='color:#FF0000;'>Maybe you will behave yourself next time.</div>\n";
                        echo "<br />\n";
                        echo str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
                        $flag = 1;
                        include_once './footer.php';
                        $banned = 1;
                    }

                }

                // Check for destroyed ship
                if ($playerinfo['ship_destroyed'] == "Y" && $banned == 0)
                {
                    // if the player has an escapepod, set the player up with a new ship
                    if ($playerinfo['dev_escapepod'] == "Y")
                    {
                        include_once './header.php';
                        $result2 = $db->Execute ("UPDATE {$db->prefix}ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N',dev_lssd='N' WHERE email=?", array ($_SESSION['username']));
                        \bnt\dbop::dbresult ($db, $result2, __LINE__, __FILE__);
                        echo str_replace ("[here]", "<a href='main.php'>" . $langvars['l_here'] . "</a>", $langvars['l_login_died']);
                        $flag = 1;
                        include_once './footer.php';
                    }
                    else
                    {
                        if ($stop_die == true)
                        {
                            include_once './header.php';
                            // if the player doesn't have an escapepod - they're dead, delete them. But we can't delete them yet.
                            // (This prevents the self-distruct inherit bug)
                            echo str_replace ("[here]", "<a href='log.php'>" . ucfirst($langvars['l_here']) . "</a>", $langvars['l_global_died']) . "<br><br>" . $langvars['l_global_died2'];
                            echo str_replace ("[logout]", "<a href='logout.php'>" . $langvars['l_logout'] . "</a>", $langvars['l_die_please']);
                            $flag = 1;
                            include_once './footer.php';
                        }
                    }
                }

            }
            else
            {
                $title = $langvars['l_error'];
                include_once './header.php';
                echo str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_needlogin']);
                include_once './footer.php';
                $flag = 1;
            }
        }
        else
        {
            $title = $langvars['l_error'];
            include_once './header.php';
            echo str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_needlogin']);
            include_once './footer.php';
            $flag = 1;
        }

    }
    else
    {
        $title = $langvars['l_error'];
        include_once './header.php';
        echo str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_needlogin']);
        include_once './footer.php';
        $flag = 1;
    }

    global $server_closed;
    if ($server_closed && $flag == 0)
    {
        $title = $langvars['l_login_closed_message'];
        include_once './header.php';
        echo $langvars['l_login_closed_message'];
        include_once './footer.php';
        $flag = 1;
    }

    return $flag;
}

?>
