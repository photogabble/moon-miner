<?
	include("config.php3");
	updatecookie();

	$title="Trading at Planet";
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
		echo "You need at least one turn to trade at a planet.<BR><BR>";
	    TEXT_GOTOMAIN();
		include("footer.php3");		
		die();
	}

	$trade_ore=round(abs($trade_ore));
	$trade_organics=round(abs($trade_organics));
	$trade_goods=round(abs($trade_goods));
	$trade_energy=round(abs($trade_energy));
	$ore_price=($ore_price + $ore_delta/4);
	$organics_price=($organics_price + $organics_delta/4);
	$goods_price=($goods_price + $goods_delta/4);
	$energy_price=($energy_price + $energy_delta/4); 

	if ($sectorinfo[planet]=='Y' && $sectorinfo[base_sells]=='Y')
	{
		$cargo_exchanged= $trade_ore + $trade_organics + $trade_goods;

		$free_holds=round(pow($level_factor,$playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
		$free_power=round(pow($level_factor,$playerinfo[power]) * 500) - $playerinfo[ship_energy];
		$total_cost=($trade_ore*$ore_price) + ($trade_organics*$organics_price) + ($trade_goods*$goods_price) + ($trade_energy*$energy_price);

		if ($free_holds < $cargo_exchanged)
		{
			echo "You do not have enough free cargo holds for the commodities you wish to purchase.  Click <a href=planet.php3>here</a> to return to the planet menu.<BR><BR>";
		} elseif ($trade_energy > $free_power) {
			echo "You do not have enough free power storage for the energy you wish to purchase.  Click <a href=planet.php3>here</a> to return to the planet menu.<BR><BR>";
		} elseif ($playerinfo[turns]<1) {
			echo "You do not have enough turns to complete the transaction.<BR><BR>";
		} elseif ($playerinfo[credits]<$total_cost) {
			echo "You do not have enough credits to complete the transaction. <BR><BR>";	
		} elseif ($trade_organics > $sectorinfo[planet_organics]){
			echo "Number of organics exceeds the supply.  ";
		} elseif ($trade_ore > $sectorinfo[planet_ore]){
			echo "Number of ore exceeds the supply.  ";
		} elseif ($trade_goods > $sectorinfo[planet_goods]){
			echo "Number of goods exceeds the supply.  ";
		} elseif ($trade_energy > $sectorinfo[planet_energy]){
			echo "Number of energy exceeds the supply.  ";
		} else {
			echo "Total cost: $total_cost<BR>Traded Ore: $trade_ore<BR>Traded Organics: $trade_organics<BR>Traded Goods: $trade_goods<BR>Traded Energy: $trade_energy<BR><BR>";
			/* Update ship cargo, credits and turns */
			$trade_result = mysql_query ("UPDATE ships SET turns=turns-1, turns_used=turns_used+1, credits=credits-$total_cost, ship_ore=ship_ore+$trade_ore, ship_organics=ship_organics+$trade_organics, ship_goods=ship_goods+$trade_goods, ship_energy=ship_energy+$trade_energy where ship_id=$playerinfo[ship_id]");

			$trade_result2 = mysql_query ("UPDATE universe SET planet_ore=planet_ore-$trade_ore, planet_organics=planet_organics-$trade_organics, planet_goods=planet_goods-$trade_goods, planet_energy=planet_energy-$trade_energy, planet_credits=planet_credits+$total_cost where  sector_id=$sectorinfo[sector_id]");
			echo "Trade completed.<BR><BR>";
		}
	} 

    gen_score($sector_info[planet_owner]);
    TEXT_GOTOMAIN();
	include("footer.php3");

?>
