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
// File: faq.php

include './global_includes.php';

if (!isset ($_GET['lang']))
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

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('common', 'faq', 'global_funcs'));

$variables = null;
$variables['lang'] = $lang;
$variables['link'] = $link;
$variables['body_class'] = 'faq';

if (empty ($_SESSION['username']))
{
    $variables['linkback'] = array ("fulltext"=>$langvars['l_global_mlogin'], "link"=>"index.php");
}
else
{
    $variables['linkback'] = array ("fulltext"=>$langvars['l_global_mmenu'], "link"=>"index.php");
}

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvars";

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('langvars', $langvars);
$template->AddVariables ('variables', $variables);
$template->Display ("faq.tpl");
?>
