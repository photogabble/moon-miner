<?
	include("config.php3");
	updatecookie();

	$title="Deploy Space Beacon";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);

	$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
	$sectorinfo=mysql_fetch_array($result2);
        bigtitle();
	if ($playerinfo[dev_beacon]>0 && $beacon_text=="")
	{
		if ($sectorinfo[beacon]!="") {echo "Present beacon reads: \"$sectorinfo[beacon]\"<BR><BR>";} else {echo "There presently isn't a beacon in this sector.<BR><BR>";}
		echo"<form action=beacon.php3 method=post>";
		echo"<table>";
		echo"<tr><td>Enter text for beacon:</td><td><input type=text name=beacon_text size=40 maxlength=80></td></tr>";
		echo"</table>";
		echo"<input type=submit value=Submit><input type=reset value=Reset>";
		echo"</form>";


	} elseif ($playerinfo[dev_beacon]>0 && $beacon_text!=""){
		
		$beacon_text=trim(strip_tags($beacon_text));
		echo "Beacon now reads: \"$beacon_text\".<BR><BR>";
		$update = mysql_query("UPDATE universe SET beacon='$beacon_text' WHERE sector_id=$sectorinfo[sector_id]");
		$update = mysql_query("UPDATE ships SET dev_beacon=dev_beacon-1 WHERE ship_id=$playerinfo[ship_id]");
		
	} else {
		echo "You do not have a space beacon.<BR><BR>";
	}

	echo "Click <a href=main.php3>here</a> to return to the main menu.";

	include("footer.php3");

?> 
