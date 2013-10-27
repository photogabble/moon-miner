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
// File: emerwarp.php

include './global_includes.php';

BntLogin::checkLogin ($db, $lang, $langvars, $bntreg, $template);

// Always make sure we are using empty vars before use.
$variables = null;

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('emerwarp', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

if ($playerinfo['dev_emerwarp'] > 0)
{
    $dest_sector = mt_rand (0, $sector_max - 1);
    $result_warp = $db->Execute ("UPDATE {$db->prefix}ships SET sector = ?, dev_emerwarp = dev_emerwarp - 1 WHERE ship_id = ?;", array ($dest_sector, $playerinfo['ship_id']));
    BntDb::logDbErrors ($db, $result_warp, __LINE__, __FILE__);
    BntLogMove::writeLog ($db, $playerinfo['ship_id'], $dest_sector);
    $langvars['l_ewd_used'] = str_replace ("[sector]", $dest_sector, $langvars['l_ewd_used']);
    $variables['dest_sector'] = $dest_sector;
}

$variables['body_class'] = 'emerwarp';
$variables['playerinfo_dev_emerwarp'] = $playerinfo['dev_emerwarp'];
$variables['linkback'] = array ("fulltext"=>$langvars['l_global_mmenu'], "link"=>"main.php");

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvar";

// Pull in footer variables from footer_t.php
include './footer_t.php';
// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('emerwarp', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));
$template->AddVariables ('langvars', $langvars);
$template->AddVariables ('variables', $variables);
$template->Display ("emerwarp.tpl");
?>
