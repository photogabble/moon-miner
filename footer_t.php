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
// File: footer.php

global $sched_ticks, $footer_show_time, $footer_show_debug, $no_db;

// New database driven language entries
load_languages($db, $lang, array('regional', 'footer','global_includes'), $langvars);

// Needs to be put into the language table.
$langvars['l_running_update'] = "Running Update";
$langvars['l_please_wait'] = "Please wait.";


$online = (integer) 0;

if (!$no_db)
{
    $res = $db->Execute("SELECT COUNT(*) AS loggedin FROM {$db->prefix}ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP({$db->prefix}ships.last_login)) / 60 <= 5 AND email NOT LIKE '%@xenobe'");
    db_op_result ($db, $res, __LINE__, __FILE__);
    if ($res instanceof ADORecordSet)
    {
        $row = $res->fields;
        $online = $row['loggedin'];
    }
}

global $BenchmarkTimer;
if (is_object ($BenchmarkTimer) )
{
    $stoptime = $BenchmarkTimer->stop();
    $elapsed = $BenchmarkTimer->elapsed();
    $elapsed = substr ($elapsed, 0, 5);
}
else
{
    $elapsed = 999;
}

// Suppress the news ticker on the IGB and index pages
$news_ticker = (!(preg_match("/index.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF'])));

// Update counter
$seconds_left = (integer) 0;
$display_update_ticker = false;

if (!$no_db)
{
    $rs = $db->Execute("SELECT last_run FROM {$db->prefix}scheduler LIMIT 1");
    db_op_result ($db, $rs, __LINE__, __FILE__);
    if ($rs instanceof ADORecordSet)
    {
        $last_run = $rs->fields['last_run'];
        $seconds_left = ($sched_ticks * 60) - (time() - $last_run);
        $display_update_ticker = true;
    }
}
// End update counter

if ($footer_show_time == true) // Make the SF logo a little bit larger to balance the extra line from the benchmark for page generation
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
    // New database driven language entries
    load_languages($db, $lang, array('news'), $langvars);

    $startdate = date("Y/m/d");

    $news_ticker = array();

    if ($no_db)
    {
        // Needs to be put into the language table.
        array_push($news_ticker, array('url'=>null, 'text'=>"News Network Down", 'type'=>"error", 'delay'=>5));
    }
    else
    {
        $rs = $db->Execute("SELECT * FROM {$db->prefix}news WHERE date > '{$startdate} 00:00:00' AND date < '{$startdate} 23:59:59' ORDER BY news_id");
        db_op_result ($db, $rs, __LINE__, __FILE__);
        if ($rs instanceof ADORecordSet)
        {
            if ($rs->RecordCount() == 0)
            {
                array_push($news_ticker, array('url'=>null, 'text'=>$langvars['l_news_none'], 'type'=>null, 'delay'=>5));
            }
            else
            {
                while (!$rs->EOF)
                {
                    $row = $rs->fields;
                    $headline = addslashes($row['headline']);
                    array_push($news_ticker, array('url'=>"news.php", 'text'=>$headline, 'type'=>$row['news_type'], 'delay'=>5));
                    $rs->MoveNext();
                }
                array_push($news_ticker, array('url'=>null, 'text'=>"End of News", 'type'=>null, 'delay'=>5));
            }
        }
    }
    $news_ticker['container']    = "article";
    $template->AddVariables("news", $news_ticker);
}
else
{
    $sf_logo_type++; // Make the SF logo darker for all pages except login. No need to change the sizes as 12 is the same size as 11 and 15 is the same size as 14.
}

if (!isset($_GET['lang']))
{
    $sf_logo_link = '';
}
else
{
    $sf_logo_link = "?lang=" . $_GET['lang'];
}

$elapsed = number_format ($elapsed, 2);
$mem_peak_usage = floor (memory_get_peak_usage() / 1024);

// Set array with all used variables in page
$variables['update_ticker'] = array("display"=>$display_update_ticker, "seconds_left"=>$seconds_left, "sched_ticks"=>$sched_ticks);

$variables['players_online'] = $online;
$variables['sf_logo_type'] = $sf_logo_type;
$variables['sf_logo_height'] = $sf_logo_height;
$variables['sf_logo_width'] = $sf_logo_width;
$variables['sf_logo_link'] = $sf_logo_link;
$variables['elapsed'] = $elapsed;
$variables['mem_peak_usage'] = $mem_peak_usage;
$variables['footer_show_debug'] = $footer_show_debug;
?>
