<?

/***********************************************************
This file includes the default language for now, so that news
are generated in the server's default language. The news text
will have to be removed from the database for the next version
************************************************************/

include_once($gameroot . "/languages/$default_lang");

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

// generation of planet amount
$sql = mysql_query("select count(owner) as amount, owner from planets where owner !='0' group by owner order by amount ASC");

while ($row = mysql_fetch_array($sql))
  {
   if ($row[amount] >= 50) {
   						 $sql2 = mysql_query("select * from bn_news where user_id='$row[owner]' and news_type='planet50'");

   						 if (!mysql_fetch_row($sql2)) {
   						 				$planetcount = 50;
   						 		        $name = get_player_name($row[owner]);
              		        $l_news_p_headline=str_replace("[player]",$name,$l_news_p_headline);
                          $headline = $l_news_p_headline . $planetcount . $l_news_planets;
   						 		        $l_news_p_text50=str_replace("[name]",$name,$l_news_p_text50);
   						 				$news = mysql_query("INSERT INTO bn_news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_p_text50','$row[owner]',NOW(), 'planet50')");
   						 				              }
  						 }
  elseif ($row[amount] >= 25) {
  						 $sql2 = mysql_query("select * from bn_news where user_id='$row[owner]' and news_type='planet25'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$planetcount = 25;
  						 		        $name = get_player_name($row[owner]);
              		        $l_news_p_headline=str_replace("[player]",$name,$l_news_p_headline);
                          $headline = $l_news_p_headline . $planetcount . $l_news_planets;
  						 		        $l_news_p_text25=str_replace("[name]",$name,$l_news_p_text25);
  						 				$news = mysql_query("INSERT INTO bn_news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_p_text25','$row[owner]',NOW(), 'planet25')");
  						 				              }
  						 }
 elseif ($row[amount] >= 10) {
  						 $sql2 = mysql_query("select * from bn_news where user_id='$row[owner]' and news_type='planet10'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$planetcount = 10;
  						 		        $name = get_player_name($row[owner]);
              		        $l_news_p_headline=str_replace("[player]",$name,$l_news_p_headline);
                          $headline = $l_news_p_headline . $planetcount . $l_news_planets;
  						 		        $l_news_p_text10=str_replace("[name]",$name,$l_news_p_text10);
  						 				$news = mysql_query("INSERT INTO bn_news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_p_text10','$row[owner]',NOW(), 'planet10')");
  						 				              }
  						 }
 elseif ($row[amount] >= 5) {
  						 $sql2 = mysql_query("select * from bn_news where user_id='$row[owner]' and news_type='planet5'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$planetcount = 5;
  						 		        $name = get_player_name($row[owner]);
              		        $l_news_p_headline=str_replace("[player]",$name,$l_news_p_headline);
                          $headline = $l_news_p_headline . $planetcount . $l_news_planets;
  						 		        $l_news_p_text5=str_replace("[name]",$name,$l_news_p_text5);
  						 				$news = mysql_query("INSERT INTO bn_news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_p_text5','$row[owner]',NOW(), 'planet5')");
  						 	              }

  						 }

  } // while
// end generation of planet amount


// generation of colonist amount

$sql = mysql_query("select sum(colonists) as amount, owner from planets where owner !='0' group by owner order by amount ASC");

while ($row = mysql_fetch_array($sql))
  {
   if ($row[amount] >= 1000000000) {
   						 $sql2 = mysql_query("select * from bn_news where user_id='$row[owner]' and news_type='col1000'");

   						 if (!mysql_fetch_row($sql2)) {
   						 				$colcount = 1000;
   						 		        $name = get_player_name($row[owner]);
              		        $l_news_p_headline=str_replace("[player]",$name,$l_news_p_headline);
   						 		        $headline = $l_news_p_headline . $colcount . $l_news_cols;
   						 		        $l_news_c_text1000=str_replace("[name]",$name,$l_news_c_text1000);
   						 				$news = mysql_query("INSERT INTO bn_news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_c_text1000','$row[owner]',NOW(), 'col1000')");
   						 				              }
  						 }
  elseif ($row[amount] >= 500000000) {
  						 $sql2 = mysql_query("select * from bn_news where user_id='$row[owner]' and news_type='col500'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$colcount = 500;
  						 		        $name = get_player_name($row[owner]);
              		        $l_news_p_headline=str_replace("[player]",$name,$l_news_p_headline);
   						 		        $headline = $l_news_p_headline . $colcount . $l_news_cols;
  						 		        $l_news_c_text500=str_replace("[name]",$name,$l_news_c_text500);
  						 				$news = mysql_query("INSERT INTO bn_news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_c_text500','$row[owner]',NOW(), 'col500')");
  						 				              }
  						 }
 elseif ($row[amount] >= 100000000) {
  						 $sql2 = mysql_query("select * from bn_news where user_id='$row[owner]' and news_type='col100'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$colcount = 100;
  						 		        $name = get_player_name($row[owner]);
              		        $l_news_p_headline=str_replace("[player]",$name,$l_news_p_headline);
   						 		        $headline = $l_news_p_headline . $colcount . $l_news_cols;
  						 		        $l_news_c_text100=str_replace("[name]",$name,$l_news_c_text100);
  						 				$news = mysql_query("INSERT INTO bn_news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_c_text100','$row[owner]',NOW(), 'col100')");
  						 				              }
  						 }
 elseif ($row[amount] >= 25000000) {
  						 $sql2 = mysql_query("select * from bn_news where user_id='$row[owner]' and news_type='col25'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$colcount = 25;
  						 		        $name = get_player_name($row[owner]);
              		        $l_news_p_headline=str_replace("[player]",$name,$l_news_p_headline);
   						 		        $headline = $l_news_p_headline . $colcount . $l_news_cols;
  						 		        $l_news_c_text25=str_replace("[name]",$name,$l_news_c_text25);
  						 				$news = mysql_query("INSERT INTO bn_news (headline, newstext, user_id, date, news_type) VALUES ('$headline','$l_news_c_text25','$row[owner]',NOW(), 'col25')");
  						 	              }

  						 }

  } // while
// end generation of colonist amount

$multiplier = 0; //no use to run this more than once per tick
?>