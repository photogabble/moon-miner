<?
include("extension.inc");
	include("config.$phpext");
	updatecookie();

	$title="View Galactic Distances";
	include("header.$phpext");

	connectdb();


	$result = mysql_query ("SELECT sector_id, angle1, angle2,distance FROM universe ORDER BY sector_id ASC");
        bigtitle();
	while ($row=mysql_fetch_array($result))
	{
		echo "$row[sector_id], $row[angle1], $row[angle2], $row[distance]<BR>";
	}
	echo "Click <a href=main.$phpext>here</a> to return to main menu.";
	include("footer.$phpext");

?> 

