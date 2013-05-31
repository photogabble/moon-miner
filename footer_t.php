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
// File: footer_t.php

$online = 0;

if (!$db->inactive)
{
    $res = $db->Execute ("SELECT COUNT(*) AS loggedin FROM {$db->prefix}ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP({$db->prefix}ships.last_login)) / 60 <= 5 AND email NOT LIKE '%@xenobe'");
    DbOp::dbResult ($db, $res, __LINE__, __FILE__);
    if ($res instanceof ADORecordSet)
    {
        $row = $res->fields;
        $online = $row['loggedin'];
    }
}

if (isset ($bntreg))
{
    if (is_object ($bntreg->get("bnttimer")))
    {
        $bnttimer = $bntreg->get("bnttimer");
        $bnttimer->stop();
        $elapsed = $bnttimer->elapsed();
        $elapsed = substr ($elapsed, 0, 5);
    }
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

if (!$db->inactive)
{
    $rs = $db->SelectLimit ("SELECT last_run FROM {$db->prefix}scheduler", 1);
    DbOp::dbResult ($db, $rs, __LINE__, __FILE__);
    if ($rs instanceof ADORecordSet)
    {
        $last_run = $rs->fields['last_run'];
        $seconds_left = ($bntreg->get("sched_ticks") * 60) - (time() - $last_run);
        $display_update_ticker = true;
    }
}
// End update counter

if ($bntreg->get("footer_show_debug") == true) // Make the SF logo a little bit larger to balance the extra line from the benchmark for page generation
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
    $langvars = BntTranslate::load ($db, $lang, array ('news', 'common'));

    $startdate = date ("Y/m/d");

    $news_ticker = array ();

    if ($db->inactive)
    {
        // Needs to be put into the language table.
        array_push ($news_ticker, array ('url'=>null, 'text'=>"News Network Down", 'type'=>"error", 'delay'=>5));
    }
    else
    {
        $rs = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE date > ? AND date < ? ORDER BY news_id", array ($startdate ." 00:00:00", $startdate ." 23:59:59"));
        DbOp::dbResult ($db, $rs, __LINE__, __FILE__);
        if ($rs instanceof ADORecordSet)
        {
            if ($rs->RecordCount() == 0)
            {
                array_push ($news_ticker, array ('url'=>null, 'text'=>$langvars['l_news_none'], 'type'=>null, 'delay'=>5));
            }
            else
            {
                while (!$rs->EOF)
                {
                    $row = $rs->fields;
                    $headline = addslashes($row['headline']);
                    array_push ($news_ticker, array ('url'=>"news.php", 'text'=>$headline, 'type'=>$row['news_type'], 'delay'=>5));
                    $rs->MoveNext();
                }
                array_push ($news_ticker, array ('url'=>null, 'text'=>"End of News", 'type'=>null, 'delay'=>5));
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

if (!isset ($_GET['lang']))
{
    $sf_logo_link = '';
}
else
{
    $sf_logo_link = "?lang=" . $_GET['lang'];
}

$elapsed = number_format ($elapsed, 2);
$mem_peak_usage = floor (memory_get_peak_usage() / 1024);

$public_pages = array ( 'ranking.php', 'new.php', 'faq.php', 'settings.php', 'news.php', 'index.php');
$slash_position = strrpos ($_SERVER['PHP_SELF'], '/') + 1;
$current_page = substr($_SERVER['PHP_SELF'], $slash_position);
if (in_array ($current_page, $public_pages))
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
$variables['update_ticker'] = array ("display"=>$display_update_ticker, "seconds_left"=>$seconds_left, "sched_ticks"=>$bntreg->get("sched_ticks"));
$variables['players_online'] = $online;
$variables['sf_logo_type'] = $sf_logo_type;
$variables['sf_logo_height'] = $sf_logo_height;
$variables['sf_logo_width'] = $sf_logo_width;
$variables['sf_logo_link'] = $sf_logo_link;
$variables['elapsed'] = $elapsed;
$variables['mem_peak_usage'] = $mem_peak_usage;
$variables['footer_show_debug'] = $bntreg->get("footer_show_debug");
$variables['cur_year'] = date ('Y');
?>
