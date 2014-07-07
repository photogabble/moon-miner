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
// File: classes/Login.php

namespace Bnt;

class Login
{
    public static function checkLogin($db, $pdo_db, $lang, $langvars, $bntreg, $template)
    {
        // Database driven language entries
        $langvars = Translate::load($db, $lang, array ('login', 'global_funcs', 'common', 'footer', 'self_destruct'));

        // Check if game is closed - Ignore the false return if it is open
        Game::isGameClosed($db, $pdo_db, $bntreg, $lang, $template, $langvars);

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
            $res = $db->SelectLimit("SELECT ip_address, password, last_login, ship_id, ship_destroyed, dev_escapepod FROM {$db->prefix}ships WHERE email=?", 1, -1, array ('email' => $_SESSION['username']));
            Db::logDbErrors($db, $res, __LINE__, __FILE__);
//          if ($res instanceof ADORecordSet && $res->RecordCount() >0) // This is producing errors for some reason, while if $res does not
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
                        $update_llogin = $db->Execute("UPDATE {$db->prefix}ships SET last_login = ?, ip_address = ? WHERE ship_id = ?;", array ($stamp, $_SERVER['REMOTE_ADDR'], $playerinfo['ship_id']));
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

        // Check for ban - Ignore the false return if not
        Player::HandleBan($timestamp, $db, $pdo_db, $lang, $template, $playerinfo);

        // Check for destroyed ship - Ignore the false return if not
        Ship::isDestroyed($db, $pdo_db, $lang, $bntreg, $langvars, $template, $playerinfo);

        return (!$flag);
    }
}
?>
