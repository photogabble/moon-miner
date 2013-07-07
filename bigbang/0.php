<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: bigbang/0.php

$pos = strpos ($_SERVER['PHP_SELF'], "/0.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die ();
}

// Determine current step, next step, and number of steps
$bigbang_info = BntBigBang::findStep (__FILE__);

// Set variables
$variables['templateset'] = $bntreg->get ("default_template");
$variables['body_class'] = 'bigbang';
$variables['steps'] = $bigbang_info['steps'];
$variables['current_step'] = $bigbang_info['current_step'];
$variables['next_step'] = $bigbang_info['next_step'];

$lang_dir = new DirectoryIterator ('languages/');
$lang_list = array ();
$i = 0;
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
            $variables['lang_list'][$i]['file'] = $lang_file;
            $variables['lang_list'][$i]['value'] = $row['value'];
            $variables['lang_list'][$i]['selected'] = $bntreg->get("default_lang");
            $i++;
            $result->MoveNext();
        }
    }
}
$variables['lang_list']['size'] = $i -1;

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('common', 'regional', 'footer', 'global_includes', 'create_universe', 'options', 'news'));
$template->AddVariables ('langvars', $langvars);

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('variables', $variables);
$template->display ("templates/classic/bigbang/0.tpl");
?>
