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
	if ($sectorinfo[port_type]!="none" && $sectorinfo[port_type]!="special") {

		if ($sectorinfo[port_type]=="ore")
		{
			$ore_price=$ore_price - $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
			$sb_ore="Selling";
		} else {
			$ore_price=$ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
			$sb_ore="Buying";
		}
		if ($sectorinfo[port_type]=="organics")
		{
			$organics_price=$organics_price - $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
			$sb_organics="Selling";
		} else {
			$organics_price=$organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
			$sb_organics="Buying";
		}
		if ($sectorinfo[port_type]=="goods")
		{
			$goods_price=$goods_price - $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
			$sb_goods="Selling";
		} else {
			$goods_price=$goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
			$sb_goods="Buying";
		}
		if ($sectorinfo[port_type]=="energy")
		{
			$energy_price=$energy_price - $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
			$sb_energy="Selling";
		} else {
			$energy_price=$energy_price + $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
			$sb_energy="Buying";
		}
		echo "<form action=\"port2.php3\" method=\"post\">";
		
		echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
		echo "<tr><td>Commodity</td><td>Buying/Selling</td><td>Amount</td><td>Price</td><td>Buy/Sell</td><td>Cargo</td></tr>";
		if ($sb_ore=="Buying") {$amount=$playerinfo[ship_ore];} else {$amount=round(pow($level_factor,$playerinfo[hull]) * 100)-$playerinfo[ship_ore]-$playerinfo[ship_colonists];}
		echo "<tr><td>Ore</td><td>$sb_ore</td><td>$sectorinfo[port_ore]</td><td>$ore_price</td><td><input type=\"text\" name=\"trade_ore\" size=\"10\" maxlength=\"20\" value=\"$amount\"></td><td>$playerinfo[ship_ore]</td></tr>";
		if ($sb_organics=="Buying") {$amount=$playerinfo[ship_organics];} else {$amount=round(pow($level_factor,$playerinfo[hull]) * 100)-$playerinfo[ship_organics]-$playerinfo[ship_colonists];}
		echo "<tr><td>Organics</td><td>$sb_organics</td><td>$sectorinfo[port_organics]</td><td>$organics_price</td><td><input type=\"text\" name=\"trade_organics\" size=\"10\" maxlength=\"20\" value=\"$amount\"></td><td>$playerinfo[ship_organics]</td></tr>";
		if ($sb_goods=="Buying") {$amount=$playerinfo[ship_goods];} else {$amount=round(pow($level_factor,$playerinfo[hull]) * 100)-$playerinfo[ship_goods]-$playerinfo[ship_colonists];}
		echo "<tr><td>Goods</td><td>$sb_goods</td><td>$sectorinfo[port_goods]</td><td>$goods_price</td><td><input type=\"text\" name=\"trade_goods\" size=\"10\" maxlength=\"20\" value=\"$amount\"></td><td>$playerinfo[ship_goods]</td></tr>";
		if ($sb_energy=="Buying") {$amount=$playerinfo[ship_energy];} else {$amount=round(pow($level_factor,$playerinfo[power]) * 500)-$playerinfo[ship_energy];}
		echo "<tr><td>Energy</td><td>$sb_energy</td><td>$sectorinfo[port_energy]</td><td>$energy_price</td><td><input type=\"text\" name=\"trade_energy\" size=\"10\" maxlength=\"20\" value=\"$amount\"></td><td>$playerinfo[ship_energy]</td></tr>";
		echo "</table>";
		$free_holds=round(pow($level_factor,$playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
		$free_power=round(pow($level_factor,$playerinfo[power]) * 500) - $playerinfo[ship_energy];
		
		echo "<input type=\"submit\" value=\"Submit\"><input type=\"reset\" value=\"Reset\"></form>You have $free_holds empty cargo holds, can carry $free_power more energy units, and have $playerinfo[credits] credits.<BR><BR>";

	} elseif  ($sectorinfo[port_type]=="special") {
			if ($sectorinfo[sector_id]=="0") 
			{
				echo "Welcome to the Sol Supply Depot!<BR><BR>";
			} else {
				echo "Welcome to this supply depot.<BR><BR>";
			}
			echo "You have $playerinfo[credits] credits to spend.<BR><BR>";
			echo "Here you can purchase:<BR><BR>";
			echo "<form action=port2.php3 method=post>";
			echo "<table  width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
			echo "<tr><td >Devices</td><td >Price</td><td >Value</td><td >Upgrades</td><td >Price</td><td >Present Value</td><td >Additional Level</td></tr>";
			$hull_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[hull]));
			echo "<tr><td >Genesis Device</td><td >$dev_genesis_price</td><td ><input type=\"text\" name=\"dev_genesis_number\" size=\"4\" maxlength=\"4\" value=\"0\"></td><td >Hull</td><td >$hull_upgrade_cost</td><td >$playerinfo[hull]</td><td ><input type=\"checkbox\" name=\"hull_upgrade\" value=\"1\"></td></tr>";
			$engine_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[engines]));
			echo "<tr><td >Beacon</td><td >$dev_beacon_price</td><td ><input type=\"text\" name=\"dev_beacon_number\" size=\"4\" maxlength=\"4\" value=\"0\"></td><td >Engines</td><td >$engine_upgrade_cost</td><td >$playerinfo[engines]</td><td ><input type=\"checkbox\" name=\"engine_upgrade\" value=\"1\"></td></tr>";
			$power_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[power]));
			echo "<tr><td >Emergency Warp Device</td><td >$dev_emerwarp_price</td><td ><input type=\"text\" name=\"dev_emerwarp_number\" size=\"4\" maxlength=\"4\" value=\"0\"></td><td >Power</td><td >$power_upgrade_cost</td><td >$playerinfo[power]</td><td ><input type=\"checkbox\" name=\"power_upgrade\" value=\"1\"></td></tr>";
			$computer_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[computer]));
			echo "<tr><td >Warp Editor</td><td >$dev_warpedit_price</td><td ><input type=\"text\" name=\"dev_warpedit_number\" size=\"4\" maxlength=\"4\" value=\"0\"></td><td >Computer</td><td >$computer_upgrade_cost</td><td >$playerinfo[computer]</td><td ><input type=\"checkbox\" name=\"computer_upgrade\" value=\"1\"></td></tr>";
			$sensors_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[sensors]));
			echo "<tr><td ></td><td ></td><td ></td><td >Sensors</td><td >$sensors_upgrade_cost</td><td >$playerinfo[sensors]</td><td ><input type=\"checkbox\" name=\"sensors_upgrade\" value=\"1\"></td></tr>";
			$beams_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[beams]));
			echo "<tr><td >Mine Deflector</td><td >$dev_minedeflector_price</td><td ><input type=\"text\" name=\"dev_minedeflector_number\" size=\"4\" maxlength=\"4\" value=\"0\"></td><td >Beam Weapons</td><td >$beams_upgrade_cost</td><td >$playerinfo[beams]</td><td ><input type=\"checkbox\" name=\"beams_upgrade\" value=\"1\"></td></tr>";
			$armour_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[armour]));
			echo "<tr><td >Escape Pod</td>";
			if ($playerinfo[dev_escapepod]=="N") 
			{
				echo "<td >$dev_escapepod_price</td><td ><input type=\"checkbox\" name=\"escapepod_purchase\" value=\"1\"></td>";
			} else {
				echo "<td >NA</td><td >Equipped</td>";
			}
			echo "<td >Armour</td><td >$armour_upgrade_cost</td><td >$playerinfo[armour]</td><td ><input type=\"checkbox\" name=\"armour_upgrade\" value=\"1\"></td></tr>";
			$cloak_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[cloak]));
			echo "<tr><td >Fuel Scoop</td>";
			if ($playerinfo[dev_fuelscoop]=="N") 
			{
				echo "<td >$dev_fuelscoop_price</td><td ><input type=\"checkbox\" name=\"fuelscoop_purchase\" value=\"1\"></td>";
			} else {
				echo "<td >NA</td><td >Equipped</td>";
			}
			echo "<td >Cloak</td><td >$cloak_upgrade_cost</td><td >$playerinfo[cloak]</td><td ><input type=\"checkbox\" name=\"cloak_upgrade\" value=\"1\"></td></tr>";
			$torp_launchers_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[torp_launchers]));
			echo "<tr><td ></td><td ></td><td ></td><td >Torpedo Launchers</td><td >$torp_launchers_upgrade_cost</td><td >$playerinfo[torp_launchers]</td><td ><input type=\"checkbox\" name=\"torp_launchers_upgrade\" value=\"1\"></td></tr>";
			$shields_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[shields]));
			echo "<tr><td ></td><td ></td><td ></td><td >Shields</td><td >$shields_upgrade_cost</td><td >$playerinfo[shields]</td><td ><input type=\"checkbox\" name=\"shields_upgrade\" value=\"1\"></td></tr>";
			echo "</table>";
			echo "<BR>Other Supplies:<BR><BR>";
			echo "<table  width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
			echo "<tr><td >Item</td><td >Present</td><td >Max</td><td >Price</td><td >Value</td><td >Item</td><td >Present</td><td >Max</td><td >Price</td><td >Value</td></tr>";
			$fighter_max=round(pow($level_factor,$playerinfo[computer])*100);
			$torpedo_max=round(pow($level_factor,$playerinfo[torp_launchers])*100);
			$armour_max=round(pow($level_factor,$playerinfo[armour])*100);
			echo "<tr><td >Fighters</td><td >$playerinfo[ship_fighters]</td><td >$fighter_max</td><td >$fighter_price</td><td ><input type=\"text\" name=\"fighter_number\" size=\"6\" maxlength=\"10\" value=\"0\"></td><td >Torpedoes</td><td >$playerinfo[torps]</td><td >$torpedo_max</td><td >$torpedo_price</td><td ><input type=\"text\" name=\"torpedo_number\" size=\"6\" maxlength=\"10\" value=\"0\"></td></tr>";
			$colonist_max=round(pow($level_factor,$playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
			echo "<tr><td >Armour Points</td><td >$playerinfo[armour_pts]</td><td >$armour_max</td><td >$armour_price</td><td ><input type=\"text\" name=\"armour_number\" size=\"6\" maxlength=\"10\" value=\"0\"></td><td >Colonists</td><td >$playerinfo[ship_colonists]</td><td >+$colonist_max</td><td >$colonist_price</td><td ><input type=\"text\" name=\"colonist_number\" size=\"6\" maxlength=\"10\" value=\"0\"></td></tr>";
			echo "</table>";
			echo "<input type=\"submit\" value=\"Submit\">&nbsp;<input type=\"reset\" value=\"Clear\">";
			echo "</form>";
			echo "If you would like to dump all your colonists here, click <a href=dump.php3>here</a>.<BR><BR>";
	} else { echo "There is no port here!<BR><BR>";}
	

	echo "Click <a href=main.php3>here</a> to return to main menu without trading.";


	include("footer.php3");

?>
