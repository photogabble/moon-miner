<?

	include("config.php");
	updatecookie();

  include_once($gameroot . "/languages/$lang");
	$title="View Galactic Distances";
	include("header.php");

	connectdb();


	$result = $db->Execute ("SELECT sector_id, angle1, angle2,distance FROM $dbtables[universe] ORDER BY sector_id ASC");
        bigtitle();
	while (!$result->EOF)
	{
		$row = $result->fields;
    echo "$row[sector_id], $row[angle1], $row[angle2], $row[distance]<BR>";
    $result->MoveNext();
	}
	echo "Click <a href=main.php>here</a> to return to main menu.";
	include("footer.php");

?> 

