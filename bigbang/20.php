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
// File: bigbang/20.php

$pos = strpos ($_SERVER['PHP_SELF'], "/20.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die ();
}

// Determine current step, next step, and number of steps
$bigbang_info = BntBigBang::findStep (__FILE__);

// Set variables
$variables['templateset']  = $bntreg->get ("default_template");
$variables['body_class']   = 'bigbang';
$variables['steps']        = $bigbang_info['steps'];
$variables['current_step'] = $bigbang_info['current_step'];
$variables['next_step']    = $bigbang_info['next_step'];
$variables['sector_max']   = (int) filter_input (INPUT_POST, 'sektors', FILTER_SANITIZE_NUMBER_INT); // Sanitize the input and typecast it to an int
$variables['spp']          = round ($variables['sector_max'] * filter_input (INPUT_POST, 'special', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['oep']          = round ($variables['sector_max'] * filter_input (INPUT_POST, 'ore', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['ogp']          = round ($variables['sector_max'] * filter_input (INPUT_POST, 'organics', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['gop']          = round ($variables['sector_max'] * filter_input (INPUT_POST, 'goods', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['enp']          = round ($variables['sector_max'] * filter_input (INPUT_POST, 'energy', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['nump']         = round ($variables['sector_max'] * filter_input (INPUT_POST, 'planets', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['empty']        = $variables['sector_max'] - $variables['spp'] - $variables['oep'] - $variables['ogp'] - $variables['gop'] - $variables['enp'];
$variables['initscommod']  = filter_input (INPUT_POST, 'initscommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['initbcommod']  = filter_input (INPUT_POST, 'initbcommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['fedsecs']      = filter_input (INPUT_POST, 'fedsecs', FILTER_SANITIZE_NUMBER_INT);
$variables['loops']        = filter_input (INPUT_POST, 'loops', FILTER_SANITIZE_NUMBER_INT);
$variables['swordfish']    = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('common', 'regional', 'footer', 'global_includes', 'create_universe'));
$template->AddVariables ('langvars', $langvars);

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('variables', $variables);
$template->display ("templates/classic/bigbang/20.tpl");
?>
