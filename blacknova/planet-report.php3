<?
	include("config.php3");
	updatecookie();

	$title="Planet Report";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);

	$result2 = mysql_query ("SELECT * from universe WHERE planet_owner=$playerinfo[ship_id]");
        bigtitle();	
	$i=0;
	if ($result2>0)
	{
		while ($row = mysql_fetch_array($result2))
		{
			$planet[$i]=$row;
			$i++;
		}
	}
	$num_planets=$i;
	if ($num_planets<1)
	{
		echo "<BR>You have no planets<BR><BR>";
	} else {
		echo "<table>";
		echo "<tr><td>Sector</td><td>Planet Name</td><td>Organics</td><td>Ore</td><td>Goods</td><td>Energy</td><td>Colonists</td><td>Credits</td><td>Fighters</td><td>Torpedoes</td><td>Base</td><td>Selling</td><td>Defeated</td></tr>";
		for ($i=0;$i<$num_planets;$i++)
		{
			if (empty($planet[$i][planet_name])) {$planet[$i][planet_name]="Unamed";}
			echo "<tr><td><a href=rsmove.php3?engage=1&destination=". $planet[$i][sector_id] .">". $planet[$i][sector_id] ."</a>" . "</td><td>". $planet[$i][planet_name] ."</td><td>". $planet[$i][planet_organics] ."</td><td>". $planet[$i][planet_ore] ."</td><td>". $planet[$i][planet_goods] ."</td><td>". $planet[$i][planet_energy] ."</td><td>". $planet[$i][planet_colonists] ."</td><td>". $planet[$i][planet_credits] ."</td><td>". $planet[$i][planet_fighters] ."</td><td>". $planet[$i][base_torp] ."</td><td>". $planet[$i][base] ."</td><td>". $planet[$i][base_sells] ."</td><td>". $planet[$i][planet_defeated] ."</td></tr>";
		}
		echo "</table>";
	}
	echo "Click <a href=main.php3>here</a> to return to main menu.";
	include("footer.php3");

?> 
