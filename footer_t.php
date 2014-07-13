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
// File: footer_t.php

$online = (integer) 0;

if (Bnt\Db::isActive($pdo_db))
{
    $stamp = date("Y-m-d H:i:s", time()); // Now (as seen by PHP)
    $since_stamp = date("Y-m-d H:i:s", time() - 5 * 60); // Five minutes ago
    $sql = "SELECT COUNT(*) AS loggedin FROM {$pdo_db->prefix}ships WHERE {$pdo_db->prefix}ships.last_login BETWEEN timestamp '" . $since_stamp . "' AND timestamp '" . $stamp . "' AND email NOT LIKE '%@xenobe'";
    $stmt = $pdo_db->query($sql);
    Bnt\Db::logDbErrors($pdo_db, $sql, __LINE__, __FILE__);
    $row = $stmt->fetchObject();
    $online = $row->loggedin;
}

if (isset ($bntreg))
{
    if (property_exists($bntreg, 'bnttimer'))
    {
        $bnttimer = $bntreg->bnttimer;
        $bnttimer->stop();
        $elapsed = $bnttimer->elapsed();
    }
}
else
{
    $elapsed = 999;
}

// Suppress the news ticker on the IGB and index pages
$news_ticker = (!(preg_match("/index.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF']) || preg_match("/new.php/i", $_SERVER['PHP_SELF'])));

// Update counter
$seconds_left = (integer) 0;
$display_update_ticker = false;
if (Bnt\Db::isActive($pdo_db))
{
    $sql = "SELECT last_run FROM {$pdo_db->prefix}scheduler LIMIT 1";
    $stmt = $pdo_db->query($sql);
    $row = $stmt->fetchObject();
    Bnt\Db::logDbErrors($pdo_db, $sql, __LINE__, __FILE__);

    if (is_object($row))
    {
        $last_run = $row->last_run;
        $seconds_left = ($bntreg->sched_ticks * 60) - (time() - $last_run);
        $display_update_ticker = true;
    }
}
// End update counter

if ($bntreg->footer_show_debug == true) // Make the SF logo a little bit larger to balance the extra line from the benchmark for page generation
{
    $sf_logo_type = '14';
    $sf_logo_width = "150";
    $sf_logo_height = "40";
}
else
{
    $sf_logo_type = '11';
    $sf_logo_width = "120";
    $sf_logo_height = "30";
}

if ($news_ticker == true)
{
    // Database driven language entries

    $langvars_temp = Bnt\Translate::load($pdo_db, $lang, array ('news', 'common', 'footer', 'global_includes', 'logout'));
    // Use Array merge so that we do not clobber the langvars array, and only add to it the items needed for footer
    $langvars = array_merge($langvars, $langvars_temp);

    // Use Array unique so that we don't end up with duplicate lang array entries
    // This is resulting in an array with blank values for specific keys, so array_unique isn't entirely what we want
//    $langvars = array_unique ($langvars);

    $startdate = date("Y/m/d");

    $news_ticker = array ();

    if (Bnt\Db::isActive($pdo_db))
    {
        // Needs to be put into the language table.
        array_push($news_ticker, array ('url' => null, 'text' => "News Network Down", 'type' => "error", 'delay' => 5));
    }
    else
    {
        $rs = $db->Execute("SELECT * FROM {$db->prefix}news WHERE date > ? AND date < ? ORDER BY news_id", array ($startdate ." 00:00:00", $startdate ." 23:59:59"));
        Bnt\Db::logDbErrors($pdo_db, $rs, __LINE__, __FILE__);
        if ($rs instanceof ADORecordSet)
        {
            if ($rs->RecordCount() == 0)
            {
                array_push($news_ticker, array ('url' => null, 'text' => $langvars['l_news_none'], 'type' => null, 'delay' => 5));
            }
            else
            {
                while (!$rs->EOF)
                {
                    $row = $rs->fields;
                    $headline = addslashes($row['headline']);
                    array_push($news_ticker, array ('url' => "news.php", 'text' => $headline, 'type' => $row['news_type'], 'delay' => 5));
                    $rs->MoveNext();
                }
                array_push($news_ticker, array ('url'=>null, 'text' => "End of News", 'type' => null, 'delay' => 5));
            }
        }
    }
    $news_ticker['container']    = "article";
    $template->addVariables("news", $news_ticker);
}
else
{
    $sf_logo_type++; // Make the SF logo darker for all pages except login. No need to change the sizes as 12 is the same size as 11 and 15 is the same size as 14.
}

if (!array_key_exists('lang', $_GET))
{
    $sf_logo_link = null;
}
else
{
    $sf_logo_link = "?lang=" . $_GET['lang'];
}

$mem_peak_usage = floor(memory_get_peak_usage() / 1024);

$public_pages = array ( 'ranking.php', 'new.php', 'faq.php', 'settings.php', 'news.php', 'index.php');
$slash_position = mb_strrpos($_SERVER['PHP_SELF'], '/') + 1;
$current_page = mb_substr($_SERVER['PHP_SELF'], $slash_position);
if (in_array($current_page, $public_pages))
{
    // If it is a non-login required page, such as ranking, new, faq, settings, news, and index use the public SF logo, which increases project stats.
    $variables['suppress_logo'] = false;
}
else
{
    // Else suppress the logo, so it is as fast as possible.
    $variables['suppress_logo'] = true;
}

// Set array with all used variables in page
$variables['update_ticker'] = array ("display" => $display_update_ticker, "seconds_left" => $seconds_left, "sched_ticks" => $bntreg->sched_ticks);
$variables['players_online'] = $online;
$variables['sf_logo_type'] = $sf_logo_type;
$variables['sf_logo_height'] = $sf_logo_height;
$variables['sf_logo_width'] = $sf_logo_width;
$variables['sf_logo_link'] = $sf_logo_link;
$variables['elapsed'] = $elapsed;
$variables['mem_peak_usage'] = $mem_peak_usage;
$variables['footer_show_debug'] = $bntreg->footer_show_debug;
$variables['cur_year'] = date('Y');
?>
