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

global $sched_ticks, $footer_show_time, $footer_show_debug, $db, $lang;

// New database driven language entries
load_languages ($db, $lang, array ('footer','global_includes'), $langvars);

$online = (integer) 0;

if (!$db->inactive)
{
    $res = $db->Execute ("SELECT COUNT(*) AS loggedin FROM {$db->prefix}ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP({$db->prefix}ships.last_login)) / 60 <= 5 AND email NOT LIKE '%@xenobe'");
    \bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
    if ($res instanceof ADORecordSet)
    {
        $row = $res->fields;
        $online = $row['loggedin'];
    }
}

if (is_object ($bntreg->get("bnttimer")))
{
    $bnttimer = $bntreg->get("bnttimer");
    $bnttimer->stop();
    $elapsed = $bnttimer->elapsed();
    $elapsed = substr ($elapsed, 0, 5);
}
else
{
    $elapsed = 999;
}
echo '<div class="push"></div></div>';
echo '<div class="footer">';

// Suppress the news ticker on the IGB and index pages
if (!(preg_match("/index.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF'])))
{
    echo "<p></p>\n";
    echo "<script src='templates/classic/javascript/newsticker.js.php'></script>\n";
    echo "<div id='news_ticker' class='faderlines'></div>\n";
    include './fader.php';
}

?>
<br>
 <div style='clear:both'></div><div style="text-align:center">
<?php
// Update counter
$mySEC = (integer) 0;

if (!$db->inactive)
{
    $res = $db->Execute("SELECT last_run FROM {$db->prefix}scheduler LIMIT 1");
    \bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
    if ($res instanceof ADORecordSet)
    {
        $result = $res->fields;
        $mySEC = ($sched_ticks * 60) - (TIME () - $result['last_run']);
    }
}

//echo "<script src='templates/classic/javascript/updateticker.js.php?mySEC={$mySEC}&amp;sched_ticks={$sched_ticks}'></script>";
echo "<script src='templates/classic/javascript/updateticker.js.php'></script>";
echo "<script>";
echo "var seconds = '" . $mySEC . "';";
echo "var nextInterval = new Date().getTime();";
echo "var maxTicks = '" . ($sched_ticks * 60) . "';";
echo "var l_running_update = '" . $langvars['l_running_update'] . "';";
echo "var l_footer_until_update = '" . $langvars['l_footer_until_update'] . "';";
echo 'setTimeout("NextUpdate();", 100);';
echo "</script>";
echo '<div style="width:600px; margin:auto; text-align:center;"><span id=update_ticker>' . $langvars['l_please_wait'] . '</span></div>';
// End update counter

if ($online == 1)
{
    echo "  ";
    echo $langvars['l_footer_one_player_on'];
}
else
{
    echo "  " . $langvars['l_footer_players_on_1'] . " " . $online . " " . $langvars['l_footer_players_on_2'];
}
?>
</div><br>
<?php

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

if (preg_match("/index.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF']))
{
    $sf_logo_type++; // Make the SF logo darker for all pages except login. No need to change the sizes as 12 is the same size as 11 and 15 is the same size as 14.
}

if (!isset($_GET['lang']))
{
    $link = '';
}
else
{
    $link = "?lang=" . $_GET['lang'];
}

$public_pages = array ( 'ranking.php', 'new.php', 'faq.php', 'settings.php', 'news.php', 'index.php');
$slash_position = strrpos ($_SERVER['PHP_SELF'], '/') + 1;
$current_page = substr ($_SERVER['PHP_SELF'], $slash_position);
if (in_array ($current_page, $public_pages))
{
    // If it is a non-login required page, such as ranking, new, faq, settings, news, and index use the public SF logo, which increases project stats.
    echo "<div style='position:absolute; float:left; text-align:left'><a href='http://www.sourceforge.net/projects/blacknova'>";
    echo "<img style='border:0;' width='" . $sf_logo_width . "' height='" . $sf_logo_height ."' src='http://sflogo.sourceforge.net/sflogo.php?group_id=14248&amp;type=" . $sf_logo_type . "' alt='Blacknova Traders at SourceForge.net'>";
    echo "</a></div>";
}
else
{
    // Else suppress the logo, so it is as fast as possible.
}

echo "<div style='font-size:smaller; text-align:right'><a class='new_link' href='news.php" . $link . "'>" . $langvars['l_local_news'] . "</a></div>";
echo "<div style='font-size:smaller; text-align:right'>&copy;2000-" . date('Y') ." Ron Harwood &amp; the BNT Dev team</div>";
if ($footer_show_debug == true)
{
    echo "<div style='font-size:smaller; text-align:right'>" . number_format($elapsed,2) . " " . $langvars['l_seconds'] . " " . $langvars['l_time_gen_page'] ." / " . floor(memory_get_peak_usage() / 1024) . $langvars['l_peak_mem'] . "</div>";
}
?>
</div>
</body>
</html>
