<?
	include("config.php3");
	updatecookie();

	$title="Top 100 Players";
	include("header.php3");

	connectdb();
        bigtitle();

	$result1 = mysql_query ("SELECT * FROM ships WHERE ship_destroyed='N'");
	$i=0;
	while ($row=mysql_fetch_array($result1))
	{
		$i=$i+1;
		$score=1+round($row[credits]/10000+$row[hull]/10+$row[engines]/10+$row[power]/10+$row[computers]/10+$row[sensors]/10+$row[beams]/10+$row[torp_launchers]/10+$row[armour]/10+$row[cloak]/10+$row[shields]/10+$row[ship_fighters]/1000+$row[ship_ore]/1000+$row[ship_organics]/1000+$row[ship_goods]/1000+$row[energy]/1000+$row[torps]/1000+$row[dev_genesis]/100+$row[dev_minedeflectors]/1000+$row[dev_warpedit]/100+$row[dev_beacon]/100);
		if ($row[dev_escapepod]=="Y") {$score=$score+100;}
		if ($row[dev_fuelscoop]=="Y") {$score=$score+100;}
		$result2 = mysql_query ("SELECT * FROM universe WHERE planet_owner=$row[ship_id]");
		while ($planet=mysql_fetch_array($result2))
		{
			$score=$score+round($planet[planet_organics]/1000+$planet[planet_ore]/1000+$planet[planet_goods]/1000+$planet[planet_energy]/1000+$planet[planet_credits]/10000+$planet[planet_colonists]/1000+$planet[planet_fighters]/1000+$planet[base_torp]/1000);
			if ($planet[base]=="Y") {$score=$score+1000;}
		}
		mysql_free_result($result2);
/*		$rank[$i]=array ($score, $row[character_name], $row[ship_id]); */
		$rank[$row[ship_id]]=$score;
		$name[$row[ship_id]]=$row[character_name];
	}
	$num_players=count($rank);
	arsort($rank, SORT_NUMERIC);
	reset($rank);
        echo "Generated at:  ";
        print (date("l dS of F Y h:i:s A")) ;
	echo "<BR>Total players: $num_players<BR>Players with destroyed ships are not counted.<BR><BR>";

	echo "<table>";
	echo "<tr><td>Rank</td><td>Score</td><td>Player</td></tr>";
	$offset=100;
	if ($offset>$num_players) {$offset=$num_players;}
	for ($i=1; $i<=$offset; $i++)
	{
		list ($key, $value) = each ($rank);
		echo "<tr><td>$i</td><td>$value</td><td>$name[$key]</td></tr>";
	}
	if ($i==$num_players) 
	{
		list ($key, $value) = each ($rank);
                echo "<tr><td>$i</td><td>$value</td><td>$name[$key]</td></tr>";
	}
	echo "</table>";
	echo "<BR>Click <a href=main.php3>here</a> to return to main menu.";
	include("footer.php3");
?>
