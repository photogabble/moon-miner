<?

include("config.php3");
updatecookie();

$title="View Log";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("SELECT ship_id FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);

bigtitle();

if($command == "delete") 
{ 
  $player_log_file = $gameroot;
  $player_log_file = $player_log_file . "/player-log/" . $playerinfo[ship_id];
  
  if(file_exists($player_log_file))
  {
    unlink($player_log_file);
  }
  echo "Log Cleared.<BR><BR>"; 
  playerlog($playerinfo[ship_id], "Log cleared from " . $ip);
}
else
{
  include("player-log/" . $playerinfo[ship_id]);
  echo "<BR><BR>Click <A HREF=log.php3?command=delete>here</A> to clear log.<BR><BR>";
}

TEXT_GOTOMAIN();

include("footer.php3");

?> 
