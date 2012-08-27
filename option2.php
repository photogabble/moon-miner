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
// File: option2.php

include './global_includes.php';

if (check_login ($db, $lang, $langvars)) // Checks player login, sets playerinfo
{
    die();
}

global $l_opt2_title;
$title = $l_opt2_title;

// Initialize the hasher, with 8 (a base-2 log iteration count) for password stretching and without less-secure portable hashes for older systems
$hasher = new PasswordHash(8, false);

// Check the password against the stored hashed password
$password_match = $hasher->CheckPassword($_POST['oldpass'], $playerinfo['password']);

if ($newpass1 == $newpass2 && $password_match && $newpass1 != "")
{
    // Hash the password.  $hashedPassword will be a 60-character string.
    $hashed_pass = $hasher->HashPassword($newpass1);
}

if (!preg_match("/^[\w]+$/", $newlang))
{
    $newlang = $default_lang;
}
else
{
    $lang = $_POST['newlang'];
}

// New database driven language entries
load_languages($db, $lang, array('option2', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'), $langvars);

include './header.php';
bigtitle ();

if ($newpass1 == "" && $newpass2 == "")
{
    echo $l_opt2_passunchanged . "<br><br>";
}
elseif (!$password_match)
{
    echo $l_opt2_srcpassfalse . "<br><br>";
}
elseif ($newpass1 != $newpass2)
{
    echo $l_opt2_newpassnomatch . "<br><br>";
}
else
{
    $res = $db->Execute("SELECT ship_id,password FROM {$db->prefix}ships WHERE email=?", array($_SESSION['username']));
    db_op_result ($db, $res, __LINE__, __FILE__);
    $playerinfo = $res->fields;

    $res = $db->Execute("UPDATE {$db->prefix}ships SET password='$hashed_pass' WHERE ship_id=$playerinfo[ship_id]");
    db_op_result ($db, $res, __LINE__, __FILE__);
    if ($res)
    {
        echo $l_opt2_passchanged . "<br><br>";
    }
    else
    {
        echo $l_opt2_passchangeerr . "<br><br>";
    }
}

$res = $db->Execute("UPDATE {$db->prefix}ships SET lang='$lang' WHERE email=?", array($_SESSION['username']));
db_op_result ($db, $res, __LINE__, __FILE__);
foreach ($avail_lang as $curlang)
{
    if ($lang == $curlang['file'])
    {
        $l_opt2_chlang = str_replace("[lang]", "$curlang[name]", $l_opt2_chlang);
        echo $l_opt2_chlang . "<p>";
        break;
    }
}

echo "<br>";
TEXT_GOTOMAIN();
include './footer.php';
?>
