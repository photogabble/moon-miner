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
// File: index.php

$index_page = true;
include "config.php";

if (!isset($_GET['indexlang']))
{
    $_GET['indexlang'] = null;
}

if ($_GET['indexlang'] == 'french')
{
    $lang = 'french.inc';
}
elseif ($_GET['indexlang'] == 'german')
{
    $lang = 'german.inc';
}
elseif ($_GET['indexlang'] == 'spanish')
{
    $lang = 'spanish.inc';
}
elseif ($_GET['indexlang'] == 'british')
{
    $lang = 'english.inc';
}
elseif ($_GET['indexlang'] == 'english')
{
    $lang = 'english.inc';
}
else
{
    $lang = $default_lang . ".inc";
}
if (empty($lang))
{
    $lang = $default_lang;
}

// New database driven language entries
load_languages($db, $langsh, array('main', 'index', 'login', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars, $db_logging);

// Check to see if the language database has been installed yet. If not, redirect to create_universe.
$result = $db->Execute("SELECT name, value FROM {$db->prefix}languages WHERE category=? AND language=?;", array('common', $langsh));
if (!$result)
{
    echo "Universe creation has not occurred yet. Please run <a href='create_universe.php'>create universe</a>. We will now redirect you to that page.<br>";
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=create_universe.php\">";
    die ();
}

$title = $l_welcome_bnt;
$body_class = 'index';

include "header.php";
?>

<div class="index-header"><img class="index" src="images/header1.png" alt="Blacknova Traders"></div>
<div class="index-flags">
<a href="index.php?indexlang=french"><img src="images/flags/France.png" alt="French"></a>
<a href="index.php?indexlang=german"><img src="images/flags/Germany.png" alt="German"></a>
<a href="index.php?indexlang=spanish"><img src="images/flags/Mexico.png" alt="Spanish"></a>
<a href="index.php?indexlang=british"><img src="images/flags/United_Kingdom.png" alt="British English"></a>
<a href="index.php?indexlang=english"><img src="images/flags/United_States_of_America.png" alt="American English"></a></div>
<div class="index-header-text">Blacknova Traders</div>
<br>
<h2 style="display:none">Navigation</h2>
<div class="navigation" role="navigation">
<ul class="navigation">
<li class="navigation"><a href="login.php"><span class="button blue"><span class="shine"></span><?php echo $l_login_title; ?></span></a></li>
<li class="navigation"><a href="new.php"><span class="button green"><span class="shine"></span><?php echo $l_new_player; ?></span></a></li>
<li class="navigation"><a href="mailto:<?php echo $admin_mail; ?>"><span class="button gray"><span class="shine"></span><?php echo $l_login_emailus; ?></span></a></li>
<li class="navigation"><a href="ranking.php"><span class="button purple"><span class="shine"></span><?php echo $l_rankings; ?></span></a></li>
<li class="navigation"><a href="docs/faq.html"><span class="button brown"><span class="shine"></span><?php echo $l_faq; ?></span></a></li>
<li class="navigation"><a href="settings.php"><span class="button red"><span class="shine"></span><?php echo $l_settings; ?></span></a></li>
<?php
if (!empty($link_forums))
{
    echo "<li class='navigation'><a href='$link_forums' target='_blank'><span class='button orange'><span class='shine'></span>$l_forums</span></a></li>";
}
?>
</ul></div><br style="clear:both">
<div><p></p></div>
<div class="index-welcome">
<h1 class="index-h1"><?php echo $l_welcome_bnt; ?></h1><br>
<p><?php echo $l_bnt_description; ?><br></p>
<br>
<p class="cookie-warning"><?php echo $l_cookie_warning; ?></p></div>
<br>
<?php include "footer.php"; ?>
