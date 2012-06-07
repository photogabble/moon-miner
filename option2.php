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

include("config.php");
if (checklogin())
{
    die();
}
$title=$l_opt2_title;

if ($intrf == "N")
{
    $interface = "main.php";
    setcookie("interface", "main.php");
}
else
{
    $intrf = "O";
    $interface = "maintext.php";
    setcookie("interface", "maintext.php");
}

if ($newpass1 == $newpass2 && $password == $oldpass && $newpass1 != "")
{
    $userpass = $username."+".$newpass1;
    setcookie("userpass",$userpass,time()+(3600*24)*365,$gamepath,$gamedomain);
    setcookie("id",$id);
}
if (!preg_match("/^[\w]+$/", $newlang))
{
    $newlang = $default_lang;
}

$lang=$newlang;
SetCookie("lang",$lang,time()+(3600*24)*365,$gamepath,$gamedomain);
include("languages/$lang" . ".inc");
include("header.php");
bigtitle();

if ($newpass1 == "" && $newpass2 == "")
{
    echo $l_opt2_passunchanged;
}
elseif ($password != $oldpass)
{
    echo $l_opt2_srcpassfalse;
}
elseif ($newpass1 != $newpass2)
{
    echo $l_opt2_newpassnomatch;
}
else
{
    $res = $db->Execute("SELECT ship_id,password FROM $dbtables[ships] WHERE email='$username'");
    $playerinfo = $res->fields;
    if ($oldpass != $playerinfo[password])
    {
        echo $l_opt2_srcpassfalse;
    }
    else
    {
        $res = $db->Execute("UPDATE $dbtables[ships] SET password='$newpass1' WHERE ship_id=$playerinfo[ship_id]");
        if ($res)
        {
            echo $l_opt2_passchanged;
        }
        else
        {
            echo $l_opt2_passchangeerr;
        }
    }
}

$res = $db->Execute("UPDATE $dbtables[ships] SET interface='$intrf' WHERE email='$username'");
if ($res)
{
    echo $l_opt2_userintup;
}
else
{
    echo $l_opt2_userintfail;
}

$res = $db->Execute("UPDATE $dbtables[ships] SET lang='$lang' WHERE email='$username'");
foreach ($avail_lang as $curlang)
{
    if ($lang == $curlang[file])
    {
        $l_opt2_chlang = str_replace("[lang]", "$curlang[name]", $l_opt2_chlang);
        echo $l_opt2_chlang;
        break;
    }
}

if ($dhtml != 'Y')
{
    $dhtml = 'N';
}

$res = $db->Execute("UPDATE $dbtables[ships] SET dhtml='$dhtml' WHERE email='$username'");
if ($res)
{
    echo $l_opt2_dhtmlup;
}
else
{
    echo $l_opt2_dhtmlfail;
}

TEXT_GOTOMAIN();
include("footer.php");
?>
