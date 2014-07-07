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
    public static function HandleBan($timestamp, $db, $pdo_db, $lang, $template, $playerinfo)
    {
        // Check to see if the player is banned every 60 seconds (may need to ajust this).
        if ($timestamp['now'] >= ($timestamp['last'] + 60))
        {
            $ban_result = CheckBan::isBanned($db, $lang, null, $playerinfo);
            if ($ban_result === false ||  (array_key_exists('ban_type', $ban_result) && $ban_result['ban_type'] === ID_WATCH))
            {
                return false;
            }
            else
            {
                // Set login status to false, then clear the session array, and clear the session cookie
                $_SESSION['logged_in'] = false;
                $_SESSION = array ();
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
                    $error_status .="<div style='font-size:16px; color:#FFFF00;'>" . $ban_result['public_info'] . "</div>\n";
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
