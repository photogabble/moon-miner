<?

include("config.php3");
updatecookie();

$title="Change traderoute type";
include("header.php3");
bigtitle();

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("UPDATE ships SET traderoutetype='$type' WHERE email='$username'");
if($res)
{
  echo "<br>Your traderoute settings have been updated to ";
  if($type=='R')
    echo "Realspace trading.";
  else
    echo "Warp trading.";
  echo "<BR><BR>";
}
else
{
  echo "Failed to update traderoute settings ; Database error.<BR><BR>";
}

TEXT_GOTOMAIN();

include("footer.php3");

?>