<?
include("config.php3");
updatecookie();

include($gameroot . $default_lang);

$title=$l_ship_title;
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("SELECT sector FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
$res2 = mysql_query("SELECT ship_name, character_name, sector FROM ships WHERE ship_id=$ship_id");
$othership = mysql_fetch_array($res2);

bigtitle();

if($othership[sector] != $playerinfo[sector])
{
  echo "$l_ship_the <font color=white>", $othership[ship_name],"</font> $l_ship_nolonger ", $playerinfo[sector], "<BR>";
}
else
{
	echo "$l_ship_youc <font color=white>", $othership[ship_name], "</font>, $l_ship_owned <font color=white>", $othership[character_name],"</font>.<br><br>";
	echo "$l_ship_perform<BR><BR>";
	echo "<a href=scan.php3?ship_id=$ship_id>$l_planet_scn_link</a><br>";
	echo "<a href=attack.php3?ship_id=$ship_id>$l_planet_att_link</a><br>";
	echo "<a href=mailto.php3?to=$ship_id>$l_send_msg</a><br>";
}

echo "<BR>";
TEXT_GOTOMAIN();

include("footer.php3");

?>
