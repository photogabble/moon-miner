<?
	include("config.php3");
	updatecookie();

	$title="Use Device";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
        bigtitle();

	echo "You have the following devices you can use:<BR><BR>";
	if ($playerinfo[dev_warpedit] > 0)
	{
		echo "$playerinfo[dev_warpedit] <a href=warpedit.php3>Warp Editor(s)</a><BR>";
	}
	if ($playerinfo[dev_genesis] > 0)
	{
		echo "$playerinfo[dev_genesis] <a href=genesis.php3>Genesis Device(s)</a><BR>";
	}
	if ($playerinfo[dev_emerwarp] > 0)
	{
		echo "$playerinfo[dev_emerwarp] <a href=emerwarp.php3>Emergency Warp Device(s)</a><BR>";
	}
		if ($playerinfo[dev_beacon] > 0)
	{
		echo "$playerinfo[dev_beacon] <a href=beacon.php3>Space Beacon(s)</a><BR>";
	}

	echo "<BR>You also have these devices that are used automatically:<BR><BR>";

	if ($playerinfo[dev_escapepod] -= "Y")
	{
		echo "Escape Pods<BR>";
	}
	if ($playerinfo[dev_fuelscoop] -= "Y")
	{
		echo "Fuelscoop<BR>";
	}
		if ($playerinfo[dev_mindeflector] > 0)
	{
		echo "$playerinfo[dev_minedeflector] Mine Deflector(s)<BR>";
	}
	
	echo "<BR>Click <a href=main.php3>here</a> to return to the main menu.";

	include("footer.php3");

?> 
