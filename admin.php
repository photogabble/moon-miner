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
// File: admin.php

include './global_includes.php';
include './config/admin_pw.php';

// New database driven language entries
$langvars = null;
load_languages($db, $lang, array ('admin', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news', 'report', 'main', 'zoneedit', 'planet'), $langvars);

$title = $langvars['l_admin_title'];

function checked ($yesno)
{
    return (($yesno == "Y") ? "checked" : "");
}

$menu = null;
if (isset ($_POST['menu']))
{
    $menu = filter_input (INPUT_POST, 'menu', FILTER_SANITIZE_STRING); // We only want menu values that come from $_POST, and only want string values.
}

$swordfish  = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);
$filename = null;
$menu_location = null;
$button_main = false;

if ($swordfish == ADMIN_PW)
{
    $i = 0;
    $option_title = array ();
    $admin_dir = new DirectoryIterator ('admin/');
    foreach ($admin_dir as $file_info) // Get a list of the files in the admin directory
    {
        // This is to get around the issue of not having DirectoryIterator::getExtension.
        $file_ext = pathinfo ($file_info->getFilename(), PATHINFO_EXTENSION);

        // If it is a PHP file, add it to the list of accepted admin files
        if ($file_info->isFile () && $file_ext == 'php') // If it is a PHP file, add it to the list of accepted admin files
        {
            $i++; // Increment a counter, so we know how many files there are to choose from
            $filename[$i]['file'] = $file_info->getFilename (); // The actual file name
            $option_title = "l_admin_" . substr ($filename[$i]['file'], 0, -4); // Set the option title to a language string of the form l_admin + file name

            if (isset ($langvars[$option_title]))
            {
                $filename[$i]['option_title'] = $langvars[$option_title]; // The language translated title for option
            }
            else
            {
                $filename[$i]['option_title'] = $langvars['l_admin_new_module'] . $filename[$i]['file']; // The placeholder text for a not translated module
            }

            if (!empty ($menu))
            {
                if ($menu == $filename[$i]['file'])
                {
                    $button_main = true;
                    include './admin/' . $filename[$i]['file']; // Include that filename
                }
            }
        }
    }
}

// Clear variables array before use, and set array with all used variables in page
$variables = null;
$variables['lang'] = $lang;
$variables['swordfish'] = $swordfish;
$variables['admin_pw'] = ADMIN_PW;
$variables['linkback'] = array("fulltext"=>$langvars['l_global_mmenu'], "link"=>"main.php");
$variables['menu'] = $menu;
$variables['filename'] = $filename;
$variables['menu_location'] = $menu_location;
$variables['button_main'] = $button_main;

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvar";

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables('langvars', $langvars);
$template->AddVariables('variables', $variables);
$template->Display("admin.tpl");
?>
