<?

include("config.php3");
$title="System Update";

include("header.php3");
connectdb();

bigtitle();

function QUERYOK($res)
{
  if($res)
  {
    echo " ok.<BR>";
  }
  else
  {
    die(" FAILED.");
  }
}

if($swordfish != $adminpass) 
{
  echo "<FORM ACTION=sysupdate.php3 METHOD=POST>";
  echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Submit><INPUT TYPE=RESET VALUE=Reset>";
  echo "</FORM>";
}
else
{
  srand((double)microtime() * 1000000);
  
  // add turns
  echo "<B>TURNS</B><BR><BR>";
  echo "Adding turns...";
  QUERYOK(mysql_query("UPDATE ships SET turns=turns+1 WHERE turns<$max_turns"));
  echo "Ensuring minimum turns are 0...";
  QUERYOK(mysql_query("UPDATE ships SET turns=0 WHERE turns<0"));
  echo "Ensuring maximum turns are $max_turns...";
  QUERYOK(mysql_query("UPDATE ships SET turns=$max_turns WHERE turns>$max_turns"));
  echo "<BR>";
  
  // add commodities to ports
  echo "<B>PORTS</B><BR><BR>";
  echo "Adding ore to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=port_ore+$ore_rate WHERE port_type='ore' AND port_ore<$ore_limit"));
  echo "Adding ore to all ore ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=port_ore+$ore_rate WHERE port_type!='special' AND port_type!='none' AND port_ore<$ore_limit"));
  echo "Ensuring minimum ore levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=0 WHERE port_ore<0"));
  echo "<BR>";
  echo "Adding organics to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=port_organics+$organics_rate WHERE port_type='organics' AND port_organics<$organics_limit"));
  echo "Adding organics to all organics ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=port_organics+$organics_rate WHERE port_type!='special' AND port_type!='none' AND port_organics<$organics_limit"));
  echo "Ensuring minimum organics levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=0 WHERE port_organics<0"));
  echo "<BR>";
  echo "Adding goods to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=port_goods+$goods_rate WHERE port_type='goods' AND port_goods<$goods_limit"));
  echo "Adding goods to all goods ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=port_goods+$goods_rate WHERE port_type!='special' AND port_type!='none' AND port_goods<$goods_limit"));
  echo "Ensuring minimum goods levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=0 WHERE port_goods<0"));
  echo "<BR>";
  echo "Adding energy to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=port_energy+$energy_rate WHERE port_type='energy' AND port_energy<$energy_limit"));
  echo "Adding energy to all energy ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=port_energy+$energy_rate WHERE port_type!='special' AND port_type!='none' AND port_energy<$energy_limit"));
  echo "Ensuring minimum energy levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=0 WHERE port_energy<0"));
  echo "<BR>";
  
  // update planet production
  echo "<B>PLANETS</B><BR><BR>";
  $res = mysql_query("SELECT sector_id, planet_colonists, planet_owner, planet_ore, planet_organics, planet_goods, planet_energy, planet_fighters, base_torp FROM universe WHERE planet='Y'");
  while($row = mysql_fetch_array($res))
  {
    $production = min($row[planet_colonists], $colonist_limit) * $colonist_production_rate;

    $organics_production = $production * $organics_prate;
    if(($row[planet_organics] + $organics_production) > $organics_limit)
    {
      $organics_production = 0;
    }
    
    $ore_production = $production * $ore_prate;
    if(($row[planet_ore] + $ore_production) > $ore_limit)
    {
      $ore_production = 0;
    }
    
    $goods_production = $production * $goods_prate;
    if(($row[planet_goods] + $goods_production) > $goods_limit)
    {
      $goods_production = 0;
    }

    $energy_production = $production * $energy_prate;
    if(($row[planet_energy] + $energy_production) > $energy_limit)
    {
      $energy_production = 0;
    }

    $reproduction = round($row[planet_colonists] * $colonist_reproduction_rate);
    if(($row[planet_colonists] + $reproduction) > $colonist_limit)
    {
      $reproduction = 0;
    }

    if($row[planet_owner])
    {
      $fighter_production = $production * $fighter_prate;
      $torp_production = $production * $torpedo_prate;
      echo "$torp_production - $fighter_production";
    }
    else
    {
      $fighter_production = 0;
      $torp_production = 0;
    }
    
    $query = "UPDATE universe SET planet_organics=planet_organics+$organics_production, planet_ore=planet_ore+$ore_production, planet_goods=planet_goods+$goods_production, planet_energy=planet_energy+$energy_production, planet_colonists=planet_colonists+$reproduction, base_torp=base_torp+$torp_production, planet_fighters=planet_fighters+$fighter_production, planet_credits=planet_credits*$interest_rate WHERE sector_id=$row[sector_id]";
    if($row[planet_colonists] > $colonist_limit)
    {
      $query = "UPDATE universe SET planet_organics=planet_organics+$organics_production, planet_ore=planet_ore+$ore_production, planet_goods=planet_goods+$goods_production, planet_energy=planet_energy+$energy_production, planet_colonists=$colonist_limit, base_torp=base_torp+$torp_production, planet_fighters=planet_fighters+$fighter_production WHERE sector_id=$row[sector_id]";
    }
    $update_planet = mysql_query("$query");
    echo "<BR>$query<BR>";
  }
  mysql_free_result($res);
  echo "Planets updated.<BR><BR>";
  echo "<BR>";
  
  // update planet production
  echo "<B>ZONES</B><BR><BR>";
  echo "Towing bigger players out of restricted zones...";
  $num_to_tow = 0;
  do
  {
    $res = mysql_query("SELECT ship_id,character_name,hull,sector,universe.zone_id,max_hull FROM ships,universe,zones WHERE sector=sector_id AND universe.zone_id=zones.zone_id AND max_hull<>0 AND ships.hull>max_hull");
    $num_to_tow = mysql_num_rows($res);
    echo "<BR>$num_to_tow players to tow:<BR>";
    while($row = mysql_fetch_array($res))
    {
      echo "...towing $row[character_name] out of $row[sector] (max_hull=$row[max_hull] hull=$row[hull])...";
      $newsector = rand(0, $sector_max);
      echo " to sector $newsector.<BR>";
      $query = mysql_query("UPDATE ships SET sector=$newsector where ship_id=$row[ship_id]");
      playerlog($row[ship_id], "Your ship was towed from sector $row[sector] to sector $newsector because your hull size exceeded $row[max_hull].");
    }
    mysql_free_result($res);
  } while($num_to_tow);
}

include("footer.php3");

?> 
