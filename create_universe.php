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
// File: create_universe.php
// Todo: Import languages needs to have an iterator that detects language files automatically
// This is required by Setup Info, So DO NOT REMOVE
// create_universe_port_fix,0.2.0,25-02-2004,TheMightyDude

$index_page = true;
include './global_includes.php';
include './config/admin_pw.php';

// HTML Table Functions
if (!function_exists ('print_flush'))
{
    function print_flush ($text = "")
    {
        echo $text;
    }
}

if (!function_exists ('true_or_false'))
{
    function true_or_false ($true_or_false, $stat, $true, $false)
    {
        return (($true_or_false === $stat) ? $true : $false);
    }
}

if (!function_exists ('table_header'))
{
    function table_header ($title = "", $h_size)
    {
        print_flush ( "<div align=\"center\">\n");
        print_flush ( "  <center>\n");
        print_flush ( "  <table border=\"0\" cellpadding=\"1\" width=\"700\" cellspacing=\"1\" bgcolor=\"#000000\">\n");
        print_flush ( "    <tr>\n");
        print_flush ( "      <th width=\"700\" colspan=\"2\" bgcolor=\"#9999cc\" align=\"left\"><" . $h_size . " style=\"color:#000; height: 0.8em; font-size: 0.8em;font-weight: normal;\">$title</" . $h_size . "></th>\n");
        print_flush ( "    </tr>\n");
    }
}

if (!function_exists ('table_row'))
{
    function table_row ($db, $data, $failed = "Failed", $passed = "Passed")
    {
        $err = true_or_false (0, $db->ErrorNo (), "No errors found", $db->ErrorNo () . ": " . $db->ErrorMsg ());
        print_flush ( "    <tr title='" . $err . "'>\n");
        print_flush ( "      <td width=\"600\" bgcolor=\"#ccccff\"><font size=\"1\" color=\"#000000\">$data</font></td>\n");
        if ($db->ErrorNo ()!=0)
        {
            print_flush ( "      <td width=\"100\" align=\"center\" bgcolor=\"#C0C0C0\"><font size=\"1\" color=\"red\">$failed</font></td>\n");
        }
        else
        {
            print_flush ( "      <td width=\"100\" align=\"center\" bgcolor=\"#C0C0C0\"><font size=\"1\" color=\"Blue\">$passed</font></td>\n");
        }
        echo "    </tr>\n";
    }
}

if (!function_exists ('table_row_xml'))
{
    function table_row_xml ($db, $data, $failed = "Failed", $passed = "Passed", $err)
    {
        if ($err !== true)
        {
            print_flush ( "    <tr title=\'" . $err . "'>\n");
            print_flush ( "      <td width=\"600\" bgcolor=\"#ccccff\"><font size=\"1\" color=\"#000000\">$data</font></td>\n");
            print_flush ( "      <td width=\"100\" align=\"center\" bgcolor=\"#C0C0C0\"><font size=\"1\" color=\"red\">$failed</font></td>\n");
        }
        else
        {
            $err = 'No errors found.';
            print_flush ( "    <tr title=\"$err\">\n");
            print_flush ( "      <td width=\"600\" bgcolor=\"#ccccff\"><font size=\"1\" color=\"#000000\">$data</font></td>\n");
            print_flush ( "      <td width=\"100\" align=\"center\" bgcolor=\"#C0C0C0\"><font size=\"1\" color=\"Blue\">$passed</font></td>\n");
        }
        echo "    </tr>\n";
    }
}

if (!function_exists ('table_2col'))
{
    function table_2col ($name, $value)
    {
        print_flush ("    <tr>\n");
        print_flush ( "      <td width=\"600\" bgcolor=\"#ccccff\"><font size=\"1\" color=\"#000000\">$name</font></td>\n");
        print_flush ( "      <td width=\"100\" bgcolor=\"#C0C0C0\"><font size=\"1\" color=\"#000000\">$value</font></td>\n");
        print_flush ( "    </tr>\n");
    }
}

if (!function_exists ('table_1col'))
{
    function table_1col ($data)
    {
        print_flush ( "    <tr>\n");
        print_flush ( "      <td width=\"700\" colspan=\"2\" bgcolor=\"#C0C0C0\" align=\"left\"><font color=\"#000000\" size=\"1\">$data</font></td>\n");
        print_flush ( "    </tr>\n");
    }
}

if (!function_exists ('table_spacer'))
{
    function table_spacer ()
    {
        print_flush ( "    <tr>\n");
        print_flush ( "      <td width=\"100%\" colspan=\"2\" bgcolor=\"#9999cc\" height=\"1\"></td>\n");
        print_flush ( "    </tr>\n");
    }
}

if (!function_exists ('table_footer'))
{
    function table_footer ($footer = '')
    {
        if (!empty($footer))
        {
            print_flush ( "    <tr>\n");
            print_flush ( "      <td width=\"100%\" colspan=\"2\" bgcolor=\"#9999cc\" align=\"left\"><font color=\"#000000\" size=\"1\">$footer</font></td>\n");
            print_flush ( "    </tr>\n");
        }
        print_flush ( "  </table>\n");
        print_flush ( "  </center>\n");
        print_flush ( "</div><p>\n");
    }
}

// Set timelimit and randomize timer.

set_time_limit (0);

// Get POST Variable "swordfish" and URL Sanitize it. (returns NULL if not found)
$swordfish  = filter_input (INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);

// Get POST Variable "engame" and INT Sanitize it. (returns NULL if not found)
$engage  = (int) filter_input (INPUT_POST, 'engage', FILTER_SANITIZE_NUMBER_INT);

// Get POST Variable "step" and INT Sanitize it. (returns NULL if not found)
$step = (int) filter_input (INPUT_POST, 'step', FILTER_SANITIZE_NUMBER_INT);

if ($swordfish === null)
{
    $step = "0";
}
elseif (ADMIN_PW != $swordfish)
{
    $step = "99";
}

if (is_null ($engage) && ADMIN_PW == $swordfish )
{
    $step = "1";
}

if ($engage == "1" && ADMIN_PW == $swordfish )
{
    $step = "2";
}

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('create_universe'));

if ($step == '0')
{
    $title = $langvars['l_cu_welcome'];
}
elseif ($step == '99')
{
    $title = $langvars['l_cu_welcome'] . " - " . $langvars['l_cu_badpass_title'];
}
else
{
    $title = $langvars['l_cu_step'] . " " . $step . " : " . $langvars['l_cu_title'];
}

include './header.php';

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('create_universe', 'common'));

// Main switch statement.

switch ($step)
{
    case '1':

        echo "<form action='create_universe.php' method='post'>";
        table_header ($langvars['l_cu_base_n_planets'], "h1");
        table_2col ($langvars['l_cu_percent_special'], "<input type=text name=special size=10 maxlength=10 value=1>");
        table_2col ($langvars['l_cu_percent_ore'], "<input type=text name=ore size=10 maxlength=10 value=15>");
        table_2col ($langvars['l_cu_percent_organics'], "<input type=text name=organics size=10 maxlength=10 value=10>");
        table_2col ($langvars['l_cu_percent_goods'], "<input type=text name=goods size=10 maxlength=10 value=15>");
        table_2col ($langvars['l_cu_percent_energy'], "<input type=text name=energy size=10 maxlength=10 value=10>");
        table_1col ($langvars['l_cu_percent_empty']);
        table_2col ($langvars['l_cu_init_comm_sell'], "<input type=text name=initscommod size=10 maxlength=10 value=100.00>");
        table_2col ($langvars['l_cu_init_comm_buy'], "<input type=text name=initbcommod size=10 maxlength=10 value=100.00>");
        table_footer (" ");
        table_header ($langvars['l_cu_sector_n_link'], "h2");
        $fedsecs = intval ($sector_max / 200);
        $loops = intval ($sector_max / 500);
        $langvars['l_cu_sector_total'] = str_replace ('[overrides config]','<strong>[overrides config]</strong>', $langvars['l_cu_sector_total']);
        table_2col ($langvars['l_cu_sector_total'], "<input type=text name=sektors size=10 maxlength=10 value=$sector_max>");
        table_2col ($langvars['l_cu_fed_sectors'], "<input type=text name=fedsecs size=10 maxlength=10 value=$fedsecs>");
        table_2col ($langvars['l_cu_num_loops'], "<input type=text name=loops size=10 maxlength=10 value=$loops>");
        table_2col ($langvars['l_cu_percent_unowned'], "<input type=text name=planets size=10 maxlength=10 value=10>");
        table_footer (" ");
        echo "<input type=hidden name=engage value=1>\n";
        echo "<input type=hidden name=step value=2>\n";
        echo "<input type=hidden name=swordfish value=$swordfish>\n";
        table_header ($langvars['l_cu_submit_settings'], "h3");
        table_1col ("<p align='center'><input type=submit value=" . $langvars['l_submit'] ."><input type=reset value=" . $langvars['l_reset'] . "></p>");
        table_footer (" ");
        echo "</form>";
        break;

    case '2':

        $langvars['l_cu_confirm_settings'] = str_replace ('[sector_max]', $sector_max, $langvars['l_cu_confirm_settings']);
        table_header ($langvars['l_cu_confirm_settings'], "h1");

        $sector_max = round ($sektors);
        if ($fedsecs > $sector_max)
        {
            table_1col ("<font color=red>" . $langvars['l_cu_fedsec_smaller'] . "</font>");
            table_footer (" ");
            break;
        }
        $spp = round ($sector_max * $special / 100);
        $oep = round ($sector_max * $ore / 100);
        $ogp = round ($sector_max * $organics / 100);
        $gop = round ($sector_max * $goods / 100);
        $enp = round ($sector_max * $energy / 100);
        $empty = $sector_max - $spp - $oep - $ogp - $gop - $enp;
        $nump = round ($sector_max * $planets / 100);
        echo "<form action=create_universe.php method=post>\n";
        echo "<input type=hidden name=step value=3>\n";
        echo "<input type=hidden name=spp value=$spp>\n";
        echo "<input type=hidden name=oep value=$oep>\n";
        echo "<input type=hidden name=ogp value=$ogp>\n";
        echo "<input type=hidden name=gop value=$gop>\n";
        echo "<input type=hidden name=enp value=$enp>\n";
        echo "<input type=hidden name=initscommod value=$initscommod>\n";
        echo "<input type=hidden name=initbcommod value=$initbcommod>\n";
        echo "<input type=hidden name=nump value=$nump>\n";
        echo "<input type=hidden name=fedsecs value=$fedsecs>\n";
        echo "<input type=hidden name=loops value=$loops>\n";
        echo "<input type=hidden name=engage value=2>\n";
        echo "<input type=hidden name=swordfish value=$swordfish>\n";
        table_2col ($langvars['l_cu_special_ports'], $spp);
        table_2col ($langvars['l_cu_ore_ports'], $oep);
        table_2col ($langvars['l_cu_organics_ports'], $ogp);
        table_2col ($langvars['l_cu_goods_ports'], $gop);
        table_2col ($langvars['l_cu_energy_ports'], $enp);
        table_spacer ();
        table_2col ($langvars['l_cu_init_comm_sell'], $initscommod . " %");
        table_2col ($langvars['l_cu_init_comm_buy'], $initbcommod . " %");
        table_spacer ();
        table_2col ($langvars['l_cu_empty_sectors'], $empty);
        table_2col ($langvars['l_cu_fed_sectors'], $fedsecs);
        table_2col ($langvars['l_cu_loops'], $loops);
        table_2col ($langvars['l_cu_unowned_planets'], $nump);
        table_spacer ();
        table_1col ("<font color=red>" . $langvars['l_cu_table_drop_warn'] . "</font>");
        table_spacer ();
        table_1col ("<p align='center'><input type=submit value='" . $langvars['l_confirm'] ."'></p>");
        table_footer (" ");
        echo "</form>";
        break;

    case '3':

        // Delete all tables in the database
        table_header ($langvars['l_cu_drop_tables'], "h1");
        $destroy_schema_results = BntSchema::destroy ($db, $db_prefix);
        $table_count = count ($destroy_schema_results) - 1;

        for ($i = 0; $i <= $table_count; $i++)
        {
            $langvars['l_cu_completed_in_substituted'] = str_replace ('[time]', $destroy_schema_results[$i]['time'], $langvars['l_cu_completed_in']);
            table_row ($db, $langvars['l_cu_dropping_tables'] . " " . $destroy_schema_results[$i]['name'] . " " . $langvars['l_cu_completed_in_substituted'], $langvars['l_cu_failed'], $langvars['l_cu_passed']);
        }

        echo "<form action=create_universe.php method=post>";
        echo "<input type=hidden name=step value=4>";
        echo "<input type=hidden name=spp value=$spp>";
        echo "<input type=hidden name=oep value=$oep>";
        echo "<input type=hidden name=ogp value=$ogp>";
        echo "<input type=hidden name=gop value=$gop>";
        echo "<input type=hidden name=enp value=$enp>";
        echo "<input type=hidden name=initscommod value=$initscommod>";
        echo "<input type=hidden name=initbcommod value=$initbcommod>";
        echo "<input type=hidden name=nump value=$nump>";
        echo "<input type=hidden name=fedsecs value=$fedsecs>";
        echo "<input type=hidden name=loops value=$loops>";
        echo "<input type=hidden name=engage value=2>";
        echo "<input type=hidden name=swordfish value=$swordfish>";
        table_header ($langvars['l_cu_hover_for_more'], "h2");
        table_1col ("<p align='center'><input type=submit value='" . $langvars['l_cu_continue'] ."'></p>");
        table_footer (" ");
        echo "</form>";
        break;

    case '4':

        table_header ($langvars['l_cu_create_tables'], "h1");
        $create_schema_results = BntSchema::create ($db, $db_prefix);
        $table_count = count ($create_schema_results) - 1;
        for ($i = 0; $i <= $table_count; $i++)
        {
            $langvars['l_cu_completed_in_substituted'] = str_replace ('[time]', $create_schema_results[$i]['time'], $langvars['l_cu_completed_in']);
            table_row_xml ($db, $langvars['l_cu_creating_tables'] . " " . $create_schema_results[$i]['name'] . " " . $langvars['l_cu_completed_in_substituted'], $langvars['l_cu_failed'], $langvars['l_cu_passed'], $create_schema_results[$i]['result']);
        }

        echo "<form action=create_universe.php method=post>";
        echo "<input type=hidden name=step value=5>";
        echo "<input type=hidden name=spp value=$spp>";
        echo "<input type=hidden name=oep value=$oep>";
        echo "<input type=hidden name=ogp value=$ogp>";
        echo "<input type=hidden name=gop value=$gop>";
        echo "<input type=hidden name=enp value=$enp>";
        echo "<input type=hidden name=initscommod value=$initscommod>";
        echo "<input type=hidden name=initbcommod value=$initbcommod>";
        echo "<input type=hidden name=nump value=$nump>";
        echo "<input type=hidden name=fedsecs value=$fedsecs>";
        echo "<input type=hidden name=loops value=$loops>";
        echo "<input type=hidden name=engage value=2>";
        echo "<input type=hidden name=swordfish value=$swordfish>";
        table_header ($langvars['l_cu_hover_for_more'], "h2");
        table_1col ("<p align='center'><input type=submit value='" . $langvars['l_cu_continue'] ."'></p>");
        table_footer (" ");
        echo "</form>";
        break;

    case '5':

        $i = 0;
        table_header ($langvars['l_cu_import_configs_step'], "h1");
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
                $lang_name = ucwords (substr ($language_filename->getFilename(), 0, -8));
                // Import Languages
                $table_timer->start (); // Start benchmarking
                $lang_result = BntFile::iniToDb ($db, "languages/" . $language_filename->getFilename(), "languages", $lang_name, $bntreg);
                $table_timer->stop ();
                $elapsed = $table_timer->elapsed ();
                $elapsed = substr ($elapsed, 0, 5);

                $langvars['l_cu_import_langs_substituted'] = str_replace ('[language]', $lang_name, $langvars['l_cu_import_langs']);
                $langvars['l_cu_import_langs_substituted'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_import_langs_substituted']);
                table_row_xml ($db, $langvars['l_cu_import_langs_substituted'], $langvars['l_cu_failed'], $langvars['l_cu_passed'], $lang_result);
                $i++;
            }
        }

        $gameconfig_result = BntFile::iniToDb ($db, "config/configset_classic.ini.php", "gameconfig", "game", $bntreg);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        if ($gameconfig_result === true)
        {
            $table_results = true;
            $db->inactive = false;
        }
        else
        {
            $table_results = $gameconfig_result;
        }

        $langvars['l_cu_import_configs'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_import_configs']);
        table_row_xml ($db, $langvars['l_cu_import_configs'], $langvars['l_cu_failed'], $langvars['l_cu_passed'], $table_results);

        $lang = $bntreg->get('default_lang');
        echo "<form action=create_universe.php method=post>";
        echo "<input type=hidden name=step value=6>";
        echo "<input type=hidden name=spp value=$spp>";
        echo "<input type=hidden name=oep value=$oep>";
        echo "<input type=hidden name=ogp value=$ogp>";
        echo "<input type=hidden name=gop value=$gop>";
        echo "<input type=hidden name=enp value=$enp>";
        echo "<input type=hidden name=initscommod value=$initscommod>";
        echo "<input type=hidden name=initbcommod value=$initbcommod>";
        echo "<input type=hidden name=nump value=$nump>";
        echo "<input type=hidden name=fedsecs value=$fedsecs>";
        echo "<input type=hidden name=loops value=$loops>";
        echo "<input type=hidden name=engage value=2>";
        echo "<input type=hidden name=swordfish value=$swordfish>";
        table_header ($langvars['l_cu_hover_for_more'], "h2");
        table_1col ("<p align='center'><input type=submit value='" . $langvars['l_cu_continue'] ."'></p>");
        table_footer (" ");
        echo "</form>";
        break;

    case '6':

        // Database driven language entries
        $langvars = BntTranslate::load ($db, $lang, array ('create_universe', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));
        table_header ($langvars['l_cu_setup_sectors_step'], "h1");

        $initsore = $ore_limit * $initscommod / 100.0;
        $initsorganics = $organics_limit * $initscommod / 100.0;
        $initsgoods = $goods_limit * $initscommod / 100.0;
        $initsenergy = $energy_limit * $initscommod / 100.0;
        $initbore = $ore_limit * $initbcommod / 100.0;
        $initborganics = $organics_limit * $initbcommod / 100.0;
        $initbgoods = $goods_limit * $initbcommod / 100.0;
        $initbenergy = $energy_limit * $initbcommod / 100.0;

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $insert = $db->Execute ("INSERT INTO {$db->prefix}universe (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES ('1', 'Sol', '1', 'special', '0', '0', '0', '0', 'Sol: Hub of the Universe', '0', '0', '0')");
        DbOp::dbResult ($db, $insert, __LINE__, __FILE__);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_create_sol'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_create_sol']);
        table_row ($db, $langvars['l_cu_create_sol'], $langvars['l_cu_failed'], $langvars['l_cu_created']);

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $insert = $db->Execute ("INSERT INTO {$db->prefix}universe (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES ('2', 'Alpha Centauri', '1', 'energy',  '0', '0', '0', '0', 'Alpha Centauri: Gateway to the Galaxy', '0', '0', '1')");
        DbOp::dbResult ($db, $insert, __LINE__, __FILE__);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_create_ac'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_create_ac']);
        table_row ($db, $langvars['l_cu_create_ac'], $langvars['l_cu_failed'], $langvars['l_cu_created']);

        table_spacer ();
        $remaining = $sector_max - 2;
        // Cycle through remaining sectors

        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($sector_max / $loopsize) + 1;
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish > ($sector_max)) $finish = ($sector_max);
        //  $finish = $finish - 1; // Now that SOL is in sector 1 (not 0), we have to remove one.
        $start = 3; // We added sol (1), and alpha centauri (2), so start at 3.

        for ($i = 1; $i <= $loops; $i++)
        {
            $table_timer = new Timer;
            $table_timer->start (); // Start benchmarking
            $insert = "INSERT INTO {$db->prefix}universe " .
                     "(sector_id, zone_id, angle1, angle2, distance) VALUES ";
            for ($j = $start; $j < $finish; $j++)
            {
                $sector_id = $i + $j;
                $distance = intval (mt_rand (1, $universe_size));
                $angle1 = mt_rand (0, 180);
                $angle2 = mt_rand (0, 90);
                $insert .= "($sector_id, '1', $angle1, $angle2, $distance)";
                if ($j < ($finish - 1)) $insert .= ", "; else $insert .= ";";
            }

            // Now lets post the information to the mysql database.
            if ($start < $sector_max && $finish <= $sector_max)
            {
                $db->Execute ($insert);
            }

            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_insert_loop_sector_block_swapped'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_insert_loop_sector_block']);
            $langvars['l_cu_insert_loop_sector_block_swapped'] = str_replace ('[loop]', $i, $langvars['l_cu_insert_loop_sector_block_swapped']);
            $langvars['l_cu_insert_loop_sector_block_swapped'] = str_replace ('[loops]', $loops, $langvars['l_cu_insert_loop_sector_block_swapped']);
            $langvars['l_cu_insert_loop_sector_block_swapped'] = str_replace ('[start]', $start, $langvars['l_cu_insert_loop_sector_block_swapped']);
            if ($start == $finish)
            {
                $langvars['l_cu_insert_loop_sector_block_swapped'] = str_replace ('[finish]', $finish, $langvars['l_cu_insert_loop_sector_block_swapped']);
            }
            else
            {
                $langvars['l_cu_insert_loop_sector_block_swapped'] = str_replace ('[finish]', ($finish - 1), $langvars['l_cu_insert_loop_sector_block_swapped']);
            }
            table_row ($db, $langvars['l_cu_insert_loop_sector_block_swapped'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);
            $start = $finish;
            $finish += $loopsize;
            if ($finish > ($sector_max)) $finish = ($sector_max);
        };

        table_spacer ();

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $replace = $db->Execute ("INSERT INTO {$db->prefix}zones (zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('Unchartered space', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', '0' )");
        DbOp::dbResult ($db, $replace, __LINE__, __FILE__);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_setup_unchartered'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_unchartered']);
        table_row ($db, $langvars['l_cu_setup_unchartered'], $langvars['l_cu_failed'], $langvars['l_cu_set']);

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $replace = $db->Execute ("INSERT INTO {$db->prefix}zones (zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('Federation space', 0, 'N', 'N', 'N', 'N', 'N', 'N',  'Y', 'N', '$fed_max_hull')");
        DbOp::dbResult ($db, $replace, __LINE__, __FILE__);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_setup_fedspace'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_fedspace']);
        table_row ($db, $langvars['l_cu_setup_fedspace'], $langvars['l_cu_failed'], $langvars['l_cu_set']);

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $replace = $db->Execute ("INSERT INTO {$db->prefix}zones (zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('Free-Trade space', 0, 'N', 'N', 'Y', 'N', 'N', 'N','Y', 'N', '0')");
        DbOp::dbResult ($db, $replace, __LINE__, __FILE__);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_setup_free'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_free']);
        table_row ($db, $langvars['l_cu_setup_free'], $langvars['l_cu_failed'], $langvars['l_cu_set']);

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $replace = $db->Execute ("INSERT INTO {$db->prefix}zones (zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('War Zone', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y','N', 'Y', '0')");
        DbOp::dbResult ($db, $replace, __LINE__, __FILE__);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_setup_warzone'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_warzone']);
        table_row ($db, $langvars['l_cu_setup_warzone'], $langvars['l_cu_failed'], $langvars['l_cu_set']);

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $langvars['l_cu_setup_fed_sectors'] = str_replace ('[fedsecs]', $fedsecs, $langvars['l_cu_setup_fed_sectors']);
        $update = $db->Execute ("UPDATE {$db->prefix}universe SET zone_id='2' WHERE sector_id<$fedsecs");
        DbOp::dbResult ($db, $update, __LINE__, __FILE__);
        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_setup_fed_sectors'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_fed_sectors']);
        table_row ($db, $langvars['l_cu_setup_fed_sectors'], $langvars['l_cu_failed'], $langvars['l_cu_set']);

        // Finding random sectors where port=none and getting their sector ids in one sql query
        // For Special Ports

        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($spp / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish > $spp) $finish = ($spp);

        // Well since we hard coded a special port already, we start from 1.
        $start = 1;

        table_spacer ();

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $sql_query = $db->SelectLimit ("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY " . $db->random . " DESC", $spp);
        DbOp::dbResult ($db, $sql_query, __LINE__, __FILE__);
        $update = "UPDATE {$db->prefix}universe SET zone_id='3',port_type='special' WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update = "UPDATE {$db->prefix}universe SET zone_id='3',port_type='special' WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j < ($finish - 1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext ();
            }
            // echo "Update is " . $update;
            $resx = $db->Execute ($update);
            DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_setup_special_ports_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_special_ports']);
            $langvars['l_cu_setup_special_ports_changed'] = str_replace ('[loop]', $i, $langvars['l_cu_setup_special_ports_changed']);
            $langvars['l_cu_setup_special_ports_changed'] = str_replace ('[loops]', $loops, $langvars['l_cu_setup_special_ports_changed']);
            $langvars['l_cu_setup_special_ports_changed'] = str_replace ('[start]', ($start + 1), $langvars['l_cu_setup_special_ports_changed']);
            $langvars['l_cu_setup_special_ports_changed'] = str_replace ('[finish]', $finish, $langvars['l_cu_setup_special_ports_changed']);
            table_row ($db, $langvars['l_cu_setup_special_ports_changed'], $langvars['l_cu_failed'], "Selected");

            $start = $finish;
            $finish += $loopsize;
            if ($finish>$spp) $finish = ($spp);
        }

        // Finding random sectors where port=none and getting their sector ids in one sql query
        // For Ore Ports
        $initsore = $ore_limit * $initscommod / 100.0;
        $initsorganics = $organics_limit * $initscommod / 100.0;
        $initsgoods = $goods_limit * $initscommod / 100.0;
        $initsenergy = $energy_limit * $initscommod / 100.0;
        $initbore = $ore_limit * $initbcommod / 100.0;
        $initborganics = $organics_limit * $initbcommod / 100.0;
        $initbgoods = $goods_limit * $initbcommod / 100.0;
        $initbenergy = $energy_limit * $initbcommod / 100.0;

        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($oep / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish > $oep) $finish = ($oep);
        $start = 0;

        table_spacer ();

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $sql_query=$db->SelectLimit ("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY " . $db->random . " DESC", $oep);
        DbOp::dbResult ($db, $sql_query, __LINE__, __FILE__);
        $update = "UPDATE {$db->prefix}universe SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update = "UPDATE {$db->prefix}universe SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j < ($finish - 1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext ();
            }
            $resx = $db->Execute ($update);
            DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_setup_ore_ports_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_ore_ports']);
            $langvars['l_cu_setup_ore_ports_changed'] = str_replace ('[loop]', $i, $langvars['l_cu_setup_ore_ports_changed']);
            $langvars['l_cu_setup_ore_ports_changed'] = str_replace ('[loops]', $loops, $langvars['l_cu_setup_ore_ports_changed']);
            $langvars['l_cu_setup_ore_ports_changed'] = str_replace ('[start]', ($start + 1), $langvars['l_cu_setup_ore_ports_changed']);
            $langvars['l_cu_setup_ore_ports_changed'] = str_replace ('[finish]', $finish, $langvars['l_cu_setup_ore_ports_changed']);
            table_row ($db, $langvars['l_cu_setup_ore_ports_changed'], $langvars['l_cu_failed'],"Selected");

            $start = $finish;
            $finish += $loopsize;
            if ($finish > $oep) $finish = ($oep);
        }

        // Finding random sectors where port=none and getting their sector ids in one sql query
        // For Organic Ports
        $initsore = $ore_limit * $initscommod / 100.0;
        $initsorganics = $organics_limit * $initscommod / 100.0;
        $initsgoods = $goods_limit * $initscommod / 100.0;
        $initsenergy = $energy_limit * $initscommod / 100.0;
        $initbore = $ore_limit * $initbcommod / 100.0;
        $initborganics = $organics_limit * $initbcommod / 100.0;
        $initbgoods = $goods_limit * $initbcommod / 100.0;
        $initbenergy = $energy_limit * $initbcommod / 100.0;

        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($ogp / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish > $ogp) $finish = ($ogp);
        $start = 0;

        table_spacer ();

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $sql_query=$db->SelectLimit ("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY " . $db->random . " DESC", $ogp);
        DbOp::dbResult ($db, $sql_query, __LINE__, __FILE__);
        $update = "UPDATE {$db->prefix}universe SET port_type='organics',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update = "UPDATE {$db->prefix}universe SET port_type='organics',port_ore=$initbore,port_organics=$initsorganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j < ($finish - 1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext ();
            }
            $resx = $db->Execute ($update);
            DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_setup_organics_ports_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_organics_ports']);
            $langvars['l_cu_setup_organics_ports_changed'] = str_replace ('[loop]', $i, $langvars['l_cu_setup_organics_ports_changed']);
            $langvars['l_cu_setup_organics_ports_changed'] = str_replace ('[loops]', $loops, $langvars['l_cu_setup_organics_ports_changed']);
            $langvars['l_cu_setup_organics_ports_changed'] = str_replace ('[start]', ($start + 1), $langvars['l_cu_setup_organics_ports_changed']);
            $langvars['l_cu_setup_organics_ports_changed'] = str_replace ('[finish]', $finish, $langvars['l_cu_setup_organics_ports_changed']);
            table_row ($db, $langvars['l_cu_setup_organics_ports_changed'], $langvars['l_cu_failed'], "Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish > $ogp) $finish = ($ogp);
        }

        // Finding random sectors where port=none and getting their sector ids in one sql query
        // For Goods Ports
        $initsore = $ore_limit * $initscommod / 100.0;
        $initsorganics = $organics_limit * $initscommod / 100.0;
        $initsgoods = $goods_limit * $initscommod / 100.0;
        $initsenergy = $energy_limit * $initscommod / 100.0;
        $initbore = $ore_limit * $initbcommod / 100.0;
        $initborganics = $organics_limit * $initbcommod / 100.0;
        $initbgoods = $goods_limit * $initbcommod / 100.0;
        $initbenergy = $energy_limit * $initbcommod / 100.0;

        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($gop / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish > $gop) $finish = ($gop);
        $start = 0;

        table_spacer ();

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $sql_query=$db->SelectLimit ("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY " . $db->random . " DESC", $gop);
        DbOp::dbResult ($db, $sql_query, __LINE__, __FILE__);
        $update = "UPDATE {$db->prefix}universe SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update = "UPDATE {$db->prefix}universe SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j < ($finish - 1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext ();
            }
            $resx = $db->Execute ($update);
            DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_setup_goods_ports_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_goods_ports']);
            $langvars['l_cu_setup_goods_ports_changed'] = str_replace ('[loop]', $i, $langvars['l_cu_setup_goods_ports_changed']);
            $langvars['l_cu_setup_goods_ports_changed'] = str_replace ('[loops]', $loops, $langvars['l_cu_setup_goods_ports_changed']);
            $langvars['l_cu_setup_goods_ports_changed'] = str_replace ('[start]', ($start + 1), $langvars['l_cu_setup_goods_ports_changed']);
            $langvars['l_cu_setup_goods_ports_changed'] = str_replace ('[finish]', $finish, $langvars['l_cu_setup_goods_ports_changed']);
            table_row ($db, $langvars['l_cu_setup_goods_ports_changed'], $langvars['l_cu_failed'], "Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish > $gop) $finish = ($gop);
        }

        // Finding random sectors where port=none and getting their sector ids in one sql query
        // For Energy Ports
        $initsore = $ore_limit * $initscommod / 100.0;
        $initsorganics = $organics_limit * $initscommod / 100.0;
        $initsgoods = $goods_limit * $initscommod / 100.0;
        $initsenergy = $energy_limit * $initscommod / 100.0;
        $initbore = $ore_limit * $initbcommod / 100.0;
        $initborganics = $organics_limit * $initbcommod / 100.0;
        $initbgoods = $goods_limit * $initbcommod / 100.0;
        $initbenergy = $energy_limit * $initbcommod / 100.0;

        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($enp / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish > $enp) $finish = ($enp);

        // Well since we hard coded an energy port already, we start from 1.
        $start = 1;

        table_spacer ();

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $sql_query = $db->SelectLimit ("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY " . $db->random . " DESC", $enp);
        DbOp::dbResult ($db, $sql_query, __LINE__, __FILE__);
        $update = "UPDATE {$db->prefix}universe SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update = "UPDATE {$db->prefix}universe SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j < ($finish - 1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext ();
            }
            $resx = $db->Execute ($update);
            DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_setup_energy_ports_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_energy_ports']);
            $langvars['l_cu_setup_energy_ports_changed'] = str_replace ('[loop]', $i, $langvars['l_cu_setup_energy_ports_changed']);
            $langvars['l_cu_setup_energy_ports_changed'] = str_replace ('[loops]', $loops, $langvars['l_cu_setup_energy_ports_changed']);
            $langvars['l_cu_setup_energy_ports_changed'] = str_replace ('[start]', ($start + 1), $langvars['l_cu_setup_energy_ports_changed']);
            $langvars['l_cu_setup_energy_ports_changed'] = str_replace ('[finish]', $finish, $langvars['l_cu_setup_energy_ports_changed']);
            table_row ($db, $langvars['l_cu_setup_energy_ports_changed'], $langvars['l_cu_failed'], "Selected");

            $start = $finish;
            $finish += $loopsize;
            if ($finish > $enp) $finish = ($enp);
        }

        table_spacer ();
        echo "<form action=create_universe.php method=post>";
        echo "<input type=hidden name=step value=7>";
        echo "<input type=hidden name=spp value=$spp>";
        echo "<input type=hidden name=oep value=$oep>";
        echo "<input type=hidden name=ogp value=$ogp>";
        echo "<input type=hidden name=gop value=$gop>";
        echo "<input type=hidden name=enp value=$enp>";
        echo "<input type=hidden name=initscommod value=$initscommod>";
        echo "<input type=hidden name=initbcommod value=$initbcommod>";
        echo "<input type=hidden name=nump value=$nump>";
        echo "<input type=hidden name=fedsecs value=$fedsecs>";
        echo "<input type=hidden name=loops value=$loops>";
        echo "<input type=hidden name=engage value=2>";
        echo "<input type=hidden name=swordfish value=$swordfish>";
        table_1col ("<p align='center'><input type=submit value='" . $langvars['l_cu_continue'] ."'></p>");
        table_footer (" ");
        echo "</form>";
        break;

   case '7':

        // Database driven language entries
        $langvars = BntTranslate::load ($db, $lang, array ('create_universe', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));
        $p_add = 0; $p_skip = 0; $i = 0;
        table_header ($langvars['l_cu_setup_step_seven'], "h1");

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        do
        {
            $num = mt_rand (3, ($sector_max - 1));
            $select = $db->Execute ("SELECT {$db->prefix}universe.sector_id FROM {$db->prefix}universe, {$db->prefix}zones WHERE {$db->prefix}universe.sector_id=$num AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id AND {$db->prefix}zones.allow_planet='N'") or die("DB error");
            DbOp::dbResult ($db, $select, __LINE__, __FILE__);
            if ($select->RecordCount() == 0)
            {
                $insert = $db->Execute ("INSERT INTO {$db->prefix}planets (colonists, owner, corp, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, sector_id) VALUES (2, 0, 0, $default_prod_ore, $default_prod_organics, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp, $num)");
                DbOp::dbResult ($db, $insert, __LINE__, __FILE__);
                $p_add++;
            }
        }
        while ($p_add < $nump);

        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_setup_unowned_planets_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_setup_unowned_planets']);
        $langvars['l_cu_setup_unowned_planets_changed'] = str_replace (['nump'], $nump, $langvars['l_cu_setup_unowned_planets_changed']);
        table_row ($db, $langvars['l_cu_setup_unowned_planets_changed'], $langvars['l_cu_failed'], "Selected");
        table_spacer ();

        // Adds Sector Size *2 amount of links to the links table ##
        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($sector_max / $loopsize) + 1;
        if ($loops <= 0) $loops = 1;
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
                DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
            }

            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_loop_sectors_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_loop_sectors']);
            $langvars['l_cu_loop_sectors_changed'] = str_replace ('[loop]', $i, $langvars['l_cu_loop_sectors_changed']);
            $langvars['l_cu_loop_sectors_changed'] = str_replace ('[loops]', $loops, $langvars['l_cu_loop_sectors_changed']);
            $langvars['l_cu_loop_sectors_changed'] = str_replace ('[start]', $start, $langvars['l_cu_loop_sectors_changed']);
            if ($start == $finish)
            {
                $langvars['l_cu_loop_sectors_changed'] = str_replace ('[finish]', $finish, $langvars['l_cu_loop_sectors_changed']);
            }
            else
            {
                $langvars['l_cu_loop_sectors_changed'] = str_replace ('[finish]', ($finish - 1), $langvars['l_cu_loop_sectors_changed']);
            }
            table_row ($db, $langvars['l_cu_loop_sectors_changed'], $langvars['l_cu_failed'], $langvars['l_cu_created']);

            $start = $finish;
            $finish += $loopsize;
            if ($finish > $sector_max) $finish = $sector_max;
        }
        table_spacer ();

        // Adds Sector Size amount of links to the links table ##
        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($sector_max / $loopsize)+1;
        if ($loops <= 0) $loops = 1;
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
                DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
            }

            $langvars['l_cu_loop_random_oneway_changed'] = str_replace ('[loop]', $i, $langvars['l_cu_loop_random_oneway']);
            $langvars['l_cu_loop_random_oneway_changed'] = str_replace ('[loops]', $loops, $langvars['l_cu_loop_random_oneway_changed']);
            $langvars['l_cu_loop_random_oneway_changed'] = str_replace ('[start]', $start, $langvars['l_cu_loop_random_oneway_changed']);
            if ($start == $finish)
            {
                $langvars['l_cu_loop_random_oneway_changed'] = str_replace ('[finish]', $finish, $langvars['l_cu_loop_random_oneway_changed']);
            }
            else
            {
                $langvars['l_cu_loop_random_oneway_changed'] = str_replace ('[finish]', ($finish - 1), $langvars['l_cu_loop_random_oneway_changed']);
            }

            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_loop_random_oneway_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_loop_random_oneway_changed']);
            table_row ($db, $langvars['l_cu_loop_random_oneway_changed'], $langvars['l_cu_failed'], $langvars['l_cu_created']);

            $start = $finish;
            $finish += $loopsize;
            if ($finish > $sector_max) $finish = ($sector_max);
        }

        table_spacer ();

        // Adds Sector Size*2 amount of links to the links table ##
        // DO NOT ALTER LOOPSIZE
        // This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round ($sector_max / $loopsize) + 1;
        if ($loops <= 0) $loops = 1;
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
                DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
            }

            $langvars['l_cu_loop_random_twoway_changed'] = str_replace ('[loop]', $i, $langvars['l_cu_loop_random_twoway']);
            $langvars['l_cu_loop_random_twoway_changed'] = str_replace ('[loops]', $loops, $langvars['l_cu_loop_random_twoway_changed']);
            $langvars['l_cu_loop_random_twoway_changed'] = str_replace ('[start]', $start, $langvars['l_cu_loop_random_twoway_changed']);
            if ($start == $finish)
            {
                $langvars['l_cu_loop_random_twoway_changed'] = str_replace ('[finish]', $finish, $langvars['l_cu_loop_random_twoway_changed']);
            }
            else
            {
                $langvars['l_cu_loop_random_twoway_changed'] = str_replace ('[finish]', ($finish - 1), $langvars['l_cu_loop_random_twoway_changed']);
            }

            $table_timer->stop ();
            $elapsed = $table_timer->elapsed ();
            $elapsed = substr ($elapsed, 0, 5);
            $langvars['l_cu_loop_random_twoway_changed'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_loop_random_twoway_changed']);
            table_row ($db, $langvars['l_cu_loop_random_twoway_changed'], $langvars['l_cu_failed'], $langvars['l_cu_created']);
            $start = $finish;
            $finish += $loopsize;
            if ($finish > $sector_max) $finish = ($sector_max);
        }

        $table_timer = new Timer;
        $table_timer->start (); // Start benchmarking
        $resx = $db->Execute ("DELETE FROM {$db->prefix}links WHERE link_start = '{$sector_max}' OR link_dest ='{$sector_max}' ");
        DbOp::dbResult ($db, $resx, __LINE__, __FILE__);

        $table_timer->stop ();
        $elapsed = $table_timer->elapsed ();
        $elapsed = substr ($elapsed, 0, 5);
        $langvars['l_cu_remove_links'] = str_replace ('[elapsed]', $elapsed, $langvars['l_cu_remove_links']);
        table_row ($db, $langvars['l_cu_remove_links'], $langvars['l_cu_failed'], "Deleted");

        table_footer ($langvars['l_cu_completed']);
        echo "<form action=create_universe.php method=post>";
        echo "<input type=hidden name=step value=8>";
        echo "<input type=hidden name=spp value=$spp>";
        echo "<input type=hidden name=oep value=$oep>";
        echo "<input type=hidden name=ogp value=$ogp>";
        echo "<input type=hidden name=gop value=$gop>";
        echo "<input type=hidden name=enp value=$enp>";
        echo "<input type=hidden name=initscommod value=$initscommod>";
        echo "<input type=hidden name=initbcommod value=$initbcommod>";
        echo "<input type=hidden name=nump value=$nump>";
        echo "<input type=hidden name=fedsecs value=$fedsecs>";
        echo "<input type=hidden name=loops value=$loops>";
        echo "<input type=hidden name=engage value=2>";
        echo "<input type=hidden name=swordfish value=$swordfish>";
        table_header ($langvars['l_cu_submit_settings'], "h3");
        table_1col ("<p align='center'><input type=submit value=" . $langvars['l_cu_continue'] ."></p>");
        table_footer (" ");
        echo "</form>";
        break;

   case '8':

        // Database driven language entries
        $langvars = BntTranslate::load ($db, $lang, array ('create_universe', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));
        table_header ($langvars['l_cu_config_scheduler_title'], "h1");
        $langvars['l_cu_update_ticks'] = str_replace ('[sched]', $sched_ticks, $langvars['l_cu_update_ticks']);
        table_2col ($langvars['l_cu_update_ticks'], "<p align='center'><font size=\"1\" color=\"Blue\">" . $langvars['l_cu_already_set'] . "</font></p>");

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_turns, 'sched_turns.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_turns_occur'] = str_replace ('[sched]', $sched_turns, $langvars['l_cu_turns_occur']);
        table_row ($db, $langvars['l_cu_turns_occur'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_turns, 'sched_xenobe.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_xenobes_minutes'] = str_replace ('[sched]', $sched_turns, $langvars['l_cu_xenobes_minutes']);
        table_row ($db, $langvars['l_cu_xenobes_minutes'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_igb, 'sched_igb.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_igb_interest'] = str_replace ('[sched]', $sched_igb, $langvars['l_cu_igb_interest']);
        table_row ($db, $langvars['l_cu_igb_interest'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_news, 'sched_news.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_news_gen'] = str_replace ('[sched]', $sched_news, $langvars['l_cu_news_gen']);
        table_row ($db, $langvars['l_cu_news_gen'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_planets, 'sched_planets.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_planets_minutes'] = str_replace ('[sched]', $sched_planets, $langvars['l_cu_planets_minutes']);
        table_row ($db, $langvars['l_cu_planets_minutes'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_ports, 'sched_ports.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_port_regen'] = str_replace ('[sched]', $sched_ports, $langvars['l_cu_port_regen']);
        table_row ($db, $langvars['l_cu_port_regen'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_turns, 'sched_tow.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_tow_sched'] = str_replace ('[sched]', $sched_turns, $langvars['l_cu_tow_sched']);
        table_row ($db, $langvars['l_cu_tow_sched'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_ranking, 'sched_ranking.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_ranking_sched'] = str_replace ('[sched]', $sched_ranking, $langvars['l_cu_ranking_sched']);
        table_row ($db, $langvars['l_cu_ranking_sched'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_degrade, 'sched_degrade.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_sector_degrade'] = str_replace ('[sched]', $sched_degrade, $langvars['l_cu_sector_degrade']);
        table_row ($db, $langvars['l_cu_sector_degrade'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_apocalypse, 'sched_apocalypse.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_apoc_sched'] = str_replace ('[sched]', $sched_apocalypse, $langvars['l_cu_apoc_sched']);
        table_row ($db, $langvars['l_cu_apoc_sched'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $resxx = $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', $sched_thegovernor, 'sched_thegovernor.php', ?)", array (time ()));
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        $langvars['l_cu_governor_sched'] = str_replace ('[sched]', $sched_thegovernor, $langvars['l_cu_governor_sched']);
        table_row ($db, $langvars['l_cu_governor_sched'], $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        // This adds a news item into the newly created news table
        $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, date, news_type) " .
                      "VALUES ('Big Bang!','Scientists have just discovered the Universe exists!',NOW(), 'col25')");

        $err = true_or_false (0, $db->ErrorMsg (),"No errors found", $db->ErrorNo () . ": " . $db->ErrorMsg ());

        table_row ($db, $langvars['l_cu_insert_news'],  $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        if ($bnt_ls === true)
        {
            // $db->Execute ("INSERT INTO {$db->prefix}scheduler (repeat, ticks_full, sched_file, last_run) VALUES ('Y', 60, 'bnt_ls_client.php', ?)", array (time ()));
            // table_row ($db, "The public list updater will occur every 60 minutes", $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

            $creating = 1;
            // include_once './bnt_ls_client.php';
        }
        table_footer ($langvars['l_cu_completed']);
        table_header ($langvars['l_cu_account_info'] ." " . $admin_name, "h1");

        $update = $db->Execute ("INSERT INTO {$db->prefix}ibank_accounts (ship_id,balance,loan) VALUES (1,0,0)");
        DbOp::dbResult ($db, $update, __LINE__, __FILE__);
        table_row ($db, $langvars['l_cu_ibank_info'] . " " . $admin_name,  $langvars['l_cu_failed'], $langvars['l_cu_inserted']);
        $stamp = date("Y-m-d H:i:s");

        // Hash the password.  $hashedPassword will be a 60-character string.
        $hasher = new PasswordHash (10, false); // The first number is the hash strength, or number of iterations of bcrypt to run.
        $hashed_pass = $hasher->HashPassword (ADMIN_PW);

        $adm_ship = $db->qstr ($admin_ship_name);
        $adm_name = $db->qstr ($admin_name);
        $adm_ship_sql = "INSERT INTO {$db->prefix}ships " .
                        "(ship_name, ship_destroyed, character_name, password, " .
                        "email, turns, armor_pts, credits, sector, ship_energy, " .
                        "ship_fighters, last_login, " .
                        "ip_address, lang) VALUES " .
                        "($adm_ship, 'N', $adm_name, '$hashed_pass', " .
                        "'$admin_mail', $start_turns, $start_armor, $start_credits, 1, $start_energy, " .
                        "$start_fighters, '$stamp', " .
                        "'1.1.1.1', '$default_lang')";
        $resxx = $db->Execute ($adm_ship_sql);
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);

        table_1col ($langvars['l_cu_admin_login'] . "<br>" . $langvars['l_cu_admin_username'] . $admin_mail . "<br>" . $langvars['l_cu_admin_password'] . " " . ADMIN_PW);
        table_row ($db, $langvars['l_cu_insert_shipinfo_admin'] . " " . $admin_name,  $langvars['l_cu_failed'], $langvars['l_cu_inserted']);

        $adm_terri = $db->qstr($admin_zone_name);
        $resxx = $db->Execute ("INSERT INTO {$db->prefix}zones (zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ($adm_terri, 1, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
        DbOp::dbResult ($db, $resxx, __LINE__, __FILE__);
        table_row ($db, $langvars['l_cu_insert_zoneinfo_admin'] . " " . $admin_name,  $langvars['l_cu_failed'], $langvars['l_cu_inserted']);
        table_footer ($langvars['l_cu_completed']);

        print_flush ("<br><br><center><br><strong>" . $langvars['l_cu_congrats_success'] . "</strong><br>");
        $langvars['l_cu_return_to_login'] = str_replace ('[here]', '<a href=index.php>here</a>', $langvars['l_cu_return_to_login']);
        print_flush ("<strong>" . $langvars['l_cu_return_to_login'] . "</strong></center>");
        break;

   case '99':

        echo "<form action='create_universe.php' method='post'>";
        table_header ($langvars['l_cu_welcome'], "h1");
        table_1col ($langvars['l_cu_allow_create']);
        table_2col ($langvars['l_cu_pw_to_continue'], "<input type=password name=swordfish size=20>");
        table_footer ("<font color=darkred>" . $langvars['l_cu_bad_password'] . "</font>");
        table_header ($langvars['l_cu_submit_settings'], "h3");
        table_1col ("<p align='center'><input type=submit value=" . $langvars['l_submit'] ."><input type=reset value=" . $langvars['l_reset'] . "><input type=hidden name=step value=1></p>");
        table_footer (" ");
        echo "</form>";
        break;

    default:

        echo "<form action='create_universe.php' method='post'>";
        table_header ($langvars['l_cu_welcome'], "h1");
        table_1col ($langvars['l_cu_allow_create']);
        table_2col ($langvars['l_cu_pw_to_continue'], "<input type=password name=swordfish size=20>");
        table_footer (" ");
        table_header ($langvars['l_cu_submit_settings'], "h3");
        table_1col ("<p align='center'><input type=submit value=" . $langvars['l_submit'] ."><input type=reset value=" . $langvars['l_reset'] . "><input type=hidden name=step value=1></p>");
        table_footer (" ");
        echo "</form>";
        break;
}

include './footer.php';
?>
