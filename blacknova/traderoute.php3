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

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

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
  $free_power = round((pow($level_factor, $playerinfo[power]) * 500) - $playerinfo[ship_energy]);
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
  /* inform player of what commodities are traded at ports - if they are the same, or one/both is special, or there is no port in either sector fail. */
  if($finish[port_type] == "none" || $start[port_type] == "none")
  {
    echo "There is no port in one of the sectors.<BR><BR>";
  }
  elseif($finish[port_type] == "special" || $start[port_type] == "special")
  {
    echo "One of the two ports is a special port - they do not trade commodities.<BR><BR>";
  }
  elseif($triptime > $playerinfo[turns])
  {
    echo "You do not have enough turns - you only have " . NUMBER($playerinfo[turns]) . " turns.<BR><BR>";
  }
  else
  {
    echo "This sector has a $start[port_type] port and $destination has a $finish[port_type] port.<BR><BR>";
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
  $free_power = round((pow($level_factor, $playerinfo[power]) * 500) - $playerinfo[ship_energy]);
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
  if($finish[port_type] == "none" || $start[port_type] == "none")
  {
    echo "There is no port in one of the sectors.<BR><BR>";
  }
  elseif($finish[port_type] == "special" || $start[port_type] == "special")
  {
    echo "One of the two ports is a special port - they do not trade commodities.<BR><BR>";
  }
  elseif($triptime > $playerinfo[turns])
  {
    echo "You do not have enough turns - you only have " . NUMBER($playerinfo[turns]) . " turns.<BR><BR>";
  }
  else
  {
    /* deduct turns, gain energy (w/ fuelscoop) for each leg, trade goods, gain credits */
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
        $ore_t1 = $finish[port_organics];
      }
      $energy_t1 = $free_power = round(pow($level_factor, $playerinfo[power]) * 500) - $playerinfo[ship_energy] - $energyscooped;
      $energy_pricet1 = $energy_price - $energy_delta * $finish[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t1 > $finish[port_energy])
      {
        $energy_t1 = $finish[port_energy];
      }
      $t1_value = $goods_pricet1 * $goods_t1 + $ore_pricet1 * $ore_t1 + $organics_pricet1 * $organics_t1 -
        $energy_pricet1 * $energy_t1;
      if($t1_value < 0 && abs($t1_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum energy at $destination.<BR><BR>";
        $t1_value = $t1_value + $energy_pricet1 * $energy_t1;
        $energy_t1 = 0;
      }
      echo "Sold at $destination:<BR><BR>";
      echo NUMBER($ore_t1) . " ore at $ore_pricet1<BR>";
      echo NUMBER($organics_t1) . " organics at $organics_pricet1<BR>";
      echo NUMBER($goods_t1) . " goods at $goods_pricet1<BR><BR>";
      echo "Bought " . NUMBER($energy_t1) . " units of energy at $energy_pricet1.<BR><BR>";
      echo "Total profit:  " . NUMBER($t1_value) . " credits<BR><BR>";
      $energy_t1 = -$energy_t1;
    }
    if($finish[port_type] == "goods")
    {
      $goods_t1 = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      $goods_pricet1 = $goods_price - $goods_delta * $finish[port_goods] / $goods_limit * $inventory_factor;
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
        $ore_t1 = $finish[port_organics];
      }
      $energy_t1 = $playerinfo[ship_energy] + $energyscooped;
      $energy_pricet1 = $energy_price + $energy_delta * $finish[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t1 > $finish[port_energy])
      {
        $energy_t1 = $finish[port_energy];
      }
      $t1_value = -$goods_pricet1 * $goods_t1 + $ore_pricet1 * $ore_t1 + $organics_pricet1 * $organics_t1 +
        $energy_pricet1 * $energy_t1;
      if($t1_value < 0 && abs($t1_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum goods at $destination.<BR><BR>";
        $t1_value = $t1_value + $goods_pricet1 * $goods_t1;
        $goods_t1 = 0;
      }
      echo "Sold at $destination:<BR><BR>";
      echo NUMBER($ore_t1) . " ore at $ore_pricet1<BR>";
      echo NUMBER($organics_t1) . " organics at $organics_pricet1<BR>";
      echo NUMBER($energy_t1) . " energy at $energy_pricet1<BR><BR>";
      echo "Bought " . NUMBER($goods_t1) . " units of goods at $goods_pricet1.<BR><BR>";
      echo "Total profit:  " . NUMBER($t1_value) . " credits<BR><BR>";
      $goods_t1 = -$goods_t1;
    }
    if($finish[port_type] == "ore")
    {
      $goods_t1 = $playerinfo[ship_goods];
      $goods_pricet1 = $goods_price + $goods_delta * $finish[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t1 > $finish[port_goods])
      {
        $goods_t1 = $finish[port_goods];
      }
      $ore_t1 = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_colonists];
      $ore_pricet1 = $ore_price - $ore_delta * $finish[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t1 > $finish[port_ore])
      {
        $ore_t1 = $finish[port_ore];
      }
      $organics_t1 = $playerinfo[ship_organics];
      $organics_pricet1 = $organics_price + $organics_delta * $finish[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t1 > $finish[port_organics])
      {
        $ore_t1 = $finish[port_organics];
      }
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
      echo "Sold at $destination:<BR><BR>";
      echo NUMBER($energy_t1) . " energy at $energy_pricet1<BR>";
      echo NUMBER($organics_t1) . " organics at $organics_pricet1<BR>";
      echo NUMBER($goods_t1) . " goods at $goods_pricet1<BR><BR>";
      echo "Bought " . NUMBER($ore_t1) . " units of ore at $ore_pricet1.<BR><BR>";
      echo "Total profit:  " . NUMBER($t1_value) . " credits<BR><BR>";
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
      $organics_t1 = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_organics] - $playerinfo[ship_colonists];
      $organics_pricet1 = $organics_price - $organics_delta * $finish[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t1 > $finish[port_organics])
      {
        $ore_t1 = $finish[port_organics];
      }
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
      echo "Sold at $destination:<BR><BR>";
      echo NUMBER($ore_t1) . " ore at $ore_pricet1<BR>";
      echo NUMBER($energy_t1) . " energy at $energy_pricet1<BR>";
      echo NUMBER($goods_t1) . " goods at $goods_pricet1<BR><BR>";
      echo "Bought " . NUMBER($organics_t1) . " units of organics at $organics_pricet1.<BR><BR>";
      echo "Total profit:  " . NUMBER($t1_value) . " credits<BR><BR>";
      $organics_t1 = -$organics_t1;
    }
    $update1 = mysql_query("UPDATE ships SET ship_ore=ship_ore-$ore_t1, ship_organics=ship_organics-$organics_t1, ship_goods=ship_goods-$goods_t1, ship_energy=ship_energy-$energy_t1+$energyscooped, credits=credits+$t1_value WHERE ship_id=$playerinfo[ship_id]");
    $ore_t1 = abs($ore_t1);
    $organics_t1 = abs($ore_t1);
    $goods_t1 = abs($goods_t1);
    $energy_t1 = abs($energy_t1);
    $update2 = mysql_query("UPDATE universe SET port_ore=port_ore-$ore_t1, port_organics=port_organics-$organics_t1, port_goods=port_goods-$goods_t1, port_energy=port_energy-$energy_t1 WHERE sector_id=$destination");
    $result4 = mysql_query("SELECT * FROM ships WHERE ship_id=$playerinfo[ship_id]");
    $playerinfo = mysql_fetch_array($result4);
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
        $ore_t2 = $start[port_organics];
      }
      $energy_t2 = $free_power = round(pow($level_factor, $playerinfo[power]) * 500) - $playerinfo[ship_energy] - $energyscooped;
      $energy_pricet2 = $energy_price - $energy_delta * $start[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t2 > $start[port_energy])
      {
        $energy_t2 = $start[port_energy];
      }
      $t2_value = $goods_pricet2 * $goods_t2 + $ore_pricet2 * $ore_t2 + $organics_pricet2 * $organics_t2 -
        $energy_pricet2 * $energy_t2;
      if($t2_value < 0 && abs($t2_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum energy at $playerinfo[sector].<BR><BR>";
        $t2_value = $t2_value + $energy_pricet2 * $energy_t2;
        $energy_t2 = 0;
      }
      echo "Sold at $playerinfo[sector]:<BR><BR>";
      echo NUMBER($ore_t2) . " ore at $ore_pricet2<BR>";
      echo NUMBER($organics_t2) . " organics at $organics_pricet2<BR>";
      echo NUMBER($goods_t2) . " goods at $goods_pricet2<BR><BR>";
      echo "Bought " . NUMBER($energy_t2) . " units of energy at $energy_pricet2.<BR><BR>";
      echo "Total profit:  " . NUMBER($t2_value) . " credits<BR><BR>";
      $energy_t2 = -$energy_t2;
    }
    if($start[port_type] == "goods")
    {
      $goods_t2 = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      $goods_pricet2 = $goods_price - $goods_delta * $start[port_goods] / $goods_limit * $inventory_factor;
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
        $ore_t2 = $start[port_organics];
      }
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
      echo "Sold at $playerinfo[sector]:<BR><BR>";
      echo NUMBER($ore_t2) . " ore at $ore_pricet2<BR>";
      echo NUMBER($organics_t2) . " organics at $organics_pricet2<BR>";
      echo NUMBER($energy_t2) . " energy at $energy_pricet2<BR><BR>";
      echo "Bought " . NUMBER($goods_t2) . " units of goods at $goods_pricet2.<BR><BR>";
      echo "Total profit:  " . NUMBER($t2_value) . " credits<BR><BR>";
      $goods_t2 = -$goods_t2;
    }
    if($start[port_type] == "ore")
    {
      $goods_t2 = $playerinfo[ship_goods];
      $goods_pricet2 = $goods_price + $goods_delta * $start[port_goods] / $goods_limit * $inventory_factor;
      if($goods_t2 > $start[port_goods])
      {
        $goods_t2 = $start[port_goods];
      }
      $ore_t2 = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_colonists];
      $ore_pricet2 = $ore_price - $ore_delta * $start[port_ore] / $ore_limit * $inventory_factor;
      if($ore_t2 > $start[port_ore])
      {
        $ore_t2 = $start[port_ore];
      }
      $organics_t2 = $playerinfo[ship_organics];
      $organics_pricet2 = $organics_price + $organics_delta * $start[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t2 > $start[port_organics])
      {
        $ore_t2 = $start[port_organics];
      }
      $energy_t2 = $playerinfo[ship_energy] + $energyscooped;
      $energy_pricet2 = $energy_price + $energy_delta * $start[port_energy] / $energy_limit * $inventory_factor;
      if($energy_t2 > $start[port_energy])
      {
        $energy_t2 = $start[port_energy];
      }
      $t2_value = $goods_pricet2 * $goods_t2 - $ore_pricet2 * $ore_t2 + $organics_pricet2 * $organics_t2 +
        $energy_pricet2 * $energy_t2;
      if($t2_value < 0 && abs($t2_value) > $playerinfo[credits])
      {
        echo "You do not have enough credits to buy maximum ore at $playerinfo[sector].<BR><BR>";
        $t2_value = $t2_value + $ore_pricet2 * $ore_t2;
        $ore_t2 = 0;
      }
      echo "Sold at $playerinfo[sector]:<BR><BR>";
      echo NUMBER($energy_t2) . " energy at $energy_pricet2<BR>";
      echo NUMBER($organics_t2) . " organics at $organics_pricet2<BR>";
      echo NUMBER($goods_t2) . " goods at $goods_pricet2<BR><BR>";
      echo "Bought " . NUMBER($ore_t2) . " units of ore at $ore_pricet2.<BR><BR>";
      echo "Total profit:  " . NUMBER($t2_value) . " credits<BR><BR>";
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
      $organics_t2 = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_organics] - $playerinfo[ship_colonists];
      $organics_pricet2 = $organics_price - $organics_delta * $start[port_organics] / $organics_limit * $inventory_factor;
      if($organics_t2 > $start[port_organics])
      {
        $ore_t2 = $start[port_organics];
      }
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
      echo "Sold at $playerinfo[sector]:<BR><BR>";
      echo NUMBER($ore_t2) . " ore at $ore_pricet2<BR>";
      echo NUMBER($energy_t2) . " energy at $energy_pricet2<BR>";
      echo NUMBER($goods_t2) . " goods at $goods_pricet2<BR><BR>";
      echo "Bought " . NUMBER($organics_t2) . " units of organics $organics_pricet2.<BR><BR>";
      echo "Total profit:  " . NUMBER($t2_value) . " credits<BR><BR>";
      $organics_t2 = -$organics_t2;
    }
    $update3 = mysql_query("UPDATE ships SET turns=turns-$triptime, turns_used=turns_used+$triptime, ship_ore=ship_ore-$ore_t2, ship_organics=ship_organics-$organics_t2, ship_goods=ship_goods-$goods_t2, ship_energy=ship_energy-$energy_t2+$energyscooped, credits=credits+$t2_value WHERE ship_id=$playerinfo[ship_id]");
    $ore_t2 = abs($ore_t2);
    $organics_t2 = abs($ore_t2);
    $goods_t2 = abs($goods_t2);
    $energy_t2 = abs($energy_t2);
    $update4 = mysql_query("UPDATE universe SET port_ore=port_ore-$ore_t2, port_organics=port_organics-$organics_t2, port_goods=port_goods-$goods_t2, port_energy=port_energy-$energy_t2 WHERE sector_id=$playerinfo[sector]");
    $combined = $t1_value + $t2_value;
    echo "Total combined profit:  " . NUMBER($combined) . " credits<BR><BR>";
    $remaining = $playerinfo[turns]-$triptime;
    echo "Used " . NUMBER($triptime) . " turn(s). " . NUMBER($remaining) . " left.<BR><BR>";
  }
}

echo "Click <A HREF=main.php3>here</A> to return to main menu.";
include("footer.php3");

?> 
