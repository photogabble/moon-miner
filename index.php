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
// File: index.php

$index_page = true;
include './global_includes.php';

if (!isset($_GET['lang']))
{
    $_GET['lang'] = null;
    $lang = $default_lang;
    $link = '';
}
else
{
    $lang = $_GET['lang'];
    $link = "?lang=" . $lang;
}

// Check to see if the language database has been installed yet.
$result = $db->Execute("SELECT name, value FROM {$db->prefix}languages WHERE category=? AND section=?;", array('common', $lang));
if (!$result)
{
    // If not, redirect to create_universe.
    header ("Location: create_universe.php");
    die ();
}

// New database driven language entries
$langvars = null;
load_languages($db, $lang, array('main', 'login', 'logout', 'index', 'common'), $langvars);

$title = $l_welcome_bnt;

$variables = null;
$variables['lang'] = $lang;
$variables['link'] = $link;
$variables['link_forums'] = $link_forums;
$variables['admin_mail'] = $admin_mail;
$variables['body_class'] = 'index';

//include './header.php';
// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvar";

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables('langvars', $langvars);
$template->AddVariables('variables', $variables);
$template->Display("index.tpl");
?>
