<?
	include("config.php3");
	updatecookie();

	$title="Trading at Port";
	include("header.php3");
	connectdb();
	
	if (checklogin()) {die();}


	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);

	$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
	$sectorinfo=mysql_fetch_array($result2);
        bigtitle();
	if ($playerinfo[turns]<1)
	{
		echo "You need at least one turn to trade at a port.<BR><BR>";
		echo "Click <a href=main.php3>here</a> to return to Main Menu.";
		include("footer.php3");		
		die();
	}

	$trade_ore=round(abs($trade_ore));
	$trade_organics=round(abs($trade_organics));
	$trade_goods=round(abs($trade_goods));
	$trade_energy=round(abs($trade_energy));
	if ($sectorinfo[port_type]=="special")
	{
		/* the code for a special port will go here! */
		if ($hull_upgrade) {$hull_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[hull]));}
		if ($engine_upgrade) {$engine_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[engines]));}
		if ($power_upgrade) {$power_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[power]));}
		if ($computer_upgrade) {$computer_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[computer]));}
		if ($sensors_upgrade) {$sensors_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[sensors]));}
		if ($beams_upgrade) {$beams_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[beams]));}
		if ($armour_upgrade) {$armour_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[armour]));}
		if ($cloak_upgrade) {$cloak_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[cloak]));}
		if ($torp_launchers_upgrade) {$torp_launchers_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[torp_launchers]));}
		if ($shields_upgrade) {$shields_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[shields]));}
		$fighter_number=round(abs($fighter_number));
		$fighter_max=round(pow($level_factor,$playerinfo[computer])*100)-$playerinfo[ship_fighters];
		if ($fighter_max<0) {$fighter_max=0;}
		if ($fighter_number>$fighter_max){$fighter_number=$fighter_max;}
		$fighter_cost=$fighter_number*$fighter_price;
		$torpedo_number=round(abs($torpedo_number));
		$torpedo_max=round(pow($level_factor,$playerinfo[torp_launchers])*100)-$playerinfo[torps];
		if ($torpedo_max<0) {$torpedo_max=0;}
		if ($torpedo_number>$torpedo_max){$torpedo_number=$torpedo_max;}		
		$torpedo_cost=$torpedo_number*$torpedo_price;
		$armour_number=round(abs($armour_number));
		$armour_max=round(pow($level_factor,$playerinfo[armour])*100)-$playerinfo[armour_pts];		
		if ($armour_max<0) {$armour_max=0;}
		if ($armour_number>$armour_max){$armour_number=$armour_max;}
		$armour_cost=$armour_number*$armour_price;
		$colonist_number=round(abs($colonist_number));
		$colonist_max=round(pow($level_factor,$playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
		if ($colonist_number>$colonist_max) {$colonist_number=$colonist_max;}
		$colonist_cost=$colonist_number*$colonist_price;		
		$dev_genesis_number=round(abs($dev_genesis_number));
		$dev_genesis_cost=$dev_genesis_number*$dev_genesis_price;
		$dev_beacon_number=round(abs($dev_beacon_number));
		$dev_beacon_cost=$dev_beacon_number*$dev_beacon_price;
		$dev_emerwarp_number=round(abs($dev_emerwarp_number));
		$dev_emerwarp_cost=$dev_emerwarp_number*$dev_emerwarp_price;
		$dev_warpedit_number=round(abs($dev_warpedit_number));
		$dev_warpedit_cost=$dev_warpedit_number*$dev_warpedit_price;
		$dev_minedeflector_number=round(abs($dev_minedeflector_number));
		$dev_minedeflector_cost=$dev_minedeflector_number*$dev_minedeflector_price;
		if ($escapepod_purchase){$dev_escapepod_cost=$dev_escapepod_price;}
		if ($fuelscoop_purchase){$dev_fuelscoop_cost=$dev_fuelscoop_price;}
		$total_cost=$hull_upgrade_cost+$engine_upgrade_cost+$power_upgrade_cost+$computer_upgrade_cost+$sensors_upgrade_cost+$beams_upgrade_cost+$armour_upgrade_cost+$cloak_upgrade_cost+$torp_launchers_upgrade_cost+$fighter_cost+$torpedo_cost+$armour_cost+$colonist_cost+$dev_genesis_cost+$dev_beacon_cost+$dev_emerwarp_cost+$dev_warpedit_cost+$dev_minedeflector_cost+$dev_escapepod_cost+$dev_fuelscoop_cost+$shields_upgrade_cost;
		if ($total_cost>$playerinfo[credits])
		{
			echo "You do not have enough credits for this transaction.  The total cost is $total_cost credits and you only have $playerinfo[credits] credits.<BR><BR>Click <a href=port.php3>here</a> to return to the supply depot.<BR><BR>";
		} else {
			echo "Total cost is $total_cost credits.<BR><BR>";
			$query = "UPDATE ships SET credits=credits-$total_cost";
			if ($hull_upgrade) 
			{
				$query=$query . ", hull=hull+1";
				echo "Hull upgraded.<BR>";
			}
			if ($engine_upgrade) 
			{
				$query=$query . ", engines=engines+1";
				echo "Engines upgraded<BR>";
			}
			if ($power_upgrade) 
			{
				$query=$query . ", power=power+1";
				echo "Power upgraded<BR>";
			}
			if ($computer_upgrade) 
			{
				$query=$query . ", computer=computer+1";
				echo "Computer upgraded.<BR>";
			}
			if ($sensors_upgrade) 
			{
				$query=$query . ", sensors=sensors+1";
				echo "Sensors upgraded.<BR>";
			}
			if ($beams_upgrade) 
			{
				$query=$query . ", beams=beams+1";
				echo "Beam Weapons upgraded.<BR>";
			}
			if ($armour_upgrade) 
			{
				$query=$query . ", armour=armour+1";
				echo "Armour upgraded.<BR>";
			}
			if ($cloak_upgrade) 
			{
				$query=$query . ", cloak=cloak+1";
				echo "Cloak upgraded.<BR>";
			}
			if ($torp_launchers_upgrade) 
			{
				$query=$query . ", torp_launchers=torp_launchers+1";
				echo "Torpedo Launchers upgraded.<BR>";
			}
			if ($shields_upgrade) 
			{
				$query=$query . ", shields=shields+1";
				echo "Shields upgraded.<BR>";
			}
			if ($fighter_number) 
			{
				$query=$query . ", ship_fighters=ship_fighters+$fighter_number";
				echo "$fighter_number fighters added.<BR>";
			}
			if ($torpedo_number) 
			{
				$query=$query . ", torps=torps+$torpedo_number";
				echo "$torpedo_number torpedoes added.<BR>";
			}
			if ($armour_number) 
			{
				$query=$query . ", armour_pts=armour_pts+$armour_number";
				echo "$armour_number points of armour added.<BR>";
			}
			if ($colonist_number) 
			{
				$query=$query . ", ship_colonists=ship_colonists+$colonist_number";
				echo "$colonist_number colonists loaded.<BR>";
			}
			if ($dev_genesis_number) 
			{
				$query=$query . ", dev_genesis=dev_genesis+$dev_genesis_number";
				echo "$dev_genesis_number genesis devices added.<BR>";
			}
			if ($dev_beacon_number) 
			{
				$query=$query . ", dev_beacon=dev_beacon+$dev_beacon_number";
				echo "$dev_beacon_number beacons added.<BR>";
			}
			if ($dev_emerwarp_number) 
			{
				$query=$query . ", dev_emerwarp=dev_emerwarp+$dev_emerwarp_number";
				echo "$dev_emerwarp_number emergency warp devices added.<BR>";
			}
			if ($dev_warpedit_number) 
			{
				$query=$query . ", dev_warpedit=dev_warpedit+$dev_warpedit_number";
				echo "$dev_warpedit_number warp editors added.<BR>";
			}
			if ($dev_minedeflector_number) 
			{
				$query=$query . ", dev_minedeflector=dev_minedeflector+$dev_minedeflector_number";
				echo "$dev_minedeflector_number minedeflectors added.<BR>";
			}
			if ($escapepod_purchase) 
			{
				$query=$query . ", dev_escapepod='Y'";
				echo "Escape pod installed.<BR>";
			}
			if ($fuelscoop_purchase) 
			{
				$query=$query . ", dev_fuelscoop='Y'";
				echo "Fuelscoop installed.<BR>";
			}
			$query=$query . ", turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]";
			$purchase = mysql_query("$query");
			echo "<BR>";
		}


	} elseif ($sectorinfo[port_type]!="none") {
		/* the code for an ore port will go here! */
		if ($sectorinfo[port_type]=="ore")
		{
			$ore_price=$ore_price - $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;			
		} else {
			$ore_price=$ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
			$trade_ore=-$trade_ore;
		}
		if ($sectorinfo[port_type]=="organics")
		{
			$organics_price=$organics_price - $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;			
		} else {
			$organics_price=$organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
			$trade_organics=-$trade_organics;
		}
		if ($sectorinfo[port_type]=="goods")
		{
			$goods_price=$goods_price - $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;			
		} else {
			$goods_price=$goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
			$trade_goods=-$trade_goods;
		}
		if ($sectorinfo[port_type]=="energy")
		{
			$energy_price=$energy_price - $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;			
		} else {
			$energy_price=$energy_price + $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
			$trade_energy=-$trade_energy;
		}


		$cargo_exchanged= $trade_ore + $trade_organics + $trade_goods;

		$free_holds=round(pow($level_factor,$playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
		$free_power=round(pow($level_factor,$playerinfo[power]) * 500) - $playerinfo[ship_energy];
		$total_cost=($trade_ore*$ore_price) + ($trade_organics*$organics_price) + ($trade_goods*$goods_price) + ($trade_energy*$energy_price);

		if ($free_holds < $cargo_exchanged)
		{
			echo "You do not have enough free cargo holds for the commodities you wish to purchase.  Click <a href=port.php3>here</a> to return to the port menu.<BR><BR>";
		} elseif ($trade_energy > $free_power) {
			echo "You do not have enough free power storage for the energy you wish to purchase.  Click <a href=port.php3>here</a> to return to the port menu.<BR><BR>";
		} elseif ($playerinfo[turns]<1) {
			echo "You do not have enough turns to complete the transaction.<BR><BR>";
		} elseif ($playerinfo[credits]<$total_cost) {
			echo "You do not have enough credits to complete the transaction. <BR><BR>";	
		} elseif ($trade_ore < 0 && abs($playerinfo[ship_ore])<abs($trade_ore)){
			echo "You do not have enough ore to complete the transaction. ";
		} elseif ($trade_organics <0 && abs($playerinfo[ship_organics])<abs($trade_organics)){
			echo "You do not have enough organics to complete the transaction. ";
		} elseif ($trade_goods <0 && abs($playerinfo[ship_goods])<abs($trade_goods)){
			echo "You do not have enough goods to complete the transaction. ";
		} elseif ($trade_energy < 0 && abs($playerinfo[ship_energy])<abs($trade_energy)){
			echo "You do not have enough energy to complete the transaction. ";
		} elseif (abs($trade_organics) > $sectorinfo[port_organics]){
			echo "Number of organics exceeds the supply/demand.  ";
		} elseif (abs($trade_ore) > $sectorinfo[port_ore]){
			echo "Number of ore exceeds the supply/demand.  ";
		} elseif (abs($trade_goods) > $sectorinfo[port_goods]){
			echo "Number of goods exceeds the supply/demand.  ";
		} elseif (abs($trade_energy) > $sectorinfo[port_energy]){
			echo "Number of energy exceeds the supply/demand.  ";
		} else {
			echo "Total cost: " . number_format($total_cost, 0, "", "") . " credits.<BR>";
			echo "Traded Ore: $trade_ore<BR>Traded Organics: $trade_organics<BR>Traded Goods: $trade_goods<BR>Traded Energy: $trade_energy<BR><BR>";
			/* Update ship cargo, credits and turns */
			$trade_result = mysql_query ("UPDATE ships SET turns=turns-1, turns_used=turns_used+1, credits=credits-$total_cost, ship_ore=ship_ore+$trade_ore, ship_organics=ship_organics+$trade_organics, ship_goods=ship_goods+$trade_goods, ship_energy=ship_energy+$trade_energy where ship_id=$playerinfo[ship_id]");
			/* Make all trades positive to change port values*/
			$trade_ore=round(abs($trade_ore));
			$trade_organics=round(abs($trade_organics));
			$trade_goods=round(abs($trade_goods));
			$trade_energy=round(abs($trade_energy));
			/* Decrease supply and demand on port */
			$trade_result2 = mysql_query ("UPDATE universe SET port_ore=port_ore-$trade_ore, port_organics=port_organics-$trade_organics, port_goods=port_goods-$trade_goods, port_energy=port_energy-$trade_energy where sector_id=$sectorinfo[sector_id]");
			echo "Trade completed.<BR><BR>";
		}
	} 

	echo "Click <a href=main.php3>here</a> to return to main menu.";
	include("footer.php3");

?>
