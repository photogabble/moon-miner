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
// File: mail.php

include './global_includes.php';

$title = $langvars['l_mail_title'];
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('mail', 'common', 'global_funcs', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'));
echo "<h1>" . $title . "</h1>\n";

$result = $db->Execute ("SELECT character_name, email, password FROM {$db->prefix}ships WHERE email = ? LIMIT 1;", array ($mail));
DbOp::dbResult ($db, $result, __LINE__, __FILE__);

if (!$result->EOF)
{
    $playerinfo = $result->fields;
    $langvars['l_mail_message'] = str_replace ("[pass]", $playerinfo['password'], $langvars['l_mail_message']);
    $langvars['l_mail_message'] = str_replace ("[name]", $playerinfo['character_name'], $langvars['l_mail_message']);
    $langvars['l_mail_message'] = str_replace ("[ip]", $ip, $langvars['l_mail_message']);
    $langvars['l_mail_message'] = str_replace ("[game_name]", $game_name, $langvars['l_mail_message']);

    # Some reason \r\n is broken, so replace them now.
    $langvars['l_mail_message'] = str_replace ('\r\n', "\r\n", $langvars['l_mail_message']);

    # Need to set the topic with the game name.
    $langvars['l_mail_topic'] = str_replace ("[game_name]", $game_name, $langvars['l_mail_topic']);

    $link_to_game = "http://";
    $link_to_game .= ltrim ($gamedomain, ".");// Trim off the leading . if any
    $link_to_game .= $gamepath;

    mail ($playerinfo['email'], $langvars['l_mail_topic'], $langvars['l_mail_message'] . "\r\n\r\n{$link_to_game}\r\n", "From: {$admin_mail}\r\nReply-To: {$admin_mail}\r\nX-Mailer: PHP/" . phpversion());
    echo "<div style='color:#fff; width:400px; text-align:left; padding:6px;'>" . $langvars['l_mail_sent'] . " <span style='color:#0f0;'>{$mail}</span></div>\n";
    echo "<br>\n";
    echo "<div style='font-size:14px; font-weight:bold; color:#f00;'>Please Note: If you do not receive your emails within 5 to 10 mins of it being sent, please notify us as soon as possible either by email or on the forums.<br>DO NOT CREATE ANOTHER ACCOUNT, YOU MAY GET BANNED.</div>\n";
}
else
{
    $langvars['l_mail_noplayer'] = str_replace ("[here]", "<a href='new.php'>" . $langvars['l_here'] . "</a>", $langvars['l_mail_noplayer']);
    echo "<div style='color:#FFF; width:400px; text-align:left; font-size:12px; padding:6px;'>" . $langvars['l_mail_noplayer'] . "</div>\n";

    echo "<br>\n";
    if (isset ($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)
    {
        echo str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
    }
    else
    {
        echo str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
    }
}

include './footer.php';
?>
