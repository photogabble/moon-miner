<?
include("config.php3");
updatecookie();

include($gameroot . $default_lang);
$title=$l_die_title;
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

bigtitle();

$result = mysql_query("SELECT ship_id,character_name FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

if(!isset($sure))
{
  echo "<FONT COLOR=RED><B>$l_die_rusure</B></FONT><BR><BR>";
  echo "<A HREF=$interface>NO! NO! NO!</A> What was I thinking?<BR><BR>";
  echo "<A HREF=self-destruct.php3?sure=1>$l_yes!</A> $l_die_goodbye<BR><BR>";
}
elseif($sure == 1)
{
  echo "<FONT COLOR=RED><B>$l_die_check</B></FONT><BR><BR>";
  echo "<A HREF=$interface>$l_die_nonono</A> $l_die_what<BR><BR>";
  echo "<A HREF=self-destruct.php3?sure=2>$l_yes!</A> $l_die_goodbye<BR><BR>";
}
elseif($sure == 2)
{
  echo "$l_die_count<BR>";
  echo "$l_die_vapor<BR><BR>";
  echo "$l_die_please.<BR>";
  db_kill_player($playerinfo['ship_id']);
  adminlog(0,"$playerinfo[character_name] (at $ip) self-destructed.");
  playerlog($playerinfo[ship_id], LOG_HARAKIRI, "$ip");
}
else
{
  echo "$l_die_exploit<BR><BR>";
}

if($sure != 2)
{
  TEXT_GOTOMAIN();
}

include("footer.php3");

?>
