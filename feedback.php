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
// File: feedback.php

include './global_includes.php';

BntLogin::checkLogin ($db, $lang, $langvars, $bntreg, $template);

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('feedback'));

$title = $langvars['l_feedback_title'];
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('feedback', 'galaxy', 'common', 'global_includes', 'global_funcs', 'footer'));
echo "<h1>" . $title . "</h1>\n";

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
DbOp::dbResult ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

if (array_key_exists ('content', $_POST) === false)
{
    echo "<form action=feedback.php method=post>\n";
    echo "<table>\n";
    echo "<tr><td>" . $langvars['l_feedback_to'] . "</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=GameAdmin></td></tr>\n";
    echo "<tr><td>" . $langvars['l_feedback_from'] . "</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=\"$playerinfo[character_name] - $playerinfo[email]\"></td></tr>\n";
    echo "<tr><td>" . $langvars['l_feedback_topi'] . "</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=" . $langvars['l_feedback_feedback'] . "></td></tr>\n";
    echo "<tr><td>" . $langvars['l_feedback_message'] . "</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>\n";
    echo "<tr><td></td><td><input type=submit value=" . $langvars['l_submit'] . "><input type=reset value=" . $langvars['l_reset'] . "></td>\n";
    echo "</table>\n";
    echo "</form>\n";
    echo "<br>" . $langvars['l_feedback_info'] . "<br>\n";
}
else
{
    $link_to_game = "http://";
    $link_to_game .= ltrim ($gamedomain,".");// Trim off the leading . if any
    $link_to_game .= $gamepath;
    mail ("$admin_mail", $langvars['l_feedback_subj'], "IP address - $ip\r\nGame Name - {$playerinfo['character_name']}\r\nServer URL - {$link_to_game}\r\n\r\n{$_POST['content']}","From: {$playerinfo['email']}\r\nX-Mailer: PHP/" . phpversion());
    echo $langvars['l_feedback_messent'] . "<br><br>";
}

echo "<br>\n";
if (empty ($_SESSION['username']))
{
    echo str_replace ("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
}
else
{
    BntText::gotoMain ($db, $lang, $langvars);
}

include './footer.php';
?>
