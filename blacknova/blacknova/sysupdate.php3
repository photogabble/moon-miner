<?
	include("config.php3");
	$title="System Update";
	include("header.php3");
	connectdb();
	bigtitle();
	if ($swordfish!=$adminpass) 
	{
		echo "<form action=sysupdate.php3 method=post>";
		echo "Password: <input type=password name=swordfish size=20 maxlength=20><BR><BR>";
		echo "<input type=submit value=Submit><input type=reset value=Reset>";
		echo "</form>";
	} else {
		srand((double)microtime()*1000000);
		/* add turns */
		$update1 = mysql_query ("UPDATE ships SET turns=turns+1 WHERE turns<$max_turns");
		$update1b = mysql_query ("UPDATE ships SET turns=0 WHERE turns<0");
		echo "<BR>Turns Added<BR><BR>";
	
		/* add commodities to ports */
		$update2 = mysql_query ("UPDATE universe SET port_organics=port_organics+$organics_rate WHERE port_type!='special' AND port_type!='none' AND port_organics<$organics_limit AND port_type='organics'");
		$update2a = mysql_query ("UPDATE universe SET port_organics=port_organics+$organics_rate WHERE port_type!='special' AND port_type!='none' AND port_organics<$organics_limit");
		$update2b= mysql_query ("UPDATE universe SET port_organics=0 WHERE port_organics<0");
		$update3 = mysql_query ("UPDATE universe SET port_ore=port_ore+$ore_rate WHERE port_type!='special' AND port_type!='none' AND port_ore<$ore_limit and port_type='ore'");
		$update3a = mysql_query ("UPDATE universe SET port_ore=port_ore+$ore_rate WHERE port_type!='special' AND port_type!='none' AND port_ore<$ore_limit");
		$update3b= mysql_query ("UPDATE universe SET port_ore=0 WHERE port_ore<0");
		$update4 = mysql_query ("UPDATE universe SET port_goods=port_goods+$goods_rate WHERE port_type!='special' AND port_type!='none' AND port_goods<$goods_limit and port_type='goods'");
		$update4a = mysql_query ("UPDATE universe SET port_goods=port_goods+$goods_rate WHERE port_type!='special' AND port_type!='none' AND port_goods<$goods_limit");
		$update4b= mysql_query ("UPDATE universe SET port_goods=0 WHERE port_goods<0");
		$update5 = mysql_query ("UPDATE universe SET port_energy=port_energy+$energy_rate WHERE port_type!='special' AND port_type!='none' AND port_energy<$energy_limit and port_type='energy'");	
		$update5a = mysql_query ("UPDATE universe SET port_energy=port_energy+$energy_rate WHERE port_type!='special' AND port_type!='none' AND port_energy<$energy_limit");	
		$update5b= mysql_query ("UPDATE universe SET port_energy=0 WHERE port_energy<0");
		echo "Commodities Added to ports<BR><BR>";

		/* planet production */
		$result1 = mysql_query ("SELECT sector_id, planet_colonists, planet_owner, planet_ore, planet_organics, planet_goods, planet_energy, planet_fighters, base_torp FROM universe WHERE planet='Y'");
		while ($row = mysql_fetch_array($result1))
		{
			if ($row[planet_colonists]>$colonist_limit) 
			{ 
				$colonist_pro_rate=$colonist_limit;
			} else {
				$colonist_pro_rate=$row[planet_colonists];
			}
			$production=$colonist_pro_rate*$colonist_production_rate;
			$organics_production=$production*$organics_prate;
			$organics_test = $organics_production+$row[planet_organics];
			if ($organics_test>$organics_limit) {$organics_production=0;}
			$ore_production=$production*$ore_prate;
			$ore_test = $ore_production+$row[planet_ore];
			if ($ore_test>$ore_limit) {$ore_production=0;}
			$goods_production=$production*$goods_prate;
			$goods_test = $goods_production+$row[planet_goods];
			if ($goods_test>$goods_limit) {$goods_production=0;}
			$energy_production=$production*$energy_prate;
			$energy_test = $energy_production+$row[planet_energy];
			if ($energy_test>$energy_limit) {$energy_production=0;}
			$reproduction=round($row[planet_colonists] * $colonist_reproduction_rate);
			$colonists_test = $reproduction+$row[planet_colonists];
			if ($colonists_test>$colonist_limit) {$reproduction=0;}
			if ($row[planet_owner])
			{
				$fighter_production=$production*$fighter_prate;
				$torp_production=$production*$torpedo_prate;
				echo "$torp_production - $fighter_production";
			} else {
				$fighter_production=0;
				$torp_production=0;
			}
			$query = "UPDATE universe SET planet_organics=planet_organics+$organics_production, planet_ore=planet_ore+$ore_production, planet_goods=planet_goods+$goods_production, planet_energy=planet_energy+$energy_production, planet_colonists=planet_colonists+$reproduction, base_torp=base_torp+$torp_production, planet_fighters=planet_fighters+$fighter_production, planet_credits=planet_credits*$interest_rate WHERE sector_id=$row[sector_id]";
			if ($row[planet_colonists]>$colonist_limit) { $query = "UPDATE universe SET planet_organics=planet_organics+$organics_production, planet_ore=planet_ore+$ore_production, planet_goods=planet_goods+$goods_production, planet_energy=planet_energy+$energy_production, planet_colonists=$colonist_limit, base_torp=base_torp+$torp_production, planet_fighters=planet_fighters+$fighter_production WHERE sector_id=$row[sector_id]";}
			$update_planet=mysql_query("$query");
			echo "<BR>$query<BR>";
		}
		mysql_free_result ($result1);
		echo "Planets updated.<BR><BR>";

		/* code to kick people in sector 0 */
		$result2 = mysql_query("SELECT * FROM ships WHERE sector=0");
		while ($row = mysql_fetch_array($result2))
		{
			/* per cent chance of dmg is equal hull value */
			$dmg_chance = $row[hull];
			$roll = rand(1,100);
			if ($roll<$dmg_chance)
			{
				$damage= rand(1,100);
				if ($damage>$row[armour_pts])
				{
					playerlog($row[ship_id],"Your ship was destroyed in a collision in Sector 0!");
					$query = "UPDATE ships SET ship_destroyed='Y', sector=null where ship_id=$row[ship_id]";
					echo "$row[character_name]'s ship <font color=red>destroyed</font> - $roll - ";
				} else {
					playerlog($row[ship_id],"Your ship was damaged in a collision in Sector 0.  You lost $damage armour points.");
					$query = "UPDATE ships SET armour_pts=armour_pts-$damage where ship_id=$row[ship_id]";
					echo "$row[character_name]'s ship damaged - $roll - ";
				}
				$update_ship=mysql_query("$query");
				echo "$query<BR>";
			} else { 
				echo "$row[character_name] was not hit - $roll<BR>";
			}
		}
	}

	include("footer.php3");

?> 
