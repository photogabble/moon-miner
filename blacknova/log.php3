<?
	include("config.php3");
	updatecookie();

	$title="View Log";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);

	$result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$playerinfo[sector]");
	$sectorinfo=mysql_fetch_array($result2);
        bigtitle();
	if ($command=="delete") 
	{ 
		unlink("player-log/".$playerinfo[ship_id]);
		echo "Log Cleared.<BR><BR>"; 
		playerlog($playerinfo[ship_id],"Log cleared at ".(date("l dS of F Y h:i:s A"))." from ".$ip);
	} else {
		include("player-log/".$playerinfo[ship_id]);
		echo "<BR><BR>Click <a href=log.php3?command=delete>here</a> to clear log.<BR><BR>";
	}
	echo "Click <a href=main.php3>here</a> to return to main menu.";
	include("footer.php3");

?> 
