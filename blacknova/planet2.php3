<?
	include("config.php3");
	updatecookie();

	$title="Planetary Transfer";
	include("header.php3");

	connectdb();
	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");

	$playerinfo=mysql_fetch_array($result);
        bigtitle();
	if ($playerinfo[turns]<1)
	{
		echo "You need at least one turn to perform a planetary transfer.<BR><BR>";
		echo "Click <a href=main.php3>here</a> to return to Main Menu.";
		include("footer.php3");		
		die();
	}

	$result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$playerinfo[sector]");
	$sectorinfo=mysql_fetch_array($result2);
	$free_holds=round(pow($level_factor,$playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
	$free_power=round(pow($level_factor,$playerinfo[power]) * 500) - $playerinfo[ship_energy];
	$fighter_max=round(pow($level_factor,$playerinfo[computer])*100)-$playerinfo[ship_fighters];
	$torpedo_max=round(pow($level_factor,$playerinfo[torp_launchers])*100)-$playerinfo[torps];	
	if ($tpore==0) {$tpore=1;}
	$transfer_ore=(round(abs($transfer_ore))*$tpore);
	if ($tporganics==0) {$tporganics=1;}
	$transfer_organics=round(abs($transfer_organics))*$tporganics;
	if ($tpgoods==0) {$tpgoods=1;}
	$transfer_goods=round(abs($transfer_goods))*$tpgoods;
	if ($tpenergy==0) {$tpenergy=1;}
	$transfer_energy=round(abs($transfer_energy))*$tpenergy;
	if ($tpcolonists==0) {$tpcolonists=1;}
	$transfer_colonists=round(abs($transfer_colonists))*$tpcolonists;
	if ($tpcredits==0) {$tpcredits=1;}
	$transfer_credits=round(abs($transfer_credits))*$tpcredits;
	if ($tptorps==0) {$tptorps=1;}
	$transfer_torps=round(abs($transfer_torps))*$tptorps;
	if ($tpfighters==0) {$tpfighters=1;}
	$transfer_fighters=round(abs($transfer_fighters))*$tpfighters;
	$total_holds_needed=$transfer_ore+$transfer_organics+$transfer_goods+$transfer_colonists;
	if ($total_holds_needed>$free_holds) { die ("Not enough holds for requested transfer.<BR><BR>Click <a href=planet.php3>here</a> to return to planet menu.<BR><BR>Click <a href=main.php3>here</a> to return to main menu.");}
	if ($sectorinfo[planet]=="Y")
	{
		if ( $sectorinfo[planet_owner]==$playerinfo[ship_id])
		{
			if ($transfer_ore<0 && $playerinfo[ship_ore]<abs($transfer_ore))
			{
				echo "Not enough ore for requested transfer.<BR>";
				$transfer_ore=0;
			} elseif ($transfer_ore>0 && $sectorinfo[planet_ore]<abs($transfer_ore)) {
				echo "Not enough ore for requested transfer.<BR>";
				$transfer_ore=0;
			}
			if ($transfer_organics<0 && $playerinfo[ship_organics]<abs($transfer_organics))
			{
				echo "Not enough organics for requested transfer.<BR>";
				$transfer_organics=0;
			} elseif ($transfer_organics>0 && $sectorinfo[planet_organics]<abs($transfer_organics)) {
				echo "Not enough organics for requested transfer.<BR>";
				$transfer_organics=0;
			}
			if ($transfer_goods<0 && $playerinfo[ship_goods]<abs($transfer_goods))
			{
				echo "Not enough goods for requested transfer.<BR>";
				$transfer_goods=0;
			} elseif ($transfer_goods>0 && $sectorinfo[planet_goods]<abs($transfer_goods)) {
				echo "Not enough goods for requested transfer.<BR>";
				$transfer_goods=0;
			}
			if ($transfer_energy<0 && $playerinfo[ship_energy]<abs($transfer_energy))
			{
				echo "Not enough energy for requested transfer.<BR>";
				$transfer_energy=0;
			} elseif ($transfer_energy>0 && $sectorinfo[planet_energy]<abs($transfer_energy)) {
				echo "Not enough energy for requested transfer.<BR>";
				$transfer_energy=0;
			} elseif ($transfer_energy>0 && abs($transfer_energy)>$free_power) {
				echo "Not enough power capacity for requested energy transfer.<BR>";
				$transfer_energy=0;
			}				
			if ($transfer_colonists<0 && $playerinfo[ship_colonists]<abs($transfer_colonists))
			{
				echo "Not enough colonists for requested transfer.<BR>";
				$transfer_colonists=0;
			} elseif ($transfer_colonists>0 && $sectorinfo[planet_colonists]<abs($transfer_colonists)) {
				echo "Not enough colonists for requested transfer.<BR>";
				$transfer_colonists=0;
			}
			if ($transfer_fighters<0 && $playerinfo[ship_fighters]<abs($transfer_fighters))
			{
				echo "Not enough fighters for requested transfer.<BR>";
				$transfer_fighters=0;
			} elseif ($transfer_fighters>0 && $sectorinfo[planet_fighters]<abs($transfer_fighters)) {
				echo "Not enough fighters for requested transfer.<BR>";
				$transfer_fighters=0;
			} elseif ($transfer_fighters>0 && abs($transfer_fighters)>$fighter_max) {
				echo "Not enough computer capacity for requested fighters transfer.<BR>";
				$transfer_fighters=0;
			}
			if ($transfer_torps<0 && $playerinfo[torps]<abs($transfer_torps))
			{
				echo "Not enough torpedoes for requested transfer.<BR>";
				$transfer_torps=0;
			} elseif ($transfer_torps>0 && $sectorinfo[base_torp]<abs($transfer_torps)) {
				echo "Not enough torpedoes for requested transfer.<BR>";
				$transfer_torps=0;
			} elseif ($transfer_torps>0 && abs($transfer_torps)>$torpedo_max) {
				echo "Not enough launcher capacity for requested torpedo transfer.<BR>";
				$transfer_torps=0;
			}		
			if ($transfer_credits<0 && $playerinfo[credits]<abs($transfer_credits))
			{
				echo "Not enough credits for requested transfer.<BR>";
				$transfer_credits=0;
			} elseif ($transfer_credits>0 && $sectorinfo[planet_credits]<abs($transfer_credits)) {
				echo "Not enough credits for requested transfer.<BR>";
				$transfer_credits=0;
			}
			$update1 = mysql_query ("UPDATE ships SET ship_ore=ship_ore+$transfer_ore, ship_organics=ship_organics+$transfer_organics, ship_goods=ship_goods+$transfer_goods, ship_energy=ship_energy+$transfer_energy, ship_colonists=ship_colonists+$transfer_colonists, torps=torps+$transfer_torps, ship_fighters=ship_fighters+$transfer_fighters, credits=credits+$transfer_credits, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
			$update2 = mysql_query ("UPDATE universe SET planet_ore=planet_ore-$transfer_ore, planet_organics=planet_organics-$transfer_organics, planet_goods=planet_goods-$transfer_goods, planet_energy=planet_energy-$transfer_energy, planet_colonists=planet_colonists-$transfer_colonists, base_torp=base_torp-$transfer_torps, planet_fighters=planet_fighters-$transfer_fighters, planet_credits=planet_credits-$transfer_credits WHERE sector_id=$sectorinfo[sector_id]");
			echo "Transfer complete.<BR>Click <a href=planet.php3>here</a> to return to planet menu.<BR><BR>";
		} else { echo "You do not own this planet.<BR><BR>";}
	
	} else { echo "There is no planet here.<BR><BR>";}

	echo "Click <a href=main.php3>here</a> to return to main menu.";
	include("footer.php3");

?> 
