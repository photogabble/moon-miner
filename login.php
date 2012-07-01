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
// File: login.php

include "config.php";

if (empty($lang))
{
    $lang = $default_lang;
}

$found = 0;
if (!empty($newlang))
{
    if (!preg_match("/^[\w]+$/", $lang))
    {
        $lang = $default_lang;
    }
    foreach ($avail_lang as $key => $value)
    {
        if ($newlang == $value[file])
        {
            $lang = $newlang;
            $_SESSION['lang'] = $lang;
            $found = 1;
            break;
        }
    }

    if ($found == 0)
    {
        $lang = $default_lang;
    }

    $lang = $lang . ".inc";
}

include "languages/$lang";
$title = $l_login_title;
include "header.php";

global $title;
if (!isset($username))
{
    $username = '';
}

if (!isset($password))
{
    $password = '';
}

echo "<h1 style='text-align:center'>$title</h1>\n";
echo "<br><br>\n";
echo "<form action='login2.php' method='post'>\n";
echo "    <dl class='twocolumn-form'>\n";
echo "        <dt style='padding:3px'><label for='email'>{$l_login_email}:</label></dt>\n";
echo "        <dd style='padding:3px'><input type='text' id='email' name='email' size='20' maxlength='40' value='{$username}' style='width:200px'></dd>\n";
echo "        <dt style='padding:3px'><label for='pass'>{$l_login_pw}</label></dt>\n";
echo "        <dd style='padding:3px'><input type='password' id='pass' name='pass' size='20' maxlength='20' value='{$password}' style='width:200px'></dd>\n";
echo "    </dl>\n";
echo "    <br style='clear:both;'>";
echo "    <div style='text-align:center'>Forgot your password?  Enter it blank and press login.</div><br>\n";
echo "    <div style='text-align:center'>";
echo "        <input type='submit' value='{$l_login_title}'>\n";
echo "        <br><br>\n";
$l_login_newp = str_replace("[here]", "<a href='new.php'>" . $l_here . "</a>", $l_login_newp);
echo "        {$l_login_newp}\n";
echo "        <br>\n";
echo "        <br>\n";
echo "        {$l_login_prbs} <a href='mailto:{$admin_mail}'>{$l_login_emailus}</a>\n";
echo "    </div>";
echo "</form>\n";


echo "<div style='text-align:center'>";
if (!empty($link_forums))
{
    echo "<a href='$link_forums' target='_blank'>$l_forums</a> - ";
}
echo "<a href='ranking.php'>{$l_rankings}</a> - <a href='settings.php'>{$l_login_settings}</a><br><br>\n";
echo "</div>\n";
echo "<form action='login.php' method='post'>\n";
echo "<div style='text-align:center'>$l_login_lang&nbsp;&nbsp;<select name='newlang'>\n";

foreach ($avail_lang as $curlang)
{
    if ($curlang['file'].".inc" == $lang)
    {
        $selected = "selected='selected'";
    }
    else
    {
        $selected = "";
    }
    echo "  <option value='{$curlang['file']}' {$selected} style='width:100px;'>{$curlang['name']}</option>\n";
}

echo "  </select>\n&nbsp;&nbsp;<input type='submit' value='{$l_login_change}'></div>";
echo "</form>\n";

include "footer.php";
?>
