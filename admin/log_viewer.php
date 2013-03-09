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
// File: admin/log_viewer.php

if (strpos ($_SERVER['PHP_SELF'], 'log_viewer.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

$res = $db->Execute ("SELECT ship_id, character_name FROM {$db->prefix}ships ORDER BY character_name ASC");
\bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
while (!$res->EOF)
{
    $players[] = $res->fields;
    $res->MoveNext();
}

// Clear variables array before use, and set array with all used variables in page
$variables = null;
$variables['lang'] = $lang;
$variables['swordfish'] = $swordfish;
$variables['players'] = $players;

// Set the module name.
$variables['module'] = $module_name;

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvar";

$template->AddVariables('langvars', $langvars);
$template->AddVariables('variables', $variables);
?>
