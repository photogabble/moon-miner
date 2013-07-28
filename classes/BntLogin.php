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
// File: classes/BntLogin.php

if (strpos ($_SERVER['PHP_SELF'], 'BntLogin.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntLogin
{
    static function checkLogin ($db, $lang, $langvars, $bntreg, $stop_die = true)
    {
        // Database driven language entries
        $langvars = BntTranslate::load ($db, $lang, array ('login', 'global_funcs', 'common', 'footer', 'self_destruct'));

        $flag = 0;
        $error_status = '';

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
            $rs = $db->SelectLimit ("SELECT * FROM {$db->prefix}ships WHERE email=?", 1, -1, array ('email' => $_SESSION['username']));
            DbOp::dbResult ($db, $rs, __LINE__, __FILE__);
            if ($rs instanceof ADORecordSet && $rs->RecordCount() >0)
            {
                $playerinfo = $rs->fields;

                // Check the password against the stored hashed password
                $hasher = new PasswordHash (10, false); // The first number is the hash strength, or number of iterations of bcrypt to run.
                $password_match = $hasher->CheckPassword ($_SESSION['password'], $playerinfo['password']);

                // Check the cookie to see if username/password are empty - check password against database
                if ($password_match == true)
                {
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $stamp = date ("Y-m-d H:i:s");
                    $timestamp['now']  = (int) strtotime ($stamp);
                    $timestamp['last'] = (int) strtotime ($playerinfo['last_login']);

                    // Update the players last_login every 60 seconds to cut back SQL Queries.
                    if ($timestamp['now'] >= ($timestamp['last'] +60))
                    {
                        $update = $db->Execute ("UPDATE {$db->prefix}ships SET last_login = ?, ip_address = ? WHERE ship_id = ?;", array ($stamp, $ip, $playerinfo['ship_id']));
                        $_SESSION['last_activity'] = $timestamp['now']; // Reset the last activity time on the session so that the session renews - this is the replacement for the (now removed) update_cookie function.
                    }

                    $banned = 0;

                    // Check to see if the player is banned every 60 seconds (may need to ajust this).
                    if ($timestamp['now'] >= ($timestamp['last'] +60))
                    {
                        $ban_result = CheckBan::isBanned ($db, $lang, null, $playerinfo);
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

                            $error_status = "<div style='font-size:18px; color:#FF0000;'>\n";
                            if ( array_key_exists ('ban_type', $ban_result) && $ban_result['ban_type'] == ID_LOCKED )
                            {
                                $error_status .= "Your account has been Locked";
                            }
                            else
                            {
                                $error_status .= "Your account has been Banned";
                            }

                            if ( array_key_exists ('public_info', $ban_result) && strlen(trim ($ban_result['public_info'])) >0 )
                            {
                                $error_status .=" for the following:<br>\n";
                                $error_status .="<br>\n";
                                $error_status .="<div style='font-size:16px; color:#FFFF00;'>{$ban_result['public_info']}</div>\n";
                            }
                            $error_status .= "</div>\n";
                            $error_status .= "<br>\n";
                            $error_status .= "<div style='color:#FF0000;'>Maybe you will behave yourself next time.</div>\n";
                            $error_status .= "<br />\n";
                            $error_status .= str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
                            $flag = 1;
                            $banned = 1;
                        }
                    }

                    // Check for destroyed ship
                    if ($playerinfo['ship_destroyed'] == "Y" && $banned == 0)
                    {
                        // if the player has an escapepod, set the player up with a new ship
                        if ($playerinfo['dev_escapepod'] == "Y")
                        {
                            $result2 = $db->Execute ("UPDATE {$db->prefix}ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N',dev_lssd='N' WHERE email=?", array ($_SESSION['username']));
                            DbOp::dbResult ($db, $result2, __LINE__, __FILE__);
                            $error_status .= str_replace ("[here]", "<a href='main.php'>" . $langvars['l_here'] . "</a>", $langvars['l_login_died']);
                            $flag = 1;
                        }
                        else
                        {
                            if ($stop_die) // On log.php, this will be set to false, so that you can view the log telling you that you died.
                            {
                                // if the player doesn't have an escapepod - they're dead, delete them. But we can't delete them yet.
                                // (This prevents the self-distruct inherit bug)
                                $error_status .= str_replace ("[here]", "<a href='log.php'>" . ucfirst ($langvars['l_here']) . "</a>", $langvars['l_global_died']) . "<br><br>" . $langvars['l_global_died2'];
                                $error_status .= str_replace ("[logout]", "<a href='logout.php'>" . $langvars['l_logout'] . "</a>", $langvars['l_die_please']);
                                $flag = 1;
                            }
                        }
                    }
                }
                else
                {
                    $title = $langvars['l_error'];
                    $error_status .= str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_needlogin']);
                    $flag = 1;
                }
            }
            else
            {
                $title = $langvars['l_error'];
                $error_status .= str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_needlogin']);
                $flag = 1;
            }
        }
        else
        {
            $title = $langvars['l_error'];
            $error_status .= str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_needlogin']);
            $flag = 1;
        }

        if ($bntreg->get("server_closed") && $flag == 0)
        {
            $title = $langvars['l_login_closed_message'];
            $error_status .= $langvars['l_login_closed_message'];
            $flag = 1;
        }

        // This isn't the prettiest way to do this, and I'd like this split up and templated and so forth, but for now, it works.
        if ($flag == 1)
        {
            include_once './header.php';
            echo $error_status;
            include_once './footer.php';
            if ($stop_die)
            {
                die();
            }
        }

        return $flag;
    }
}
?>
