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
// File: sched_news.php

// Todo: Recode file so that news are generated in the server default language, and remove hard-coded (language) news text from the database

if (strpos ($_SERVER['PHP_SELF'], 'sched_news.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

global $default_lang;

// New database driven language entries
load_languages ($db, $lang, array ('admin', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars);

echo "<strong>Posting News</strong><br>\n";

$sql = $db->Execute ("SELECT IF(COUNT(*)>0, SUM(colonists), 0) AS total_colonists, COUNT(owner) AS total_planets,  owner, character_name FROM {$db->prefix}planets, {$db->prefix}ships WHERE owner != '0' AND owner=ship_id GROUP BY owner ORDER BY owner ASC;");
db_op_result ($db, $sql, __LINE__, __FILE__);

while (!$sql->EOF)
{
    $row = $sql->fields;

    // Get the owner name.
    $name = $row['character_name'];

    echo "&nbsp;&bull;&nbsp;Processing Planet(s) owned by {$name}({$row['owner']}) - Planets: ". number_format($row['total_planets']) .", Colonists: ". number_format($row['total_colonists']) ."<br>\n";

    // Generation of planet amount
    if ($row['total_planets'] >= 1000)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'planet1000';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $planetcount = 1000;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $planetcount ." ". $l_news_planets;
            $l_news_p_text1002 = str_replace ("[name]", $name, $l_news_p_text1000);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'planet1000');", array ($headline, $l_news_p_text1002, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_planets'] >= 500)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'planet500';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $planetcount = 500;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $planetcount ." ". $l_news_planets;
            $l_news_p_text502 = str_replace ("[name]", $name, $l_news_p_text500);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'planet500');", array ($headline, $l_news_p_text502, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_planets'] >= 250)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'planet250';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $planetcount = 250;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $planetcount ." ". $l_news_planets;
            $l_news_p_text2502 = str_replace ("[name]", $name, $l_news_p_text250);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'planet250');", array ($headline, $l_news_p_text2502, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_planets'] >= 100)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'planet100';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $planetcount = 100;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $planetcount ." ". $l_news_planets;
            $l_news_p_text102 = str_replace ("[name]", $name, $l_news_p_text100);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'planet100');", array ($headline, $l_news_p_text102, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_planets'] >= 50)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'planet50';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $planetcount = 50;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $planetcount ." ". $l_news_planets;
            $l_news_p_text502 = str_replace ("[name]", $name, $l_news_p_text50);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'planet50');", array ($headline, $l_news_p_text502, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_planets'] >= 25)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'planet25';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $planetcount = 25;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $planetcount ." ". $l_news_planets;
            $l_news_p_text252 = str_replace ("[name]", $name, $l_news_p_text25);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'planet25');", array ($headline, $l_news_p_text252, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_planets'] >= 10)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'planet10'", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $planetcount = 10;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $planetcount ." ". $l_news_planets;
            $l_news_p_text102 = str_replace ("[name]", $name, $l_news_p_text10);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'planet10');", array ($headline, $l_news_p_text102, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_planets'] >= 5)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'planet5';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $planetcount = 5;
            $l_news_p_headline2 = str_replace ("[player]", $name,$l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $planetcount ." ". $l_news_planets;
            $l_news_p_text52 = str_replace ("[name]", $name,$l_news_p_text5);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'planet5');", array ($headline, $l_news_p_text52, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    // end generation of planet amount

    // generation of colonist amount
    if ($row['total_colonists'] >= 1000000000)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'col1000';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $colcount = 1000;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $colcount ." ". $l_news_cols;
            $l_news_c_text10002 = str_replace ("[name]", $name, $l_news_c_text1000);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'col1000');", array ($headline, $l_news_c_text10002, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_colonists'] >= 500000000)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'col500';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $colcount = 500;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $colcount ." ". $l_news_cols;
            $l_news_c_text5002 = str_replace ("[name]", $name, $l_news_c_text500);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'col500');", array ($headline, $l_news_c_text5002, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_colonists'] >= 100000000)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'col100';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $colcount = 100;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $colcount ." ". $l_news_cols;
            $l_news_c_text1002 = str_replace ("[name]", $name, $l_news_c_text100);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'col100');", array ($headline, $l_news_c_text1002, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    elseif ($row['total_colonists'] >= 25000000)
    {
        $sql2 = $db->Execute ("SELECT * FROM {$db->prefix}news WHERE user_id = ? AND news_type = 'col25';", array ($row['owner']));
        db_op_result ($db, $sql2, __LINE__, __FILE__);

        if ($sql2->EOF)
        {
            $colcount = 25;
            $l_news_p_headline2 = str_replace ("[player]", $name, $l_news_p_headline);
            $headline = $l_news_p_headline2 ." ". $colcount ." ". $l_news_cols;
            $l_news_c_text252 = str_replace ("[name]", $name, $l_news_c_text25);
            $news = $db->Execute ("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES (?, ?, ?, NOW(), 'col25');", array ($headline, $l_news_c_text252, $row['owner']));
            db_op_result ($db, $news, __LINE__, __FILE__);
        }
    }
    // end generation of colonist amount

    $sql->MoveNext();
} // while

echo "--- <strong>End of News</strong> ---<br><br>\n";

$multiplier = 0; // No need to run this again

?>
