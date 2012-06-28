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

/***********************************************************
This file includes the default language for now, so that news
are generated in the server's default language. The news text
will have to be removed from the database for the next version
************************************************************/

if (preg_match("/sched_news.php/i", $_SERVER['PHP_SELF']))
{
    echo "You can not access this file directly!";
    die();
}

global $default_lang;
include "languages/$default_lang" .".inc";

echo "<strong>Posting News</strong><br><br>";

// Generation of planet amount
$sql = $db->Execute("select count(owner) as amount, owner from {$db->prefix}planets where owner !='0' group by owner order by amount ASC");

while (!$sql->EOF)
  {
   $row = $sql->fields;
   if ($row[amount] >= 50) {
                            $sql2 = $db->Execute("select * from {$db->prefix}news where user_id='$row[owner]' and news_type='planet50'");

                            if ($sql2->EOF) {
                                            $planetcount = 50;
                                            $name = get_player_name($row[owner]);
                              $l_news_p_headline2=str_replace("[player]",$name,$l_news_p_headline);
                          $headline = $l_news_p_headline2 . $planetcount . $l_news_planets;
                                            $l_news_p_text502=str_replace("[name]",$name,$l_news_p_text50);
                                            $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_p_text502','$row[owner]',NOW(), 'planet50')");
                                                          }
                           }
  elseif ($row[amount] >= 25) {
                           $sql2 = $db->Execute("select * from {$db->prefix}news where user_id='$row[owner]' and news_type='planet25'");

                           if ($sql2->EOF) {
                                           $planetcount = 25;
                                           $name = get_player_name($row[owner]);
                              $l_news_p_headline2=str_replace("[player]",$name,$l_news_p_headline);
                          $headline = $l_news_p_headline2 . $planetcount . $l_news_planets;
                                           $l_news_p_text252=str_replace("[name]",$name,$l_news_p_text25);
                                           $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_p_text252','$row[owner]',NOW(), 'planet25')");
                                                         }
                           }
 elseif ($row[amount] >= 10) {
                           $sql2 = $db->Execute("select * from {$db->prefix}news where user_id='$row[owner]' and news_type='planet10'");

                           if ($sql2->EOF) {
                                           $planetcount = 10;
                                           $name = get_player_name($row[owner]);
                              $l_news_p_headline2=str_replace("[player]",$name,$l_news_p_headline);
                          $headline = $l_news_p_headline2 . $planetcount . $l_news_planets;
                                           $l_news_p_text102=str_replace("[name]",$name,$l_news_p_text10);
                                           $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_p_text102','$row[owner]',NOW(), 'planet10')");
                                                         }
                           }
 elseif ($row[amount] >= 5) {
                           $sql2 = $db->Execute("select * from {$db->prefix}news where user_id='$row[owner]' and news_type='planet5'");

                           if ($sql2->EOF) {
                                           $planetcount = 5;
                                           $name = get_player_name($row[owner]);
                              $l_news_p_headline2=str_replace("[player]",$name,$l_news_p_headline);
                          $headline = $l_news_p_headline2 . $planetcount . $l_news_planets;
                                           $l_news_p_text52=str_replace("[name]",$name,$l_news_p_text5);
                                           $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_p_text52','$row[owner]',NOW(), 'planet5')");
                                             }

                           }

    $sql->MoveNext();
  } // while
// end generation of planet amount


// generation of colonist amount

$sql = $db->Execute("select sum(colonists) as amount, owner from {$db->prefix}planets where owner !='0' group by owner order by amount ASC");

while (!$sql->EOF)
  {
   $row = $sql->fields;
   if ($row[amount] >= 1000000000) {
                            $sql2 = $db->Execute("select * from {$db->prefix}news where user_id='$row[owner]' and news_type='col1000'");

                            if ($sql2->EOF) {
                                            $colcount = 1000;
                                            $name = get_player_name($row[owner]);
                              $l_news_p_headline2=str_replace("[player]",$name,$l_news_p_headline);
                                            $headline = $l_news_p_headline2 . $colcount . $l_news_cols;
                                            $l_news_c_text10002=str_replace("[name]",$name,$l_news_c_text1000);
                                            $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_c_text10002','$row[owner]',NOW(), 'col1000')");
                                                          }
                           }
  elseif ($row[amount] >= 500000000) {
                           $sql2 = $db->Execute("select * from {$db->prefix}news where user_id='$row[owner]' and news_type='col500'");

                           if ($sql2->EOF) {
                                           $colcount = 500;
                                           $name = get_player_name($row[owner]);
                              $l_news_p_headline2=str_replace("[player]",$name,$l_news_p_headline);
                                            $headline = $l_news_p_headline2 . $colcount . $l_news_cols;
                                           $l_news_c_text5002=str_replace("[name]",$name,$l_news_c_text500);
                                           $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_c_text5002','$row[owner]',NOW(), 'col500')");
                                                         }
                           }
 elseif ($row[amount] >= 100000000) {
                           $sql2 = $db->Execute("select * from {$db->prefix}news where user_id='$row[owner]' and news_type='col100'");

                           if ($sql2->EOF) {
                                           $colcount = 100;
                                           $name = get_player_name($row[owner]);
                              $l_news_p_headline2=str_replace("[player]",$name,$l_news_p_headline);
                                            $headline = $l_news_p_headline2 . $colcount . $l_news_cols;
                                           $l_news_c_text1002=str_replace("[name]",$name,$l_news_c_text100);
                                           $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_c_text1002','$row[owner]',NOW(), 'col100')");
                                                         }
                           }
 elseif ($row[amount] >= 25000000) {
                           $sql2 = $db->Execute("select * from {$db->prefix}news where user_id='$row[owner]' and news_type='col25'");

                           if ($sql2->EOF) {
                                           $colcount = 25;
                                           $name = get_player_name($row[owner]);
                              $l_news_p_headline2=str_replace("[player]",$name,$l_news_p_headline);
                                            $headline = $l_news_p_headline2 . $colcount . $l_news_cols;
                                           $l_news_c_text252=str_replace("[name]",$name,$l_news_c_text25);
                                           $news = $db->Execute("INSERT INTO {$db->prefix}news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_c_text252','$row[owner]',NOW(), 'col25')");
                                             }

                           }

    $sql->MoveNext();
  } // while
// end generation of colonist amount

$multiplier = 0; // No need to run this again
?>
