<?
include("config.php3");
updatecookie();

$title="Trade Route";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships WRITE, universe WRITE");

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);
$freeholds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
$maxholds = NUM_HOLDS($playerinfo[hull]);
$maxenergy = NUM_ENERGY($playerinfo[power]);
if ($playerinfo[ship_colonists] < 0 || $playerinfo[ship_ore] < 0 || $playerinfo[ship_organics] < 0 || $playerinfo[ship_goods] < 0 || $playerinfo[ship_energy] < 0 || $freeholds < 0)
{
	if ($playerinfo[ship_colonists] < 0 || $playerinfo[ship_colonists] > $maxholds) 
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_colonists] colonists, Max Holds: $maxholds.");
		$playerinfo[ship_colonists] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship set to $playerinfo[ship_colonists] colonists.");
	}
	if ($playerinfo[ship_ore] < 0 || $playerinfo[ship_ore] > $maxholds) 
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_ore] ore, Max Holds: $maxholds.");
		$playerinfo[ship_ore] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_ore] ore.");
	}
	if ($playerinfo[ship_organics] < 0 || $playerinfo[ship_organics] > $maxholds) 
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_organics] organics, Max Holds: $maxholds.");
		$playerinfo[ship_organics] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_organics] organics.");
	}
	if ($playerinfo[ship_goods] < 0 || $playerinfo[ship_goods] > $maxholds) 
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_goods] goods, Max Holds: $maxholds.");	
		$playerinfo[ship_goods] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_goods] goods");
	}
	if ($playerinfo[ship_energy] < 0 || $playerinfo[ship_energy] > $maxenergy)
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_energy] energy, Max Energy: $maxenergy.");
		$playerinfo[ship_energy] = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_energy] energy");
	}
	if ($freeholds < 0)
	{
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s ship had $playerinfo[ship_freeholds] holds");
		$freeholds = 0;
		adminlog($playerinfo[ship_id], "$playerinfo[ship_name]'s set to $playerinfo[ship_freeholds] holds");
	}
$update1 = mysql_query("UPDATE ships SET ship_ore=$playerinfo[ship_ore], ship_organics=$playerinfo[ship_organics], ship_goods=$playerinfo[ship_goods], ship_energy=$playerinfo[ship_energy], ship_colonists=$playerinfo[ship_colonists] WHERE ship_id=$playerinfo[ship_id]"); 
}

bigtitle();

$deg = pi() / 180;

if(empty($phase))
{
  /* player selects sectors to rs move between and trade at */
  echo "<FORM ACTION=traderoute.php3 METHOD=POST>";
  echo "You are presently in sector $playerinfo[sector] - and there are sectors available from 0 to $sector_max.<BR><BR>";    
  echo "What sector would you like to attempt to get to through realspace and trade maximum commodities at:  <input type=text name=destination size=10 maxlength=10><BR><BR>";
  echo "<INPUT TYPE=HIDDEN NAME=phase VALUE=1>";
  echo "<INPUT TYPE=SUBMIT VALUE=Evaluate><BR><BR>";
  echo "</FORM>";
}
elseif($phase == 1)
{
  $result2 = mysql_query("SELECT port_type, angle1, angle2, distance FROM universe WHERE sector_id=$playerinfo[sector]");
  $start = mysql_fetch_array($result2);
  $result3 = mysql_query("SELECT port_type, angle1, angle2, distance FROM universe WHERE sector_id=$destination");
  $finish = mysql_fetch_array($result3);
  $sa1 = $start[angle1] * $deg;
  $sa2 = $start[angle2] * $deg;
  $fa1 = $finish[angle1] * $deg;
  $fa2 = $finish[angle2] * $deg;
  $x = $start[distance] * sin($sa1) * cos($sa2) - $finish[distance] * sin($fa1) * cos($fa2);
  $y = $start[distance] * sin($sa1) * sin($sa2) - $finish[distance] * sin($fa1) * sin($fa2);
  $z = $start[distance] * cos($sa1) - $finish[distance] * cos($fa1);
  $distance = round(sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2)));
  $shipspeed = pow($level_factor, $playerinfo[engines]);
  $triptime = round($distance / $shipspeed);
  if(!$triptime && $destination != $playerinfo[sector])
  {
    $triptime = 1;
  }
  if($playerinfo[dev_fuelscoop] == "Y")
  {
    $energyscooped = $distance * 100;
  }
  else
  {
    $energyscooped = 0;
  }
  if($playerinfo[dev_fuelscoop] == "Y" && !$energyscooped && $triptime == 1)
  {
    $energyscooped = 100;
  }
  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
  if($free_power < $energyscooped)
  {
    $energyscooped = $free_power;
  }
  if($energyscooped < 1)
  {
    $energyscooped = 0;
  }
  if($destination == $playerinfo[sector])
  {
    $triptime = 0;
    $energyscooped = 0;
  }    
  /* inform player of # of turns for return trip and docking at port - if player doesn't have enough turns, fail. */
  $triptime = ($triptime + 1) * 2;
  echo "It would take " . NUMBER($triptime) . " turns to go to sector $destination, trade all of your commodities, return to sector $playerinfo[sector] and again trade all of your commodities.  You would gain " . NUMBER($energyscooped) . " energy to trade for each leg of the trip.<BR><BR>";
  if($triptime > $playerinfo[turns])
  {
    echo "You do not have enough turns left. You only have " . NUMBER($playerinfo[turns]) . ".<BR><BR>";
  }
  else
  {
    echo "Click <A HREF=traderoute.php3?phase=2&destination=$destination>here</A> to engage.<BR><BR>";
  }
}
else
{
  $result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$playerinfo[sector]");
  $start = mysql_fetch_array($result2);
  $result3 = mysql_query("SELECT * FROM universe WHERE sector_id=$destination");
  $finish = mysql_fetch_array($result3);
  $sa1 = $start[angle1] * $deg;
  $sa2 = $start[angle2] * $deg;
  $fa1 = $finish[angle1] * $deg;
  $fa2 = $finish[angle2] * $deg;
  $x = $start[distance] * sin($sa1) * cos($sa2) - $finish[distance] * sin($fa1) * cos($fa2);
  $y = $start[distance] * sin($sa1) * sin($sa2) - $finish[distance] * sin($fa1) * sin($fa2);
  $z = $start[distance] * cos($sa1) - $finish[distance] * cos($fa1);
  $distance = round(sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2)));
  $shipspeed = pow($level_factor, $playerinfo[engines]);
  $triptime = round($distance / $shipspeed);
  if($triptime == 0 && $destination != $playerinfo[sector])
  {
    $triptime = 1;
  }
  if($playerinfo[dev_fuelscoop] == "Y")
  {
    $energyscooped = $distance * 100;
  }
  else
  {
    $energyscooped = 0;
  }
  if($playerinfo[dev_fuelscoop] == "Y" && !$energyscooped && $triptime == 1)
  {
    $energyscooped = 100;
  }
  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
  if($free_power < $energyscooped)
  {
    $energyscooped = $free_power;
  }
  if($energyscooped < 1)
  {
    $energyscooped = 0;
  }
  if($destination == $playerinfo[sector])
  {
    $triptime = 0;
    $energyscooped = 0;
  }    
  /* inform player of # of turns for return trip and docking at port - if player doesn't have enough turns, fail. */
  $triptime = ($triptime + 1) * 2;
  /* inform player of what commodities are traded at ports - if they are the same, or one/both is special, or there is no port in either sector fail. */
  if($triptime > $playerinfo[turns])
  {
    echo "You do not have enough turns left. You only have " . NUMBER($playerinfo[turns]) . ".<BR><BR>";
  }
  else
  {
    echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
    echo "<TR BGCOLOR=\"$color_header\"><TD><B>Sector $destination: $finish[port_type]</B></TD></TR>";
    echo "</TABLE><BR>";
    $ore_t1 = 0;
    $organics_t1 = 0;
    $goods_t1 = 0;
    $energy_t1 = 0;
    $t1_value = 0;
    if($finish[port_type] == "none" || $finish[port_type] == "special")
    {
      echo "This port does not trade commodities.<BR><BR>";
    }
    if($finish[port_type] == "energy")
    {
      $goods_t1 = $playerinfo[ship_goods];
      $goods_pricet1 = $goods_price + $goods_delta * $finish[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t1 > $finish[port_goods])
      {
        $goods_t1 = $finish[port_goods];
      }
      $ore_t1 = $playerinfo[ship_ore];
      $ore_pricet1 = $ore_price + $ore_delta * $finish[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t1 > $finish[port_ore])
      {
        $ore_t1 = $finish[port_ore];
      }
      $organics_t1 = $playerinfo[ship_organics];
      $organics_pricet1 = $organics_price + $organics_delta * $finish[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t1 > $finish[port_organics])
      {
        $organics_t1 = $finish[port_organics];
      }
      $energy_t1 = $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy] - $energyscooped;
      $energy_pricet1 = $energy_price - $energy_delta * $finish[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t1 > $finish[port_energy])
      {
        $energy_t1 = $finish[port_energy];
      }
      
      $freebattery = NUM_ENERGY($playerinfo[power]) - $energy_t1 - $playerinfo[ship_energy] - $energyscooped;
      if ($energy_t1 >> $freebattery) $energy_t1 = $freebattery;
      $freebattery = $freebattery - $enerty_t1;
      
      $freeholds = $freeholds + $goods_t1 + $ore_t1 + $organics_t1;
      
      $t1_value = $goods_pricet1 * $goods_t1 + $ore_pricet1 * $ore_t1 + $organics_pricet1 * $organics_t1 -
        $energy_pricet1 * $energy_t1;
      if($t1_value < 0 && abs($t1_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum energy at $destination.<BR><BR>";
        $t1_value = $t1_value + $energy_pricet1 * $energy_t1;
        $energy_t1 = 0;
      }
      echo "Sold " . NUMBER($ore_t1) . " ore at $ore_pricet1<BR>";
      echo "Sold " . NUMBER($organics_t1) . " organics at $organics_pricet1<BR>";
      echo "Sold " . NUMBER($goods_t1) . " goods at $goods_pricet1<BR><BR>";
      echo "Bought " . NUMBER($energy_t1) . " units of energy at $energy_pricet1.<BR><BR>";
      $energy_t1 = -$energy_t1;
    }
    if($finish[port_type] == "goods")
    {
      $ore_t1 = $playerinfo[ship_ore];
      $ore_pricet1 = $ore_price + $ore_delta * $finish[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t1 > $finish[port_ore]) $ore_t1 = $finish[port_ore];
      
      $organics_t1 = $playerinfo[ship_organics];
      $organics_pricet1 = $organics_price + $organics_delta * $finish[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t1 > $finish[port_organics]) $organics_t1 = $finish[port_organics];
      
      $freeholds = $freeholds + $ore_t1 + $organics_t1;
      //$goods_t1 = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      $goods_t1 = $freeholds;
      $goods_pricet1 = $goods_price - $goods_delta * $finish[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t1 > $finish[port_goods]) $goods_t1 = $finish[port_goods];
      $freeholds = $freeholds - $goods_t1;
            
      $energy_t1 = $playerinfo[ship_energy] + $energyscooped;
      $energy_pricet1 = $energy_price + $energy_delta * $finish[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t1 > $finish[port_energy]) $energy_t1 = $finish[port_energy];
      
      $t1_value = -$goods_pricet1 * $goods_t1 + $ore_pricet1 * $ore_t1 + $organics_pricet1 * $organics_t1 +
        $energy_pricet1 * $energy_t1;
      if($t1_value < 0 && abs($t1_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum goods at $destination.<BR><BR>";
        $t1_value = $t1_value + $goods_pricet1 * $goods_t1;
        $goods_t1 = 0;
      }
      
      echo "Sold " . NUMBER($ore_t1) . " ore at $ore_pricet1<BR>";
      echo "Sold " . NUMBER($organics_t1) . " organics at $organics_pricet1<BR>";
      echo "Sold " . NUMBER($energy_t1) . " energy at $energy_pricet1<BR><BR>";
      echo "Bought " . NUMBER($goods_t1) . " units of goods at $goods_pricet1.<BR><BR>";
      $goods_t1 = -$goods_t1;
      
    }
    if($finish[port_type] == "ore")
    {
      $goods_t1 = $playerinfo[ship_goods];
      $goods_pricet1 = $goods_price + $goods_delta * $finish[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t1 > $finish[port_goods]) $goods_t1 = $finish[port_goods];

      $organics_t1 = $playerinfo[ship_organics];
      $organics_pricet1 = $organics_price + $organics_delta * $finish[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t1 > $finish[port_organics]) $organics_t1 = $finish[port_organics];
      
      $freeholds = $freeholds + $goods_t1 + $organics_t1;
      //$ore_t1 = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_colonists];
      $ore_t1 = $freeholds;
      $ore_pricet1 = $ore_price - $ore_delta * $finish[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t1 > $finish[port_ore]) $ore_t1 = $finish[port_ore];
      $freeholds = $freeholds - $ore_t1;
      
      $energy_t1 = $playerinfo[ship_energy] + $energyscooped;
      $energy_pricet1 = $energy_price + $energy_delta * $finish[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t1 > $finish[port_energy])
      {
        $energy_t1 = $finish[port_energy];
      }
      
      $t1_value = $goods_pricet1 * $goods_t1 - $ore_pricet1 * $ore_t1 + $organics_pricet1 * $organics_t1 +
        $energy_pricet1 * $energy_t1;
      if($t1_value < 0 && abs($t1_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum ore at $destination.<BR><BR>";
        $t1_value = $t1_value + $ore_pricet1 * $ore_t1;
        $ore_t1 = 0;
      }
      echo "Sold " . NUMBER($energy_t1) . " energy at $energy_pricet1<BR>";
      echo "Sold " . NUMBER($organics_t1) . " organics at $organics_pricet1<BR>";
      echo "Sold " . NUMBER($goods_t1) . " goods at $goods_pricet1<BR><BR>";
      echo "Bought " . NUMBER($ore_t1) . " units of ore at $ore_pricet1.<BR><BR>";
      $ore_t1 = -$ore_t1;
    }
    if($finish[port_type] == "organics")
    {
      $goods_t1 = $playerinfo[ship_goods];
      $goods_pricet1 = $goods_price + $goods_delta * $finish[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t1 > $finish[port_goods])
      {
        $goods_t1 = $finish[port_goods];
      }
      $ore_t1 = $playerinfo[ship_ore];
      $ore_pricet1 = $ore_price + $ore_delta * $finish[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t1 > $finish[port_ore])
      {
        $ore_t1 = $finish[port_ore];
      }
      $freeholds = $freeholds + $goods_t1 + $ore_t1;
      //$organics_t1 = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_organics] - $playerinfo[ship_colonists];
      $organics_t1 = $freeholds;
      $organics_pricet1 = $organics_price - $organics_delta * $finish[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t1 > $finish[port_organics])
      {
        $organics_t1 = $finish[port_organics];
      }
      $freeholds = $freeholds - $organics_t1;
      
      $energy_t1 = $playerinfo[ship_energy] + $energyscooped;
      $energy_pricet1 = $energy_price + $energy_delta * $finish[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t1 > $finish[port_energy])
      {
        $energy_t1 = $finish[port_energy];
      }
      
      $t1_value = $goods_pricet1 * $goods_t1 + $ore_pricet1 * $ore_t1 - $organics_pricet1 * $organics_t1 +
        $energy_pricet1 * $energy_t1;
      if($t1_value < 0 && abs($t1_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum organics at $destination.<BR><BR>";
        $t1_value = $t1_value + $organics_pricet1 * $organics_t1;
        $energy_t1 = 0;
      }
      echo "Sold " . NUMBER($ore_t1) . " ore at $ore_pricet1<BR>";
      echo "Sold " . NUMBER($energy_t1) . " energy at $energy_pricet1<BR>";
      echo "Sold " . NUMBER($goods_t1) . " goods at $goods_pricet1<BR><BR>";
      echo "Bought " . NUMBER($organics_t1) . " units of organics at $organics_pricet1.<BR><BR>";
      $organics_t1 = -$organics_t1;
    }
    echo "Total ";
    if($t1_value < 0)
    {
      echo "cost";
    }
    else
    {
      echo "profit";
    }
    echo ":  " . NUMBER(abs($t1_value)) . " credits<BR><BR>";
    $update1 = mysql_query("UPDATE ships SET ship_ore=ship_ore-$ore_t1, ship_organics=ship_organics-$organics_t1, ship_goods=ship_goods-$goods_t1, ship_energy=ship_energy-$energy_t1+$energyscooped, credits=credits+$t1_value WHERE ship_id=$playerinfo[ship_id]");
	//$freeholds = NUM_HOLDS($playerinfo[hull]) - $ore_t1 - $organics_t1 - $goods_t1 - $playerinfo[ship_colonists];
    $ore_t1 = abs($ore_t1);
    $organics_t1 = abs($organics_t1);
    $goods_t1 = abs($goods_t1);
    $energy_t1 = abs($energy_t1);
    $update2 = mysql_query("UPDATE universe SET port_ore=port_ore-$ore_t1, port_organics=port_organics-$organics_t1, port_goods=port_goods-$goods_t1, port_energy=port_energy-$energy_t1 WHERE sector_id=$destination");
    $result4 = mysql_query("SELECT * FROM ships WHERE ship_id=$playerinfo[ship_id]");
    $playerinfo = mysql_fetch_array($result4);
    echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
    echo "<TR BGCOLOR=\"$color_header\"><TD><B>Sector $playerinfo[sector]: $start[port_type]</B></TD></TR>";
    echo "</TABLE><BR>";
   
    $ore_t2 = 0;
    $organics_t2 = 0;
    $goods_t2 = 0;
    $energy_t2 = 0;
    $t2_value = 0;
    if($start[port_type] == "none" || $start[port_type] == "special")
    {
      echo "This port does not trade commodities.<BR><BR>";
    }
    if($start[port_type] == "energy")
    {
      $goods_t2 = $playerinfo[ship_goods];
      $goods_pricet2 = $goods_price + $goods_delta * $start[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t2 > $start[port_goods])
      {
        $goods_t2 = $start[port_goods];
      }
     
      $ore_t2 = $playerinfo[ship_ore];
      $ore_pricet2 = $ore_price + $ore_delta * $start[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t2 > $start[port_ore])
      {
        $ore_t2 = $start[port_ore];
      }
      
      $organics_t2 = $playerinfo[ship_organics];
      $organics_pricet2 = $organics_price + $organics_delta * $start[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t2 > $start[port_organics])
      {
        $organics_t2 = $start[port_organics];
      }
      
      $energy_t2 = $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy] - $energyscooped;
      $energy_pricet2 = $energy_price - $energy_delta * $start[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t2 > $start[port_energy]) $energy_t2 = $start[port_energy];
    
      $freebattery = NUM_ENERGY($playerinfo[power]) - $energy_t2 - $playerinfo[ship_energy] - $energyscooped;
      if ($energy_t2 >> $freebattery) $energy_t2 = $freebattery;
      $freebattery = $freebattery - $energy_t2;
      
      $freeholds = $freeholds + $goods_t2 + $ore_t2 + $organics_t2;
      
      $t2_value = $goods_pricet2 * $goods_t2 + $ore_pricet2 * $ore_t2 + $organics_pricet2 * $organics_t2 -
        $energy_pricet2 * $energy_t2;
      if($t2_value < 0 && abs($t2_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum energy at $playerinfo[sector].<BR><BR>";
        $t2_value = $t2_value + $energy_pricet2 * $energy_t2;
        $energy_t2 = 0;
      }
      echo "Sold " . NUMBER($ore_t2) . " ore at $ore_pricet2<BR>";
      echo "Sold " . NUMBER($organics_t2) . " organics at $organics_pricet2<BR>";
      echo "Sold " . NUMBER($goods_t2) . " goods at $goods_pricet2<BR><BR>";
      echo "Bought " . NUMBER($energy_t2) . " units of energy at $energy_pricet2.<BR><BR>";
      $energy_t2 = -$energy_t2;
    }
    if($start[port_type] == "goods")
    {
      
      $ore_t2 = $playerinfo[ship_ore];
      $ore_pricet2 = $ore_price + $ore_delta * $start[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t2 > $start[port_ore])
      {
        $ore_t2 = $start[port_ore];
      }
      
      $organics_t2 = $playerinfo[ship_organics];
      $organics_pricet2 = $organics_price + $organics_delta * $start[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t2 > $start[port_organics])
      {
        $organics_t2 = $start[port_organics];
      }

      $freeholds = $freeholds + $ore_t2 + $organics_t2;
      //$goods_t2 = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      $goods_t2 = $freeholds;
      $goods_pricet2 = $goods_price - $goods_delta * $start[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t2 > $start[port_goods])
      {
        $goods_t2 = $start[port_goods];
      }
      $freeholds = $freeholds - $ore_t2;

      $energy_t2 = $playerinfo[ship_energy] + $energyscooped;
      $energy_pricet2 = $energy_price + $energy_delta * $start[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t2 > $start[port_energy])
      {
        $energy_t2 = $start[port_energy];
      }
      
      $t2_value = -$goods_pricet2 * $goods_t2 + $ore_pricet2 * $ore_t2 + $organics_pricet2 * $organics_t2 +
        $energy_pricet2 * $energy_t2;
      if($t2_value < 0 && abs($t2_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum goods at $playerinfo[sector].<BR><BR>";
        $t2_value = $t2_value + $goods_pricet2 * $goods_t2;
        $goods_t2 = 0;
      }

      echo "Sold " . NUMBER($ore_t2) . " ore at $ore_pricet2<BR>";
      echo "Sold " . NUMBER($organics_t2) . " organics at $organics_pricet2<BR>";
      echo "Sold " . NUMBER($energy_t2) . " energy at $energy_pricet2<BR><BR>";
      echo "Bought " . NUMBER($goods_t2) . " units of goods at $goods_pricet2.<BR><BR>";
      $goods_t2 = -$goods_t2;
    }
    if($start[port_type] == "ore")
    {
      $goods_t2 = $playerinfo[ship_goods];
      $goods_pricet2 = $goods_price + $goods_delta * $start[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t2 > $start[port_goods]) $goods_t2 = $start[port_goods];
      
      $organics_t2 = $playerinfo[ship_organics];
      $organics_pricet2 = $organics_price + $organics_delta * $start[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t2 > $start[port_organics]) $organics_t2 = $start[port_organics];
     
      
      $freeholds = $freeholds + $goods_t2 + $organics_t2;
      //$ore_t2 = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_colonists];
      $ore_t2 = $freeholds;
      $ore_pricet2 = $ore_price - $ore_delta * $start[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t2 > $start[port_ore])  $ore_t2 = $start[port_ore];
      $freeholds = $freeholds - $ore_t2;
      
      $energy_t2 = $playerinfo[ship_energy] + $energyscooped;
      $energy_pricet2 = $energy_price + $energy_delta * $start[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t2 > $start[port_energy])$energy_t2 = $start[port_energy];
      
      $t2_value = $goods_pricet2 * $goods_t2 - $ore_pricet2 * $ore_t2 + $organics_pricet2 * $organics_t2 + $energy_pricet2 * $energy_t2;
      if($t2_value < 0 && abs($t2_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum ore at $playerinfo[sector].<BR><BR>";
        $t2_value = $t2_value + $ore_pricet2 * $ore_t2;
        $ore_t2 = 0;
      }
     
      
      echo "Sold " . NUMBER($energy_t2) . " energy at $energy_pricet2<BR>";
      echo "Sold " . NUMBER($organics_t2) . " organics at $organics_pricet2<BR>";
      echo "Sold " . NUMBER($goods_t2) . " goods at $goods_pricet2<BR><BR>";
      echo "Bought " . NUMBER($ore_t2) . " units of ore at $ore_pricet2.<BR><BR>";
      $ore_t2 = -$ore_t2;
    }
    if($start[port_type] == "organics")
    {
      $goods_t2 = $playerinfo[ship_goods];
      $goods_pricet2 = $goods_price + $goods_delta * $start[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t2 > $start[port_goods])
      {
        $goods_t2 = $start[port_goods];
      }
      
      $ore_t2 = $playerinfo[ship_ore];
      $ore_pricet2 = $ore_price + $ore_delta * $start[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t2 > $start[port_ore])
      {
        $ore_t2 = $start[port_ore];
      }
      
      $freeholds = $freeholds + $goods_t2 + $ore_t2;
      //$organics_t2 = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_organics] - $playerinfo[ship_colonists];
      $organics_t2 = $freeholds;
      $organics_pricet2 = $organics_price - $organics_delta * $start[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t2 > $start[port_organics])
      {
        $organics_t2 = $start[port_organics];
      }
      $freeholds = $freeholds - $organics_t2;
      
      $energy_t2 = $playerinfo[ship_energy] + $energyscooped;
      $energy_pricet2 = $energy_price + $energy_delta * $start[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t2 > $start[port_energy])
      {
        $energy_t2 = $start[port_energy];
      }
      
      $t2_value = $goods_pricet2 * $goods_t2 + $ore_pricet2 * $ore_t2 - $organics_pricet2 * $organics_t2 +
        $energy_pricet2 * $energy_t2;
      if($t2_value < 0 && abs($t2_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum organics at $playerinfo[sector].<BR><BR>";
        $t2_value = $t2_value + $organics_pricet2 * $organics_t2;
        $energy_t2 = 0;
      }
      
      echo "Sold " . NUMBER($ore_t2) . " ore at $ore_pricet2<BR>";
      echo "Sold " . NUMBER($energy_t2) . " energy at $energy_pricet2<BR>";
      echo "Sold " . NUMBER($goods_t2) . " goods at $goods_pricet2<BR><BR>";
      echo "Bought " . NUMBER($organics_t2) . " units of organics at $organics_pricet2.<BR><BR>";
      $organics_t2 = -$organics_t2;
    }
    echo "Total ";
    if($t2_value < 0)
    {
      echo "cost";
    }
    else
    {
      echo "profit";
    }
    echo ":  " . NUMBER(abs($t2_value)) . " credits<BR><BR>";
    echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
    echo "<TR BGCOLOR=\"$color_header\"><TD><B>Trading summary</B></TD></TR>";
    echo "</TABLE><BR>";
    $update3 = mysql_query("UPDATE ships SET turns=turns-$triptime, turns_used=turns_used+$triptime, ship_ore=ship_ore-$ore_t2, ship_organics=ship_organics-$organics_t2, ship_goods=ship_goods-$goods_t2, ship_energy=ship_energy-$energy_t2+$energyscooped, credits=credits+$t2_value WHERE ship_id=$playerinfo[ship_id]");
    $ore_t2 = abs($ore_t2);
    $organics_t2 = abs($organics_t2);
    $goods_t2 = abs($goods_t2);
    $energy_t2 = abs($energy_t2);
    $update4 = mysql_query("UPDATE universe SET port_ore=port_ore-$ore_t2, port_organics=port_organics-$organics_t2, port_goods=port_goods-$goods_t2, port_energy=port_energy-$energy_t2 WHERE sector_id=$playerinfo[sector]");
    $combined = $t1_value + $t2_value;
    echo "Total combined ";
    if($combined < 0)
    {
      echo "cost:<FONT COLOR='RED'>";
    }
    else
    {
      echo "profit:<FONT COLOR='#70A4C8'>";
    }
    echo "  " . NUMBER(abs($combined)) . "</FONT> credits<BR><BR>";
    $remaining = $playerinfo[turns]-$triptime;
    echo "Used " . NUMBER($triptime) . " turn(s). " . NUMBER($remaining) . " left.<BR><BR>";
    echo "<a href='traderoute.php3?phase=2&destination=$destination'>Do this trade route again</a><BR><BR>";
  }
}

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();
include("footer.php3");

?> 
