<?
	include("config.php3");
	updatecookie();

	$title="Create Universe";
	include("header.php3");

	connectdb();
	bigtitle();
	if ($swordfish!=$adminpass) 
	{
		echo "<form action=create_universe.php3 method=post>";
		echo "Password: <input type=password name=swordfish size=20 maxlength=20><BR><BR>";
		echo "<input type=submit value=Submit><input type=reset value=Reset>";
		echo "</form>";
	} elseif ($swordfish==$adminpass && $engage=="") {
		echo "Max sectors set to $sector_max in config.php3<BR><BR>";
		echo "<form action=create_universe.php3 method=post>";
		echo "<table>";
		echo "<tr><td><b><u>Base Ratios</u></b></td><td></td></tr>";
		echo "<tr><td>Percent Special</td><td><input type=text name=special size=2 maxlength=2 value=1></td></tr>";
		echo "<tr><td>Percent Ore</td><td><input type=text name=ore size=2 maxlength=2 value=20></td></tr>";
		echo "<tr><td>Percent Organics</td><td><input type=text name=organics size=2 maxlength=2 value=20></td></tr>";
		echo "<tr><td>Percent Goods</td><td><input type=text name=goods size=2 maxlength=2 value=20></td></tr>";
		echo "<tr><td>Percent Energy</td><td><input type=text name=energy size=2 maxlength=2 value=20></td></tr>";
		echo "<tr><td>Percent Empty</td><td>Equal to 100 - total of above.</td></tr>";
		echo "<tr><td><b><u>Sector/Link Set-up</u></b></td><td></td></tr>";
		$loops=intval($sector_max/300);
		echo "<tr><td>Number of loops</td><td><input type=text name=loops size=2 maxlength=2 value=$loops></td></tr>";
		echo "<tr><td>Percent of sectors with unowned planets</td><td><input type=text name=planets size=2 maxlength=2 value=10></td></tr>";
		echo "<tr><td></td><td><input type=hidden name=engage value=1><input type=hidden name=swordfish value=$swordfish><input type=submit value=Submit><input type=reset value=Reset></td></tr>";
		echo "</table>";
		echo "</form>";
	} elseif ($swordfish==$adminpass && $engage=="1") {
		echo "So you would like your $sector_max sector universe to have:<BR><BR>";
		$spp = round($sector_max*$special/100);
		echo "$spp special ports<BR>";
		$oep = round($sector_max*$ore/100);
		echo "$oep ore ports<BR>";
		$ogp = round($sector_max*$organics/100);
		echo "$ogp organics ports<BR>";
		$gop = round($sector_max*$goods/100);
		echo "$gop goods ports<BR>";
		$enp = round($sector_max*$energy/100);
		echo "$enp energy ports<BR>";
		$empty = $sector_max-$spp-$oep-$ogp-$gop-$enp;
		echo "$empty empty sectors<BR>";
		echo "$loops loops<BR>";
		$nump = round ($sector_max*$planets/100);
		echo "$nump unowned planets<BR><BR>";
		echo "If this is correct, click confirm - otherwise go back.<BR>";
		echo "<form action=create_universe.php3 method=post>";
		echo "<input type=hidden name=spp value=$spp>";
		echo "<input type=hidden name=oep value=$oep>";
		echo "<input type=hidden name=ogp value=$ogp>";
		echo "<input type=hidden name=gop value=$gop>";
		echo "<input type=hidden name=enp value=$enp>";
		echo "<input type=hidden name=nump value=$nump>";
		echo "<input type=hidden name=loops value=$loops>";
		echo "<input type=hidden name=engage value=2><input type=hidden name=swordfish value=$swordfish>";
		echo "<input type=submit value=Confirm>";
		echo "</form>";
		echo "<BR><BR>You must manually drop all tables from the database before commencing...";
	} elseif ($swordfish==$adminpass && $engage=="2") {		
		echo "Creating sector 0 - Sol...<BR>";
		$insert = mysql_query("INSERT INTO universe (sector_id, sector_name, port_type, port_organics, port_ore, port_goods, port_energy, planet, planet_name, planet_organics, planet_ore, planet_goods, planet_energy, planet_colonists, planet_credits, planet_fighters, planet_owner, base, base_sells, base_torp, beacon, angle1, angle2, distance, mines, planet_defeated) VALUES ('0', 'Sol', 'special', '', '', '', '', 'N', '', '', '', '', '', '', '', '', '', 'N', 'N', '', 'Welcome to Sol. Hub of the Universe, not.', '0', '0', '0', '', 'N')");
		$update = mysql_query("UPDATE universe SET sector_id=0 WHERE sector_id=1");
		echo "Creating sector 1 - Alpha Centauri...<BR>";
		$insert = mysql_query("INSERT INTO universe (sector_id, sector_name, port_type, port_organics, port_ore, port_goods, port_energy, planet, planet_name, planet_organics, planet_ore, planet_goods, planet_energy, planet_colonists, planet_credits, planet_fighters, planet_owner, base, base_sells, base_torp, beacon, angle1, angle2, distance, mines, planet_defeated) VALUES ('1', 'Alpha Centauri', 'energy', '', '', '', '', 'N', '', '', '', '', '', '', '', '', '', 'N', 'N', '', 'Aplha Centauri, gateway to the galaxy', '0', '0', '1', '', 'N')");
		$remaining = $sector_max-1;
		srand((double)microtime()*1000000);
		echo "Creating remaining $remaining sectors...";
		for ($i=1; $i<=$remaining;$i++)
		{
			$distance=rand(1,$universe_size);
			$angle1=rand(0,180);
			$angle2=rand(0,90);
			$insert = mysql_query("INSERT INTO universe (sector_id,angle1,angle2,distance) VALUES ('',$angle1,$angle2,$distance)");
		}
		echo "Selecting $spp sectors for additional special ports...<BR>";
		$sectors=range(2,$sector_max);

		shuffle($sectors);
		for ($i=2;$i<$spp;$i++)
		{
			$update = mysql_query("UPDATE universe SET port_type='special' WHERE sector_id=$sectors[$i]");
			echo "$sectors[$i] - ";
		}
		echo "done<BR>";
		echo "Selecting $oep sectors for ore ports...<BR>";
		$last=$spp;
		for ($i=$last;$i<$last+$oep;$i++)
		{
			$update = mysql_query("UPDATE universe SET port_type='ore' WHERE sector_id=$sectors[$i]");
			echo "$sectors[$i] - ";
		}
		echo "done<BR>";
		echo "Selecting $ogp sectors for organics ports...<BR>";
		$last=$last+$oep;
		for ($i=$last;$i<$last+$ogp;$i++)
		{
			$update = mysql_query("UPDATE universe SET port_type='organics' WHERE sector_id=$sectors[$i]");
			echo "$sectors[$i] - ";
		}
		echo "done<BR>";
		echo "Selecting $gop sectors for goods ports...<BR>";
		$last=$last+$gop;
		for ($i=$last;$i<$last+$gop;$i++)
		{
			$update = mysql_query("UPDATE universe SET port_type='goods' WHERE sector_id=$sectors[$i]");
			echo "$sectors[$i] - ";
		}
		echo "done<BR>";
		echo "Selecting $enp sectors for energy ports...<BR>";
		$last=$last+$gop;
		for ($i=$last;$i<$last+$enp;$i++)
		{
			$update = mysql_query("UPDATE universe SET port_type='energy' WHERE sector_id=$sectors[$i]");
			echo "$sectors[$i] - ";
		}
		echo "done<BR>";
		echo "Selecting $nump sectors for unowned planets...<BR>";
		$sectors=range(0,$sector_max);
		shuffle($sectors);
		for ($i=0;$i<$nump;$i++)
		{
			$update = mysql_query("UPDATE universe SET planet='Y', planet_colonists=2, planet_owner=null WHERE sector_id=$sectors[$i]");
			echo "$sectors[$i] - ";
		}
		echo "done<BR>";
		$loopsize = round($sector_max/$loops);
		$start = 0;
		$finish = $loopsize-1;
		for ($i=1; $i<=$loops ; $i++)
		{
			echo "Creating loop $i of $loopsize sectors - from sector $start to $finish...<BR>";
			for ($j=$start; $j<$finish; $j++)
			{
				$k=$j+1;
				$update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($j,$k)");
				$update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($k,$j)");
				echo "$j<=>$k - ";
			}
			$update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($start,$finish)");
			$update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($finish,$start)");
			echo "$finish<=>$start";
			echo "done loop $i<BR>";
			$start=$finish+1;
			$finish=$finish+$loopsize;
		}
		echo "Randomly One-way Linking Sectors...<BR>";
		$sectors=range(0,$sector_max);
		shuffle($sectors);
		for ($i=0;$i<=$sector_max;$i++)
		{
			$update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($i,$sectors[$i])");
			echo "$i=>$sectors[$i] - ";
		}
		echo "done.<BR>";
		echo "Randomly Two-way Linking Sectors...<BR>";
		$sectors=range(0,$sector_max);
		shuffle($sectors);
		for ($i=0;$i<=$sector_max;$i++)
		{
			$update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($i,$sectors[$i])");
			$update = mysql_query("INSERT INTO links (link_start,link_dest) VALUES ($sectors[$i],$i)");
			echo "$i<=>$sectors[$i] - ";
		}
		echo "done.<BR>";

	} else {
		echo "Huh?";

	}

	include("footer.php3");

?> 
