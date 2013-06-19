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
// File: bigbang/70.php

$pos = strpos ($_SERVER['PHP_SELF'], "/70.php");
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
$variables['spp']                    = filter_input (INPUT_POST, 'special', FILTER_SANITIZE_NUMBER_INT);
$variables['oep']                    = filter_input (INPUT_POST, 'ore', FILTER_SANITIZE_NUMBER_INT);
$variables['ogp']                    = filter_input (INPUT_POST, 'organics', FILTER_SANITIZE_NUMBER_INT);
$variables['gop']                    = filter_input (INPUT_POST, 'goods', FILTER_SANITIZE_NUMBER_INT);
$variables['enp']                    = filter_input (INPUT_POST, 'energy', FILTER_SANITIZE_NUMBER_INT);
$variables['nump']                   = filter_input (INPUT_POST, 'planets', FILTER_SANITIZE_NUMBER_INT);
$variables['empty']                  = $variables['sector_max'] - $variables['spp'] - $variables['oep'] - $variables['ogp'] - $variables['gop'] - $variables['enp'];
$variables['initscommod']            = filter_input (INPUT_POST, 'initscommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['initbcommod']            = filter_input (INPUT_POST, 'initbcommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['fedsecs']                = filter_input (INPUT_POST, 'fedsecs', FILTER_SANITIZE_NUMBER_INT);
$variables['loops']                  = filter_input (INPUT_POST, 'loops', FILTER_SANITIZE_NUMBER_INT);
$variables['swordfish']              = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('common', 'regional', 'footer', 'global_includes', 'create_universe'));

$p_add = 0;
$p_skip = 0;
$i = 0;

$table_timer = new Timer;
$table_timer->start (); // Start benchmarking

do
{
    $num = mt_rand (3, ($sector_max - 1));
    $select = $db->Execute ("SELECT {$db->prefix}universe.sector_id FROM {$db->prefix}universe, {$db->prefix}zones WHERE {$db->prefix}universe.sector_id=$num AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id AND {$db->prefix}zones.allow_planet='N'") or die ("DB error");

    // TODO: This select should have a line reflecting status in the template
    DbOp::dbResult ($db, $select, __LINE__, __FILE__);
    if ($select->RecordCount() == 0)
    {
        $insert = $db->Execute ("INSERT INTO {$db->prefix}planets (colonists, owner, corp, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, sector_id) VALUES (2, 0, 0, $default_prod_ore, $default_prod_organics, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp, $num)");
        $insert_result = DbOp::dbResult ($db, $insert, __LINE__, __FILE__);
        if ($insert_result === true)
        {
            $variables['setup_unowned_results']['result'] = true;
        }
        else
        {
            $variables['setup_unowned_results']['result'] = DbOp::dbResult ($db, $insert, __LINE__, __FILE__); // Unfortunately, this means we only display the last error, as it overwrites others
        }
        $p_add++;
    }
}
while ($p_add < $nump);

$table_timer->stop ();
$elapsed = $table_timer->elapsed ();
$elapsed = substr ($elapsed, 0, 5);
$variables['setup_unowned_results']['elapsed'] = $elapsed;
$variables['setup_unowned_results']['nump'] = $nump;

// Adds Sector Size * 2 amount of links to the links table
// Warning: Do no alter loopsize - This should be balanced 50%/50% PHP/MySQL load :)

$loopsize = 500;
$loops = round ($sector_max / $loopsize) + 1;
if ($loops <= 0) $loops = 1;

$variables['insert_link_loops'] = $loops;
$finish = $loopsize;
if ($finish > $sector_max) $finish = ($sector_max);
$start = 1;

for ($i = 1; $i <= $loops; $i++)
{
    $table_timer = new Timer;
    $table_timer->start (); // Start benchmarking
    $update = "INSERT INTO {$db->prefix}links (link_start,link_dest) VALUES ";
    for ($j = $start; $j < $finish; $j++)
    {
        $k = $j + 1;
        $update .= "($j,$k), ($k,$j)";
        if ($j < ($finish - 1)) $update .= ", "; else $update .= ";";
    }

    if ($start < $sector_max && $finish <= $sector_max)
    {
        $resx = $db->Execute ($update);
        $variables['insert_loop_sectors_results'][$i]['result'] = DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
    }
    else
    {
        $variables['insert_loop_sectors_results'][$i]['result'] = true; // Hard coded true, not sure what else to do.
    }

    $table_timer->stop ();
    $elapsed = $table_timer->elapsed ();
    $elapsed = substr ($elapsed, 0, 5);
    $variables['insert_loop_sectors_result'][$i]['elapsed'] = $elapsed;
    $variables['insert_loop_sectors_result'][$i]['loop'] = $i;
    $variables['insert_loop_sectors_result'][$i]['loops'] = $loops;
    $variables['insert_loop_sectors_result'][$i]['start'] = $start;

    if ($start == $finish)
    {
        $variables['insert_loop_sectors_result'][$i]['finish'] = $finish;
    }
    else
    {
        $variables['insert_loop_sectors_result'][$i]['finish'] = ($finish - 1);
    }

    $start = $finish;
    $finish += $loopsize;
    if ($finish > $sector_max) $finish = $sector_max;
}

// Adds Sector Size amount of links to the links table
// Warning: Do not alter loopsize - This should be balanced 50%/50% PHP/MySQL load :)

$loopsize = 500;
$loops = round ($sector_max / $loopsize)+1;
if ($loops <= 0) $loops = 1;

$variables['insert_oneway_loops'] = $loops;
$finish = $loopsize;
if ($finish > $sector_max) $finish = ($sector_max);
$start = 1;

for ($i = 1; $i <= $loops; $i++)
{
    $table_timer = new Timer;
    $table_timer->start (); // Start benchmarking
    $insert = "INSERT INTO {$db->prefix}links (link_start,link_dest) VALUES ";
    for ($j = $start; $j < $finish; $j++)
    {
        $link1 = intval (mt_rand (1, $sector_max - 1));
        $link2 = intval (mt_rand (1, $sector_max - 1));
        $insert .= "($link1, $link2)";
        if ($j < ($finish - 1)) $insert .= ", "; else $insert .= ";";
    }

    if ($start < $sector_max && $finish <= $sector_max)
    {
        $resx = $db->Execute ($insert);
        $variables['insert_random_oneway_results'][$i]['result'] = DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
    }
    else
    {
        $variables['insert_random_oneway_results'][$i]['result'] = true; // Hard-coded, not sure what else to do.
    }

    $table_timer->stop ();
    $elapsed = $table_timer->elapsed ();
    $elapsed = substr ($elapsed, 0, 5);

    $variables['insert_random_oneway_result'][$i]['elapsed'] = $elapsed;
    $variables['insert_random_oneway_result'][$i]['loop'] = $i;
    $variables['insert_random_oneway_result'][$i]['loops'] = $loops;
    $variables['insert_random_oneway_result'][$i]['start'] = $start;
    if ($start == $finish)
    {
        $variables['insert_random_oneway_result'][$i]['finish'] = $finish;
    }
    else
    {
        $variables['insert_random_oneway_result'][$i]['finish'] = ($finish - 1);
    }

    $start = $finish;
    $finish += $loopsize;
    if ($finish > $sector_max) $finish = ($sector_max);
}

// Adds (sector size * 2) amount of links to the links table ##
// Warning: Do not alter loopsize - This should be balanced 50%/50% PHP/MySQL load :)

$loopsize = 500;
$loops = round ($sector_max / $loopsize) + 1;
if ($loops <= 0) $loops = 1;

$variables['insert_twoway_loops'] = $loops;
$finish = $loopsize;
if ($finish > $sector_max) $finish = ($sector_max);
$start = 1;

for ($i = 1; $i <= $loops; $i++)
{
    $table_timer = new Timer;
    $table_timer->start (); // Start benchmarking
    $insert = "INSERT INTO {$db->prefix}links (link_start,link_dest) VALUES ";
    for ($j = $start; $j < $finish; $j++)
    {
        $link1 = intval (mt_rand (1, $sector_max - 1));
        $link2 = intval (mt_rand (1, $sector_max - 1));
        $insert .= "($link1, $link2), ($link2, $link1)";
        if ($j < ($finish - 1)) $insert .= ", "; else $insert .= ";";
    }

    if ($start < $sector_max && $finish <= $sector_max)
    {
        $resx = $db->Execute ($insert);
        $variables['insert_random_twoway_results'][$i]['result'] = DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
    }
    else
    {
        $variables['insert_random_twoway_results'][$i]['result'] = true; // Hard-coded, not sure what else to do.
    }

    $table_timer->stop ();
    $elapsed = $table_timer->elapsed ();
    $elapsed = substr ($elapsed, 0, 5);

    $variables['insert_random_twoway_result'][$i]['elapsed'] = $elapsed;
    $variables['insert_random_twoway_result'][$i]['loop'] = $i;
    $variables['insert_random_twoway_result'][$i]['loops'] = $loops;
    $variables['insert_random_twoway_result'][$i]['start'] = $start;
    if ($start == $finish)
    {
        $variables['insert_random_twoway_result'][$i]['finish'] = $finish;
    }
    else
    {
        $variables['insert_random_twoway_result'][$i]['finish'] = ($finish - 1);
    }

    $start = $finish;
    $finish += $loopsize;
    if ($finish > $sector_max) $finish = ($sector_max);
}

$table_timer = new Timer;
$table_timer->start (); // Start benchmarking
$resx = $db->Execute ("DELETE FROM {$db->prefix}links WHERE link_start = '{$sector_max}' OR link_dest ='{$sector_max}' ");
$variables['remove_links_results']['result'] = DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
$table_timer->stop ();
$elapsed = $table_timer->elapsed ();
$elapsed = substr ($elapsed, 0, 5);
$variables['remove_links_results']['elapsed'] = $elapsed;
$template->AddVariables ('langvars', $langvars);

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('variables', $variables);
$template->display ("templates/classic/bigbang/70.tpl");
?>
