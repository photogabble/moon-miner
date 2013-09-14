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

// Check to see if the language database has been installed yet.
$result = $db->Execute ("SELECT name, value FROM {$db->prefix}languages WHERE category = ? AND section = ?", array ('common', $lang));

if (($result instanceof ADORecordSet) && ($result != false)) // Before DB is installed, result will give false.
{
}
else
{
    // If not, redirect to create_universe.
    header ("Location: create_universe.php");
    die ();
}

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('main', 'login', 'logout', 'index', 'common','regional', 'footer','global_includes'));

$variables = null;
$variables['lang'] = $lang;
$variables['link'] = $link;
$variables['link_forums'] = $bntreg->get("link_forums");
$variables['admin_mail'] = $admin_mail;
$variables['body_class'] = 'index';

// Build list of languages from the languages directory
$lang_dir = new DirectoryIterator ('languages/');
$i = 0;

// Grab a drop-in supported list of the languages, their localized name, and their flag
foreach ($lang_dir as $file_info) // Get a list of the files in the languages directory
{
    // This is to get around the issue of not having DirectoryIterator::getExtension.
    $file_ext = pathinfo ($file_info->getFilename (), PATHINFO_EXTENSION);

    // If it is a PHP file, add it to the list of accepted language files
    if ($file_info->isFile () && $file_ext == 'php') // If it is a PHP file, add it to the list of accepted make galaxy files
    {
        $lang_file = substr ($file_info->getFilename (), 0, -8); // The actual file name

        // Select from the database and return the localized name of the language
        $result = $db->Execute ("SELECT value FROM {$db->prefix}languages WHERE category = 'regional' AND section = ? AND name = 'local_lang_name';", array ($lang_file));
        DbOp::dbResult ($db, $result, __LINE__, __FILE__);
        while ($result && !$result->EOF)
        {
            $row = $result->fields;

            $variables['list_of_langs'][$i]['lang_file'] = $lang_file;
            $variables['list_of_langs'][$i]['lang_name'] = $row['value'];

            // Now grab the flag name from the language table
            $result_flag = $db->Execute ("SELECT value FROM {$db->prefix}languages WHERE category = 'regional' AND section = ? AND name = 'local_lang_flag';", array ($lang_file));
            DbOp::dbResult ($db, $result_flag, __LINE__, __FILE__);
            while ($result_flag && !$result_flag->EOF)
            {
                $row_flag = $result_flag->fields;
                $variables['list_of_langs'][$i]['flag'] = $row_flag['value'];
                $result_flag->MoveNext();
            }

            $i++;
            $result->MoveNext();
        }
    }
}

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvars";

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('langvars', $langvars);
$template->AddVariables ('variables', $variables);
$template->Display ("index.tpl");
?>
