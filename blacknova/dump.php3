<?
	include("config.php3");
	updatecookie();

	$title="Dumping colonists";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);

	$result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$playerinfo[sector]");
	$sectorinfo=mysql_fetch_array($result2);
        bigtitle();	

	if ($playerinfo[turns]<1)
	{
		echo "You need at least one turn to dump colonists.<BR><BR>";
		echo "Click <a href=$interface>here</a> to return to Main Menu.";
		include("footer.php3");		
		die();
	}
	if ($playerinfo[ship_colonists]==0)
	{
		echo "You have no colonists on your ship to begin with.<BR><BR>";
	} elseif ($sectorinfo[port_type]=="special") {
		$update = mysql_query("UPDATE ships SET ship_colonists=0, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
		echo "Colonists dumped at supply depot.<BR><BR>";
	} else {
		echo "You need to be at a supply depot to do this.<BR><BR>";
	}
	echo "Click <a href=$interface>here</a> to return to main menu.";
	include("footer.php3");

?> 
