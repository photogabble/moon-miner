<?
	include("config.php3");
	updatecookie();

	$title="Use Emergency Warp Device";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
	srand((double)microtime()*1000000);
        bigtitle();
	if ($playerinfo[dev_emerwarp]>0)
	{
		$dest_sector=rand(0,$sector_max);
		$result_warp = mysql_query ("UPDATE ships SET sector=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$playerinfo[ship_id]");
		echo "Emergency warp device engaged - arrived in sector $dest_sector.<BR><BR>";
	} else {
		echo "You do not have an emergency warp device.<BR><BR>";
	}

	echo "Click <a href=$interface>here</a> to return to the main menu.";

	include("footer.php3");

?> 
