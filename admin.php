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
load_languages($db, $lang, array ('admin', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news', 'report', 'main', 'zoneedit', 'planet'), $langvars);

$title = $langvars['l_admin_title'];
include './header.php';
echo "<h1>" . $title . "</h1>\n";

function checked ($yesno)
{
    return (($yesno == "Y") ? "checked" : "");
}

if (isset ($_POST['menu']))
{
    $menu = filter_input(INPUT_POST, 'menu', FILTER_SANITIZE_STRING); // We only want menu values that come from $_POST, and only want string values.
}

if (!isset ($_POST['swordfish']))
{
    $_POST['swordfish'] = null;
}

if ($_POST['swordfish'] != ADMIN_PW)
{
    echo "<form action='admin.php' method='post'>";
    echo $langvars['l_admin_password'] . ": <input type='password' name='swordfish' size='20' maxlength='20'>&nbsp;&nbsp;";
    echo "<input type='submit' value='" . $langvars['l_submit'] . "'><input type='reset' value='" . $langvars['l_reset'] . "'>";
    echo "</form>";
}
else
{
    $i = 0;
    $admin_dir = new DirectoryIterator ('admin/');
    foreach ($admin_dir as $file_info) // Get a list of the files in the admin directory
    {
        if ($file_info->isFile () && $file_info->getExtension () == 'php') // If it is a PHP file, add it to the list of accepted admin files
        {
            $i++; // Increment a counter, so we know how many files there are to choose from
            $filename[$i] = $file_info->getFilename ();
            $option_title[$i] = "l_admin_" . substr ($filename[$i], 0, -4); // Set the option title to a language string of the form l_admin + file name
        }
    }

    if (empty ($menu))
    {
        echo $langvars['l_admin_welcome'] . "<br><br>";
        echo $langvars['l_admin_menulist'] . "<br>";
        echo "<form action='admin.php' method='post'>";
        echo "<select name='menu'>";

        while ($i > 0) // While there are choices left, create a dropdown menu of them
        {
            if (isset ($langvars[$option_title[$i]])) // If we have a language name for the file, use that as the option choice
            {
                echo "<option value='" . $filename[$i] . "'>" . $langvars[$option_title[$i]] . "</option>";
            }
            else // But if it is a new module that doesn't have a language name yet, offer its filename as the name in the drop down
            {
                echo "<option value='" . $filename[$i] . "'>" . $langvars['l_admin_new_module'] . $filename[$i] . "</option>";
            }

            $i--;
        }

        echo "</select>";
        echo "<input type='hidden' name='swordfish' value='" . $_POST['swordfish'] . "'>";
        echo "&nbsp;<input type='submit' value='" . $langvars['l_submit'] . "'>";
        echo "</form>";
    }
    else
    {
        $button_main = true;
        $menu_location = array_search ($menu, $filename); // Get the array index/location for the chosen module
        if ($menu_location !== false) // If the chosen module is one of the files in the admin directory
        {
            include './admin/' . $filename[$menu_location]; // Include that filename
        }
        else
        {
            echo $langvars['l_admin_unknown_function']; // Otherwise report back that it is an unknown module
        }

        if ($button_main)
        {
            echo "<p>";
            echo "<form action=admin.php method=post>";
            echo "<input type='hidden' name=swordfish value=" . $_POST['swordfish'] . ">";
            echo "<input type='submit' value='" . $langvars['l_admin_return_admin_menu'] . "'>";
            echo "</form>";
        }
    }
}

echo "<br>";
echo str_replace("[here]", "<a href='main.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mmenu']);
include './footer.php';
?>
