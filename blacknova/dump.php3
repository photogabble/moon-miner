<?
	include("config.php3");
	updatecookie();

	include($gameroot . $default_lang);
	$title=$l_dump_title;
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
		echo "$l_dump_turn<BR><BR>";
		TEXT_GOTOMAIN();
		include("footer.php3");
		die();
	}
	if ($playerinfo[ship_colonists]==0)
	{
		echo "$l_dump_nocol<BR><BR>";
	} elseif ($sectorinfo[port_type]=="special") {
		$update = mysql_query("UPDATE ships SET ship_colonists=0, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
		echo "$l_dump_dumped<BR><BR>";
	} else {
		echo "$l_dump_nono<BR><BR>";
	}
	TEXT_GOTOMAIN();
	include("footer.php3");

?>
