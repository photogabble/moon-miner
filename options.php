<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: options.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

$body_class = 'options';

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('options', 'common', 'global_includes', 'global_funcs', 'footer'));
$title = $langvars['l_opt_title'];
Bnt\Header::display($pdo_db, $lang, $template, $title, $body_class);

echo "<h1>" . $title . "</h1>\n";
echo "<body class = " . $body_class . ">";
$res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array($_SESSION['username']));
$playerinfo = $res->fields;

echo "<form accept-charset='utf-8' action=option2.php method=post>";
echo "<table>";
echo "<tr>";
echo "<th colspan=2><strong>" . $langvars['l_opt_chpass'] . "</strong></th>";
echo "</tr>";
echo "<tr>";
echo "<td>" . $langvars['l_opt_curpass'] . "</td>";
echo "<td><input type=password name=oldpass size=20 maxlength=20 value=\"\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>" . $langvars['l_opt_newpass'] . "</td>";
echo "<td><input type=password name=newpass1 size=20 maxlength=20 value=\"\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>" . $langvars['l_opt_newpagain'] . "</td>";
echo "<td><input type=password name=newpass2 size=20 maxlength=20 value=\"\"></td>";
echo "</tr>";
echo "<tr>";
echo "<th colspan=2><strong>" . $langvars['l_opt_lang'] . "</strong></th>";
echo "</tr>";
echo "<tr>";
echo "<td>" . $langvars['l_opt_select'] . "</td><td><select name=newlang>";

$lang_dir = new DirectoryIterator('languages/');
foreach ($lang_dir as $file_info) // Get a list of the files in the languages directory
{
    // If it is a PHP file, add it to the list of accepted language files
    if ($file_info->isFile() && $file_info->getExtension() == 'php') // If it is a PHP file, add it to the list of accepted make galaxy files
    {
        $lang_file = mb_substr($file_info->getFilename(), 0, -8); // The actual file name

        // Select from the database and return the localized name of the language
        $result = $db->Execute("SELECT value FROM {$db->prefix}languages WHERE category = 'regional' AND section = ? AND name = 'local_lang_name';", array($lang_file));
        Bnt\Db::logDbErrors($db, $result, __LINE__, __FILE__);
        while ($result && !$result->EOF)
        {
            $row = $result->fields;

            if ($lang_file == $playerinfo['lang'])
            {
                $selected = " selected";
            }
            else
            {
                $selected = null;
            }
            echo "<option value='" . $lang_file . "'" . $selected . ">" . $row['value'] . "</option>";
            $result->MoveNext();
        }
    }
}

echo "</select></td>";
echo "</tr>";
echo "</table>";
echo "<br>";
echo "<input type=submit value=" . $langvars['l_opt_save'] . ">";
echo "</form><br>";

Bnt\Text::gotoMain($db, $lang, $langvars);
Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
