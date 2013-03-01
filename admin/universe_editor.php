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
// File: admin/universe_editor.php

if (strpos ($_SERVER['PHP_SELF'], 'universe_editor.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

$i = 0;
$changed_sectors = '';
$action  = filter_input (INPUT_POST, 'action', FILTER_SANITIZE_STRING);
$radius  = filter_input (INPUT_POST, 'radius', FILTER_SANITIZE_NUMBER_INT);

if ($action == "doexpand")
{
    $result = $db->Execute ("SELECT sector_id FROM {$db->prefix}universe ORDER BY sector_id ASC");
    \bnt\dbop::dbresult ($db, $result, __LINE__, __FILE__);

    if (!$result->EOF)
    {
        $resa = $db->StartTrans (); // We enclose the updates in a transaction as it is faster
        \bnt\dbop::dbresult ($db, $resa, __LINE__, __FILE__);

        // Begin transaction
        while (!$result->EOF)
        {
            $row = $result->fields;
            $distance = mt_rand (1, $radius);
            $resx = $db->Execute("UPDATE {$db->prefix}universe SET distance = ? WHERE sector_id = ?", array ($distance, $row['sector_id']));
            \bnt\dbop::dbresult ($db, $resx, __LINE__, __FILE__);

            $changed_sectors[$i] = str_replace("[sector]", $row['sector_id'], $langvars['l_admin_updated_distance']);
            $changed_sectors[$i] = str_replace("[distance]", $distance, $changed_sectors[$i]);
            $i++;
            $result->MoveNext();
        }

        // End transaction
        $trans_status = $db->CompleteTrans(); // Complete the transaction
        \bnt\dbop::dbresult ($db, $trans_status, __LINE__, __FILE__);
    }
}

$title = $langvars['l_change_uni_title'];

// Clear variables array before use, and set array with all used variables in page
$variables = null;
$variables['lang'] = $lang;
$variables['changed_sectors'] = $changed_sectors;
$variables['swordfish'] = $swordfish;
$variables['universe_size'] = $universe_size;
$variables['action'] = $action;
$variables['radius'] = $radius;

// Set the module name.
$variables['module'] = $module_name;

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvar";

$template->AddVariables('langvars', $langvars);
$template->AddVariables('variables', $variables);
?>
