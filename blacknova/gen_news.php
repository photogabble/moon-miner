<?
/*
this is alpha code not implemented in black nova itself
yet as i am playing around with it and i hope
you'll send me some comments to cyz@shogunat.com
telling me your ideas about things that could
be in the BNN news ... if you want to play around
with it do this query first:
CREATE TABLE nnews (
   news_id int(11) DEFAULT '0' NOT NULL auto_increment,
   headline varchar(100) NOT NULL,
   newstext text NOT NULL,
   user_id int(11),
   date timestamp(14),
   news_type varchar(10),
   PRIMARY KEY (news_id),
   KEY news_id (news_id),
   UNIQUE news_id_2 (news_id)
);
this thing could be included by genranks.php3
or triggered with an own cronjob daily

i will move this into the schema when i'm content with
everything .... cYz
*/


include("config.php3");
connectdb();


// all text - makes it easier to move to language files soon
$l_planet		  = " planet";
$l_planets		  = " planets";
$l_col  		  = " colonist";
$l_cols		      = " colonists";
$p_headline = "\'s Empire now has ";
$p_text5		= "The rising empire of [name] now has five planets, <br>
                   the BNN will watch the actions of [name] more closely
                   in the future!";
$p_text10		= "The growing empire of [name] now controls ten planets, <br>
                   Reports of BNN say that further expansion of [name]
                   might lead to war!";
$p_text25		= "The huge empire of [name] colonized 25 planets already, <br>
                   in an interview, [name] announced that he might consider slowing
                   down his expansion in the future!";
$p_text50		= "The enormous vast empire of [name], represented by 50 planets
                   in the whole galaxy is getting a threatening strength<br>. One
                   of the BNN reporters found out that [name] is upgrading his
                   ship planning a major war. <br>In an interview [name] announced
                   that is done on defence purpose only!";
$c_text25		= "The rising empire of [name] now has 25 million colonists, <br>
                   the BNN will watch the actions of [name] more closely<br>
                   in the future, but its highly obvois that birthrates are going to explore!";
$c_text100		= "The aspiring empire of [name] now has 100 million colonists, <br>
                   After an explosion of birth rates [name] colonies seemed to be a good<br>
                   place for families to settle!";
$c_text500		= "The large empire of [name] now has 500 million colonists, <br>
                   [name] said in an interview that colonization of the galaxy has
                   just yet begun<br>";
$c_text1000		= "The humongous empire of [name] now has one billion colonists, <br>
                   BNN reporters found out that [name] is in possesion of some
                   weird cloning mechanism allowing him to breed new colonists<br>
                   in huge amounts. With this Amount of Colonists, the econmic strength
                   of this empire is enormous, BNN hopes that [name] does not spend
                   his money on warfare";


function get_player_name($userid) {

$query = mysql_query("select character_name from ships where ship_id='$userid'");
$name = mysql_fetch_array($query);



return $name[character_name];

}


// generation of planet amount
$sql = mysql_query("select count(owner) as amount, owner from planets where owner !='0' group by owner order by amount ASC");

while ($row = mysql_fetch_array($sql))
  {
   if ($row[amount] >= 50) {
   						 $sql2 = mysql_query("select * from nnews where user_id='$row[owner]' and news_type='planet50'");

   						 if (!mysql_fetch_row($sql2)) {
   						 				$planetcount = 50;
   						 		        $name = get_player_name($row[owner]);
   						 		        $headline = $name . $p_headline . $planetcount . $l_planets;
   						 		        $p_text50=str_replace("[name]",$name,$p_text50);
   						 				$news = mysql_query("INSERT INTO nnews (headline, newstext, user_id, date, news_type) VALUES ('$headline','$p_text50','$row[owner]',NOW(), 'planet50')");
   						 				              }
  						 }
  elseif ($row[amount] >= 25) {
  						 $sql2 = mysql_query("select * from nnews where user_id='$row[owner]' and news_type='planet25'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$planetcount = 25;
  						 		        $name = get_player_name($row[owner]);
  						 		        $headline = $name . $p_headline . $planetcount . $l_planets;
  						 		        $p_text25=str_replace("[name]",$name,$p_text25);
  						 				$news = mysql_query("INSERT INTO nnews (headline, newstext, user_id, date, news_type) VALUES ('$headline','$p_text25','$row[owner]',NOW(), 'planet25')");
  						 				              }
  						 }
 elseif ($row[amount] >= 10) {
  						 $sql2 = mysql_query("select * from nnews where user_id='$row[owner]' and news_type='planet10'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$planetcount = 10;
  						 		        $name = get_player_name($row[owner]);
  						 		        $headline = $name . $p_headline . $planetcount . $l_planets;
  						 		        $p_text10=str_replace("[name]",$name,$p_text10);
  						 				$news = mysql_query("INSERT INTO nnews (headline, newstext, user_id, date, news_type) VALUES ('$headline','$p_text10','$row[owner]',NOW(), 'planet10')");
  						 				              }
  						 }
 elseif ($row[amount] >= 5) {
  						 $sql2 = mysql_query("select * from nnews where user_id='$row[owner]' and news_type='planet5'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$planetcount = 5;
  						 		        $name = get_player_name($row[owner]);
  						 		        $headline = $name . $p_headline . $planetcount . $l_planets;
  						 		        $p_text5=str_replace("[name]",$name,$p_text5);
  						 				$news = mysql_query("INSERT INTO nnews (headline, newstext, user_id, date, news_type) VALUES ('$headline','$p_text5','$row[owner]',NOW(), 'planet5')");
  						 	              }

  						 }

  } // while
// end generation of planet amount


// generation of colonist amount

$sql = mysql_query("select sum(colonists) as amount, owner from planets where owner !='0' group by owner order by amount ASC");

while ($row = mysql_fetch_array($sql))
  {
   if ($row[amount] >= 1000000000) {
   						 $sql2 = mysql_query("select * from nnews where user_id='$row[owner]' and news_type='col1000'");

   						 if (!mysql_fetch_row($sql2)) {
   						 				$colcount = 1000;
   						 		        $name = get_player_name($row[owner]);
   						 		        $headline = $name . $p_headline . $colcount . $l_cols;
   						 		        $c_text1000=str_replace("[name]",$name,$c_text1000);
   						 				$news = mysql_query("INSERT INTO nnews (headline, newstext, user_id, date, news_type) VALUES ('$headline','$c_text1000','$row[owner]',NOW(), 'col1000')");
   						 				              }
  						 }
  elseif ($row[amount] >= 500000000) {
  						 $sql2 = mysql_query("select * from nnews where user_id='$row[owner]' and news_type='col500'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$colcount = 500;
  						 		        $name = get_player_name($row[owner]);
  						 		        $headline = $name . $p_headline . $colcount . $l_cols;
  						 		        $c_text500=str_replace("[name]",$name,$c_text500);
  						 				$news = mysql_query("INSERT INTO nnews (headline, newstext, user_id, date, news_type) VALUES ('$headline','$c_text500','$row[owner]',NOW(), 'col500')");
  						 				              }
  						 }
 elseif ($row[amount] >= 100000000) {
  						 $sql2 = mysql_query("select * from nnews where user_id='$row[owner]' and news_type='col100'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$colcount = 100;
  						 		        $name = get_player_name($row[owner]);
  						 		        $headline = $name . $p_headline . $colcount . $l_cols;
  						 		        $c_text100=str_replace("[name]",$name,$c_text100);
  						 				$news = mysql_query("INSERT INTO nnews (headline, newstext, user_id, date, news_type) VALUES ('$headline','$c_text100','$row[owner]',NOW(), 'col100')");
  						 				              }
  						 }
 elseif ($row[amount] >= 25000000) {
  						 $sql2 = mysql_query("select * from nnews where user_id='$row[owner]' and news_type='col25'");

  						 if (!mysql_fetch_row($sql2)) {
  						 				$colcount = 25;
  						 		        $name = get_player_name($row[owner]);
  						 		        $headline = $name . $p_headline . $colcount . $l_cols;
  						 		        $c_text25=str_replace("[name]",$name,$c_text25);
  						 				$news = mysql_query("INSERT INTO nnews (headline, newstext, user_id, date, news_type) VALUES ('$headline','$c_text25','$row[owner]',NOW(), 'col25')");
  						 	              }

  						 }

  } // while
// end generation of colonist amount


?>
