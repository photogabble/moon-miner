<?

include("config.php3");
updatecookie();

$title="Self-Destruct";
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
  echo "<FONT COLOR=RED><B>Are you sure you wish to destroy your ship? This will remove you from the game.</B></FONT><BR><BR>";
  echo "<A HREF=$interface>NO! NO! NO!</A> What was I thinking?<BR><BR>";
  echo "<A HREF=self-destruct.php3?sure=1>YES!</A> Goodbye cruel galaxy!<BR><BR>";
}
elseif($sure == 1)
{
  echo "<FONT COLOR=RED><B>Are you positive that you wish to remove yourself from the game?</B></FONT><BR><BR>";
  echo "<A HREF=$interface>NO! NO! NO!</A> What was I thinking?<BR><BR>";
  echo "<A HREF=self-destruct.php3?sure=2>YES!</A> Goodbye cruel galaxy!<BR><BR>";
}
elseif($sure == 2)
{
  echo "5.. 4.. 3.. 2.. 1.. Boom!<BR>";
  echo "You ship and all aboard have been vaporized.<BR><BR>";
  echo "Please, <A HREF=logout.php3>logout</A>.<BR>";
  unlink("player-log/" . $playerinfo[ship_id]);
  mysql_query("UPDATE universe SET planet_owner='' where planet_owner=$playerinfo[ship_id]");
  mysql_query("DELETE FROM ships WHERE ship_id=$playerinfo[ship_id]");
  playerlog(0,"$playerinfo[character_name] (at $ip) self-destructed.");
}
else
{
  echo "Don't play with what you don't understand.<BR><BR>";
}

if($sure != 2)
{
  echo "Click <A HREF=$interface>here</A> to return to main menu.";
}

include("footer.php3");

?> 
