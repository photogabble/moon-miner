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
// File: classes/Player.php

namespace Bnt;

class Player
{
    public static function HandleAuth($db, $pdo_db, $lang, $bntreg, $template)
    {
        $flag = true;
        $error_status = null;

        if (array_key_exists('username', $_SESSION) === false)
        {
            $_SESSION['username'] = null;
        }

        if (array_key_exists('password', $_SESSION) === false)
        {
            $_SESSION['password'] = null;
        }

        if (is_null($_SESSION['username']) === false && is_null($_SESSION['password']) === false)
        {
            $res = $db->SelectLimit(
                "SELECT ip_address, password, last_login, ship_id, ship_destroyed, dev_escapepod" .
                " FROM {$db->prefix}ships WHERE email=?",
                1,
                -1,
                array('email' => $_SESSION['username'])
            );
            Db::logDbErrors($db, $res, __LINE__, __FILE__);
//          This is producing errors for some reason, while if $res does not
//          if ($res instanceof ADORecordSet && $res->RecordCount() >0)
            if ($res)
            {
                $playerinfo = $res->fields;

                // Check the password against the stored hashed password
                // Check the cookie to see if username/password are empty - check password against database
                if (password_verify($_SESSION['password'], $playerinfo['password']))
                {
                    $stamp = date('Y-m-d H:i:s');
                    $timestamp['now']  = (int) strtotime($stamp);
                    $timestamp['last'] = (int) strtotime($playerinfo['last_login']);

                    // Update the players last_login every 60 seconds to cut back SQL Queries.
                    if ($timestamp['now'] >= ($timestamp['last'] + 60))
                    {
                        $update_llogin = $db->Execute(
                            "UPDATE {$db->prefix}ships SET last_login = ?, ip_address = ? WHERE ship_id=?;",
                            array($stamp, $_SERVER['REMOTE_ADDR'], $playerinfo['ship_id'])
                        );
                        Db::logDbErrors($db, $update_llogin, __LINE__, __FILE__);

                        // Reset the last activity time on the session so that the session renews - this is the
                        // replacement for the (now removed) update_cookie function.
                        $_SESSION['last_activity'] = $timestamp['now'];
                    }
                    $flag = false;
                }
            }
        }

        if ($flag)
        {
            $title = $langvars['l_error'];
            $error_status .= str_replace('[here]', "<a href='index.php'>" . $langvars['l_here'] . '</a>', $langvars['l_global_needlogin']);
            $title = $langvars['l_error'];
            Header::display($db, $lang, $template, $title);
            echo $error_status;
            Footer::display($pdo_db, $lang, $bntreg, $template);
            die();
        }
        else
        {
            return $playerinfo;
        }
    }

    public static function HandleBan($timestamp, $db, $pdo_db, $lang, $template, $playerinfo)
    {
        // Check to see if the player is banned every 60 seconds (may need to ajust this).
        if ($timestamp['now'] >= ($timestamp['last'] + 60))
        {
            $ban_result = CheckBan::isBanned($db, $lang, null, $playerinfo);
            if ($ban_result===false|| (array_key_exists('ban_type', $ban_result)&&$ban_result['ban_type']===ID_WATCH))
            {
                return false;
            }
            else
            {
                // Set login status to false, then clear the session array, and clear the session cookie
                $_SESSION['logged_in'] = false;
                $_SESSION = array();
                setcookie('blacknova_session', '', 0, '/');

                // Destroy the session entirely
                session_destroy();

                $error_status = "<div style='font-size:18px; color:#FF0000;'>\n";
                if (array_key_exists('ban_type', $ban_result) && $ban_result['ban_type'] === ID_LOCKED)
                {
                    $error_status .= 'Your account has been Locked';
                }
                else
                {
                    $error_status .= 'Your account has been Banned';
                }

                if (array_key_exists('public_info', $ban_result) && mb_strlen(trim($ban_result['public_info'])) >0)
                {
                    $error_status .=" for the following:<br>\n";
                    $error_status .="<br>\n";
                    $error_status .="<div style='font-size:16px; color:#FFFF00;'>";
                    $error_status .= $ban_result['public_info'] . "</div>\n";
                }
                $error_status .= "</div>\n";
                $error_status .= "<br>\n";
                $error_status .= "<div style='color:#FF0000;'>Maybe you will behave yourself next time.</div>\n";
                $error_status .= "<br />\n";
                $error_status .= str_replace('[here]', "<a href='index.php'>" . $langvars['l_here'] . '</a>', $langvars['l_global_mlogin']);

                $title = $langvars['l_error'];
                Header::display($db, $lang, $template, $title);
                echo $error_status;
                Footer::display($pdo_db, $lang, $bntreg, $template);
                die();
            }
        }
    }
}
?>
