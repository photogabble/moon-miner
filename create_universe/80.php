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
// File: create_universe/80.php

$pos = strpos ($_SERVER['PHP_SELF'], "/80.php");
if ($pos !== false)
{
    echo "You can not access this file directly!";
    die ();
}

// Determine current step, next step, and number of steps
$create_universe_info = BntBigBang::findStep (__FILE__);

// Set variables
$variables['templateset']            = $bntreg->get ("default_template");
$variables['body_class']             = 'create_universe';
$variables['steps']                  = $create_universe_info['steps'];
$variables['current_step']           = $create_universe_info['current_step'];
$variables['next_step']              = $create_universe_info['next_step'];
$variables['sector_max']             = (int) filter_input (INPUT_POST, 'sektors', FILTER_SANITIZE_NUMBER_INT); // Sanitize the input and typecast it to an int
$variables['spp']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'special', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['oep']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'ore', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['ogp']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'organics', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['gop']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'goods', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['enp']                    = round ($variables['sector_max'] * filter_input (INPUT_POST, 'energy', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['nump']                   = round ($variables['sector_max'] * filter_input (INPUT_POST, 'planets', FILTER_SANITIZE_NUMBER_INT) / 100);
$variables['empty']                  = $variables['sector_max'] - $variables['spp'] - $variables['oep'] - $variables['ogp'] - $variables['gop'] - $variables['enp'];
$variables['initscommod']            = filter_input (INPUT_POST, 'initscommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['initbcommod']            = filter_input (INPUT_POST, 'initbcommod', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$variables['fedsecs']                = filter_input (INPUT_POST, 'fedsecs', FILTER_SANITIZE_NUMBER_INT);
$variables['loops']                  = filter_input (INPUT_POST, 'loops', FILTER_SANITIZE_NUMBER_INT);
$variables['swordfish']              = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);
$variables['autorun']                = filter_input (INPUT_POST, 'autorun', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$variables['newlang']                = filter_input (INPUT_POST, 'newlang', FILTER_SANITIZE_URL);
$lang = $newlang; // Set the language to the language chosen during create universe

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('common', 'regional', 'footer', 'global_includes', 'create_universe', 'news'));
$variables['update_ticks_results']['sched'] = $sched_ticks;
$local_table_timer = new BntTimer;

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_turns, 'sched_turns.php', ?)", array (time ()));
$variables['update_turns_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_turns_results']['sched'] = $sched_turns;
$local_table_timer->stop ();
$variables['update_turns_results']['elapsed'] = $local_table_timer->elapsed ();

// This is causing errors at the moment, disabling until we get clean solutions for it.
//$local_table_timer->start (); // Start benchmarking
//$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_turns, 'sched_xenobe.php', ?)", array (time ()));
//$variables['update_xenobe_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_xenobe_results']['result'] = "DISABLED!";
$variables['update_xenobe_results']['sched'] = $sched_turns;
//$local_table_timer->stop ();
//$variables['update_xenobe_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_igb, 'sched_igb.php', ?)", array (time ()));
$variables['update_igb_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_igb_results']['sched'] = $sched_igb;
$local_table_timer->stop ();
$variables['update_igb_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_news, 'sched_news.php', ?)", array (time ()));
$variables['update_news_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_news_results']['sched'] = $sched_news;
$local_table_timer->stop ();
$variables['update_news_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_planets, 'sched_planets.php', ?)", array (time ()));
$variables['update_planets_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_planets_results']['sched'] = $sched_planets;
$local_table_timer->stop ();
$variables['update_planets_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_ports, 'sched_ports.php', ?)", array (time ()));
$variables['update_ports_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_ports_results']['sched'] = $sched_ports;
$local_table_timer->stop ();
$variables['update_ports_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_turns, 'sched_tow.php', ?)", array (time ()));
$variables['update_tow_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_tow_results']['sched'] = $sched_turns; // Towing occurs at the same time as turns
$local_table_timer->stop ();
$variables['update_tow_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_ranking, 'sched_ranking.php', ?)", array (time ()));
$variables['update_ranking_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_ranking_results']['sched'] = $sched_ranking;
$local_table_timer->stop ();
$variables['update_ranking_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_degrade, 'sched_degrade.php', ?)", array (time ()));
$variables['update_degrade_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_degrade_results']['sched'] = $sched_degrade;
$local_table_timer->stop ();
$variables['update_degrade_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_apocalypse, 'sched_apocalypse.php', ?)", array (time ()));
$variables['update_apoc_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_apoc_results']['sched'] = $sched_apocalypse;
$local_table_timer->stop ();
$variables['update_apoc_results']['elapsed'] = $local_table_timer->elapsed ();

$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', $sched_thegovernor, 'sched_thegovernor.php', ?)", array (time ()));
$variables['update_gov_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$variables['update_gov_results']['sched'] = $sched_thegovernor;
$local_table_timer->stop ();
$variables['update_gov_results']['elapsed'] = $local_table_timer->elapsed ();

// This adds a news item into the newly created news table
$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, date, news_type) " .
              "VALUES ('Big Bang!','Scientists have just discovered the Universe exists!',NOW(), 'col25')");
$variables['first_news_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$local_table_timer->stop ();
$variables['first_news_results']['elapsed'] = $local_table_timer->elapsed ();

if ($bnt_ls === true)
{
// $db->Execute ("INSERT INTO {$db->prefix}scheduler (run_once, ticks_full, sched_file, last_run) VALUES ('N', 60, 'bnt_ls_client.php', ?)", array (time ()));
// FIX table_row ($db, "The public list updater will occur every 60 minutes", $langvars['l_cu_failed'], $langvars['l_cu_inserted']);
    $creating = 1;
// include_once './bnt_ls_client.php';
}
// FIX table_footer ($langvars['l_cu_completed']);
// FIX table_header ($langvars['l_cu_account_info'] ." " . $admin_name, "h1");

$local_table_timer->start (); // Start benchmarking
$update = $db->Execute ("INSERT INTO {$db->prefix}ibank_accounts (ship_id,balance,loan) VALUES (1,0,0)");
$variables['ibank_results']['result'] = DbOp::dbResult ($db, $update, __LINE__, __FILE__);
$local_table_timer->stop ();
$variables['ibank_results']['elapsed'] = $local_table_timer->elapsed ();
$stamp = date ("Y-m-d H:i:s");

// Hash the password.  $hashed_pass will be a 60-character string.
$hasher = new PasswordHash (10, false); // The first number is the hash strength, or number of iterations of bcrypt to run.
$hashed_pass = $hasher->HashPassword (ADMIN_PW);
$variables['admin_pass'] = ADMIN_PW;

$adm_ship = $db->qstr ($admin_ship_name);
$adm_name = $db->qstr ($admin_name);
$adm_ship_sql = "INSERT INTO {$db->prefix}ships " .
                "(ship_name, ship_destroyed, character_name, password, " .
                "recovery_time, " .
                "email, turns, armor_pts, credits, sector, ship_energy, " .
                "ship_fighters, last_login, " .
                "ip_address, lang) VALUES " .
                "($adm_ship, 'N', $adm_name, '$hashed_pass', NULL, " .
                "'$admin_mail', $start_turns, $start_armor, $start_credits, 1, $start_energy, " .
                "$start_fighters, '$stamp', " .
                "'1.1.1.1', '$default_lang')";
$local_table_timer->start (); // Start benchmarking
$resxx = $db->Execute ($adm_ship_sql);
$variables['admin_account_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$local_table_timer->stop ();
$variables['admin_account_results']['elapsed'] = $local_table_timer->elapsed ();
$variables['admin_mail'] = $admin_mail;
$variables['admin_name'] = $admin_name;

$local_table_timer->start (); // Start benchmarking
$adm_terri = $db->qstr ($admin_zone_name);
$resxx = $db->Execute ("INSERT INTO {$db->prefix}zones (zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ($adm_terri, 1, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
$variables['admin_zone_results']['result'] = DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
$local_table_timer->stop ();
$variables['admin_zone_results']['elapsed'] = $local_table_timer->elapsed ();

$template->AddVariables ('langvars', $langvars);

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('variables', $variables);
$template->display ("templates/classic/create_universe/80.tpl");
?>
