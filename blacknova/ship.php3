<?


include("config.php3");
updatecookie();

$title="Ship Commands";
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
  echo "The <font color=white>", $othership[ship_name],"</font> is no longer in sector ", $playerinfo[sector], "<BR>";
}
else
{
	echo "You see the <font color=white>", $othership[ship_name], "</font>, owned by <font color=white>", $othership[character_name],"</font>.<br><br>";
	echo "You can perform the following actions:<BR><BR>";
	echo "<a href=scan.php3?ship_id=$ship_id>Scan</a><br>";
	echo "<a href=attack.php3?ship_id=$ship_id>Attack</a><br>";
	echo "<a href=mailto.php3?to=$ship_id>Send Message</a><br>";
}

echo "<BR>";
TEXT_GOTOMAIN();

include("footer.php3");

?> 
