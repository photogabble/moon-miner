<?

include("config.php3");

updatecookie();

$title=("Corporation Menu");
include("header.php3");

connectdb();
if (checklogin())
{
	die();
}

//------------------------------------
mysql_query("LOCK TABLES ships WRITE, universe WRITE");
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$playerinfo[sector]");
$sectorinfo=mysql_fetch_array($result2);
bigtitle();

	if ($action == "planetcorp")
	{
		echo ("Planet is now a Corporate Planet!<BR>");
		$result = mysql_query("UPDATE universe SET planet_corp='$playerinfo[team]', planet_owner=$playerinfo[ship_id] WHERE sector_id=$sectorinfo[sector_id]");
		
	}
	if ($action == "planetpersonal")
	{
		echo ("Planet is now a Personal Planet!<BR>");
		$result = mysql_query("UPDATE universe SET planet_corp='0', planet_owner=$playerinfo[ship_id] WHERE sector_id=$sectorinfo[sector_id]");
	}
TEXT_GOTOMAIN();

include("footer.php3");

?>
