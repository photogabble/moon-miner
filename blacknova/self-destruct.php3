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
  db_kill_player($playerinfo['ship_id']);
  playerlog(0,"$playerinfo[character_name] (at $ip) self-destructed.");
  playerlog($playerinfo[ship_id], "You self-destructed from $ip");
}
else
{
  echo "Don't play with what you don't understand.<BR><BR>";
}

if($sure != 2)
{
  TEXT_GOTOMAIN();
}

include("footer.php3");

?> 
