<?
	include("config.php3");
	updatecookie();

	$title="Use Warp Editor";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$target_sector=round($target_sector);
	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
        bigtitle();
	if ($playerinfo[turns]<1)
	{
		echo "You need at least one turn to use a warp editor.<BR><BR>";
		echo "Click <a href=main.php3>here</a> to return to Main Menu.";
		include("footer.php3");		
		die();
	}

	$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id=$target_sector");
	$row = mysql_fetch_array($result2);
	if (!$row) { echo "Sector does not exist.  Click <a href=main.php3>here</a> to return to the main menu."; die();} 


	$result3 = mysql_query ("SELECT * FROM links WHERE link_start=$playerinfo[sector]");
	if ($result3 > 0)
	{
		while ($row = mysql_fetch_array($result3))
		{
			if ($target_sector == $row[link_dest]) {$flag=1;}
		}
		if ($flag != 1)
		{
			echo "Target sector ($target_sector) does not have a link from this sector.<BR><BR>";
		} else {
			$delete1 = mysql_query ("DELETE FROM links WHERE link_start=$playerinfo[sector] AND link_dest=$target_sector");
			$update1 = mysql_query ("UPDATE ships SET dev_warpedit=dev_warpedit - 1, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
			if (!$bothway)
			{
				echo "Link removed to $target_sector.<BR><BR>";
			} else {
				$delete2 = mysql_query ("DELETE FROM links WHERE link_start=$target_sector AND link_dest=$playerinfo[sector]");

				echo "Link removed to and from $target_sector.<BR><BR>";	
			}
		}
		
	}

	echo "Click <a href=main.php3>here</a> to return to the main menu.";

	include("footer.php3");

?> 
