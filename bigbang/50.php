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
// File: bigbang/50.php

$pos = strpos ($_SERVER['PHP_SELF'], "/50.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die ();
}

// Determine current step, next step, and number of steps
$bigbang_info = BntBigBang::findStep (__FILE__);

// Set variables
$variables['templateset']            = $bntreg->get ("default_template");
$variables['body_class']             = 'bigbang';
$variables['steps']                  = $bigbang_info['steps'];
$variables['current_step']           = $bigbang_info['current_step'];
$variables['next_step']              = $bigbang_info['next_step'];
$variables['sector_max']             = (int) filter_input (INPUT_POST, 'sektors', FILTER_SANITIZE_NUMBER_INT); // Sanitize the input and typecast it to an int
$variables['spp']                    = filter_input (INPUT_POST, 'spp', FILTER_SANITIZE_NUMBER_INT);
$variables['oep']                    = filter_input (INPUT_POST, 'oep', FILTER_SANITIZE_NUMBER_INT);
$variables['ogp']                    = filter_input (INPUT_POST, 'ogp', FILTER_SANITIZE_NUMBER_INT);
$variables['gop']                    = filter_input (INPUT_POST, 'gop', FILTER_SANITIZE_NUMBER_INT);
$variables['enp']                    = filter_input (INPUT_POST, 'enp', FILTER_SANITIZE_NUMBER_INT);
$variables['nump']                   = filter_input (INPUT_POST, 'nump', FILTER_SANITIZE_NUMBER_INT);
$variables['empty']                  = $variables['sector_max'] - $variables['spp'] - $variables['oep'] - $variables['ogp'] - $variables['gop'] - $variables['enp'];
$variables['initscommod']            = filter_input (INPUT_POST, 'initscommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['initbcommod']            = filter_input (INPUT_POST, 'initbcommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['fedsecs']                = filter_input (INPUT_POST, 'fedsecs', FILTER_SANITIZE_NUMBER_INT);
$variables['loops']                  = filter_input (INPUT_POST, 'loops', FILTER_SANITIZE_NUMBER_INT);
$variables['swordfish']              = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);
$variables['autorun']                = filter_input (INPUT_POST, 'autorun', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('common', 'regional', 'footer', 'global_includes', 'create_universe'));

$i = 0;
$table_timer = new Timer;
$table_timer->start (); // Start benchmarking
$language_files = new DirectoryIterator ("languages/");
$lang_file_import_results = Array ();

foreach ($language_files as $language_filename)
{
    $table_timer = new Timer;
    $table_timer->start (); // Start benchmarking

    // This is to get around the issue of not having DirectoryIterator::getExtension.
    $file_ext = pathinfo ($language_filename->getFilename (), PATHINFO_EXTENSION);
    if ($language_filename->isFile () && $file_ext == 'php')
    {
        $lang_name = substr ($language_filename->getFilename(), 0, -8);

        // Import Languages
        $table_timer->start (); // Start benchmarking
        $lang_result = BntFile::iniToDb ($db, "languages/" . $language_filename->getFilename(), "languages", $lang_name, $bntreg);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $variables['import_lang_results'][$i]['time'] = $elapsed;
        $variables['import_lang_results'][$i]['name'] = ucwords ($lang_name);
        $variables['import_lang_results'][$i]['result'] = $lang_result;
        $i++;
    }
}
$variables['language_count'] = ($i - 1);
$gameconfig_result = BntFile::iniToDb ($db, "config/configset_classic.ini.php", "gameconfig", "game", $bntreg);
$table_timer->stop ();
$elapsed = $table_timer->elapsed ();
$elapsed = substr ($elapsed, 0, 5);
if ($gameconfig_result === true)
{
    $variables['import_config_results']['result'] = true;
    $variables['import_config_results']['time'] = $elapsed;
    $db->inactive = false;
}
else
{
    $variables['import_config_results']['result'] = $gameconfig_result;
    $variables['import_config_results']['time'] = $elapsed;
}

$lang = $bntreg->get ('default_lang');
$template->AddVariables ('langvars', $langvars);

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('variables', $variables);
$template->display ("templates/classic/bigbang/50.tpl");
?>
