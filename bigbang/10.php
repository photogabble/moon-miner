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
// File: bigbang/10.php

$pos = strpos ($_SERVER['PHP_SELF'], "/10.php");
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
$variables['swordfish']  = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);
$variables['steps'] = $bigbang_info['steps'];
$variables['current_step'] = $bigbang_info['current_step'];
$variables['next_step'] = $bigbang_info['next_step'];

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('common', 'regional', 'footer', 'global_includes', 'create_universe'));
$template->AddVariables ('langvars', $langvars);

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('variables', $variables);
$template->display ("templates/classic/bigbang/10.tpl");
?>
