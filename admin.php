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
load_languages($db, $lang, array('admin', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news', 'report', 'main', 'zoneedit', 'planet'), $langvars);

$title = $langvars['l_admin_title'];
include './header.php';
echo "<h1>" . $title . "</h1>\n";

function checked ($yesno)
{
    return(($yesno == "Y") ? "checked" : "");
}

if (isset($_POST['menu']))
{
    $module = $_POST['menu'];
}

if (!isset($_POST['swordfish']))
{
    $_POST['swordfish'] = '';
}

if ($_POST['swordfish'] != ADMIN_PW)
{
    echo "<form action='admin.php' method='post'>";
    echo "Password: <input type='password' name='swordfish' size='20' maxlength='20'>&nbsp;&nbsp;";
    echo "<input type='submit' value='" . $langvars['l_submit'] . "'><input type='reset' value='" . $langvars['l_reset'] . "'>";
    echo "</form>";
}
else
{
    if (empty($module))
    {
        echo $langvars['l_admin_welcome'] . "<br><br>";
        echo $langvars['l_admin_menulist'] . "<br>";
        echo "<form action='admin.php' method='post'>";
        echo "<select name='menu'>";
        echo "<option value='useredit' selected>" . $langvars['l_admin_user_editor'] . "</option>";
        echo "<option value='univedit'>" . $langvars['l_admin_universe_editor'] . "</option>";
        echo "<option value='sectedit'>" . $langvars['l_admin_sector_editor'] . "</option>";
        echo "<option value='planedit'>" . $langvars['l_admin_planet_editor'] . "</option>";
        echo "<option value='linkedit'>" . $langvars['l_admin_link_editor'] . "</option>";
        echo "<option value='zoneedit'>" . $langvars['l_admin_zone_editor'] . "</option>";
        echo "<option value='banedit'>" . $langvars['l_admin_bans_editor'] . "</option>";
        echo "<option value='logview'>" . $langvars['l_admin_log_viewer'] . "</option>";
        echo "</select>";
        echo "<input type='hidden' name='swordfish' value='" . $_POST['swordfish'] . "'>";
        echo "&nbsp;<input type='submit' value='" . $langvars['l_submit'] . "'>";
        echo "</form>";
    }
    else
    {
        $button_main = true;
        if ($module == "useredit")
        {
            include './admin/user_editor.php';
        }
        elseif ($module == "univedit")
        {
            include './admin/universe_editor.php';
        }
        elseif ($module == "sectedit")
        {
            include './admin/sector_editor.php';
        }
        elseif ($module == "planedit")
        {
            include './admin/planet_editor.php';
        }
        elseif ($module == "linkedit")
        {
            include './admin/link_editor.php';
            echo "<strong>Link editor</strong>";
        }
        elseif ($module == "zoneedit")
        {
            include './admin/zone_editor.php';
        }
        elseif ($module == "logview")
        {
            include 'admin/log_viewer.php';
        }
        elseif ($module == "banedit")
        {
            include 'admin/bans_editor.php';
        }
        else
        {
            echo $langvars['l_admin_unknown_function'];
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
