<?

include("config.php3");
updatecookie();

$title = "Port Trading";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = mysql_fetch_array($result2);

if($sectorinfo[port_type] != "none" && $sectorinfo[port_type] != "special")
{
  $title="Trading Commodities";
  bigtitle();

  if($sectorinfo[port_type] == "ore")
  {
    $ore_price = $ore_price - $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $sb_ore = "Selling";
  }
  else
  {
    $ore_price = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $sb_ore = "Buying";
  }
  if($sectorinfo[port_type] == "organics")
  {
    $organics_price = $organics_price - $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $sb_organics = "Selling";
  }
  else
  {
    $organics_price = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $sb_organics = "Buying";
  }
  if($sectorinfo[port_type] == "goods")
  {
    $goods_price = $goods_price - $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $sb_goods = "Selling";
  }
  else
  {
    $goods_price = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $sb_goods = "Buying";
  }
  if($sectorinfo[port_type] == "energy")
  {
    $energy_price = $energy_price - $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
    $sb_energy = "Selling";
  }
  else
  {
    $energy_price = $energy_price + $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
    $sb_energy = "Buying";
  }
  echo "<FORM ACTION=port2.php3 METHOD=POST>";
  
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>Commodity</B></TD><TD><B>Buying/Selling</B></TD><TD><B>Amount</B></TD><TD><B>Price</B></TD><TD><B>Buy/Sell</B></TD><TD><B>Cargo</B></TD></TR>";
  if($sb_ore == "Buying")
  {
    $amount = $playerinfo[ship_ore];
  }
  else
  {
    $amount = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_colonists];
  }
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Ore</TD><TD>$sb_ore</TD><TD>" . NUMBER($sectorinfo[port_ore]) . "</TD><TD>$ore_price</TD><TD><INPUT TYPE=TEXT NAME=trade_ore SIZE=10 MAXLENGTH=20 VALUE=$amount></TD><TD>" . NUMBER($playerinfo[ship_ore]) . "</TD></TR>";
  if($sb_organics == "Buying")
  {
    $amount = $playerinfo[ship_organics];
  }
  else
  {
    $amount = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_organics] - $playerinfo[ship_colonists];
  }
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Organics</TD><TD>$sb_organics</TD><TD>" . NUMBER($sectorinfo[port_organics]) . "</TD><TD>$organics_price</TD><TD><INPUT TYPE=TEXT NAME=trade_organics SIZE=10 MAXLENGTH=20 VALUE=$amount></TD><TD>" . NUMBER($playerinfo[ship_organics]) . "</TD></TR>";
  if($sb_goods == "Buying")
  {
    $amount = $playerinfo[ship_goods];
  }
  else
  {
    $amount = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
  }
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Goods</TD><TD>$sb_goods</TD><TD>" . NUMBER($sectorinfo[port_goods]) . "</TD><TD>$goods_price</TD><TD><INPUT TYPE=TEXT NAME=trade_goods SIZE=10 MAXLENGTH=20 VALUE=$amount></TD><TD>" . NUMBER($playerinfo[ship_goods]) . "</TD></TR>";
  if($sb_energy == "Buying")
  {
    $amount = $playerinfo[ship_energy];
  }
  else
  {
    $amount = round(pow($level_factor, $playerinfo[power]) * 500) - $playerinfo[ship_energy];
  }
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Energy</TD><TD>$sb_energy</TD><TD>" . NUMBER($sectorinfo[port_energy]) . "</TD><TD>$energy_price</TD><TD><INPUT TYPE=TEXT NAME=trade_energy SIZE=10 MAXLENGTH=20 VALUE=$amount></TD><TD>" . NUMBER($playerinfo[ship_energy]) . "</TD></TR>";
  echo "</TABLE><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Trade></FORM>";
  
  $free_holds = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
  $free_power = round(pow($level_factor, $playerinfo[power]) * 500) - $playerinfo[ship_energy];
  
  echo "You have " . NUMBER($free_holds) . " empty cargo holds, can carry " . NUMBER($free_power) . " more energy units, and have " . NUMBER($playerinfo[credits]) . " credits.";
}
elseif($sectorinfo[port_type] == "special")
{
  $title="Special Port";
  bigtitle();

  echo "You have " . NUMBER($playerinfo[credits]) . " credits to spend.<BR>";
  echo "If you need more you may access this port's <A HREF=\"ibank.php3\">IGB Banking Terminal</A>.<BR><BR>"; 
  echo "<FORM ACTION=port2.php3 METHOD=POST>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR BGCOLOR=\"$color_header\">";
  echo "<TD><B>Device</B></TD><TD><B>Cost</B></TD><TD><B>Current</B></TD><TD><B>Quantity</B></TD>";
  echo "<TD><B>Component Levels</B></TD><TD><B>Cost</B></TD><TD><B>Current Level</B></TD><TD><B>Upgrade?</B></TD>";
  echo "</TR>";
  $hull_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[hull]));
  $engine_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[engines]));
  $power_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[power]));
  $computer_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[computer]));
  $sensors_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[sensors]));
  $beams_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[beams]));
  $armour_upgrade_cost = $upgrade_cost * round(pow($upgrade_factor, $playerinfo[armour]));
  $cloak_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[cloak]));
  $torp_launchers_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[torp_launchers]));
  $shields_upgrade_cost=$upgrade_cost*round(pow($upgrade_factor, $playerinfo[shields]));
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD>Genesis Devices</TD><TD>" . NUMBER($dev_genesis_price) . "</TD><TD>" . NUMBER($playerinfo[dev_genesis]) . "</TD><TD><INPUT TYPE=TEXT NAME=dev_genesis_number SIZE=4 MAXLENGTH=4 VALUE=0></TD>";
  echo "<TD>Hull</TD><TD>" . NUMBER($hull_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[hull]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=hull_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>Space Beacons</TD><TD>" . NUMBER($dev_beacon_price) . "</TD><TD>" . NUMBER($playerinfo[dev_beacon]) . "</TD><TD><INPUT TYPE=TEXT NAME=dev_beacon_number SIZE=4 MAXLENGTH=4 VALUE=0></TD>";
  echo "<TD>Engines</TD><TD>" . NUMBER($engine_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[engines]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=engine_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD>Emergency Warp Devices</TD><TD>" . NUMBER($dev_emerwarp_price) . "</TD><TD>" . NUMBER($playerinfo[dev_emerwarp]) . "</TD><TD><INPUT TYPE=TEXT NAME=dev_emerwarp_number SIZE=4 MAXLENGTH=4 VALUE=0></TD>";
  echo "<TD>Power</TD><TD>" . NUMBER($power_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[power]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=power_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>Warp Editors</TD><TD>" . NUMBER($dev_warpedit_price) . "</TD><TD>" . NUMBER($playerinfo[dev_warpedit]) . "</TD><TD><INPUT TYPE=TEXT NAME=dev_warpedit_number SIZE=4 MAXLENGTH=4 VALUE=0></TD>";
  echo "<TD>Computer</TD><TD>" . NUMBER($computer_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[computer]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=computer_upgrade value=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD></TD><TD></TD><TD></TD><TD></TD>";
  echo "<TD>Sensors</TD><TD>" . NUMBER($sensors_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[sensors]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=sensors_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>Mine Deflector</TD><TD>" . NUMBER($dev_minedeflector_price) . "</TD><TD>" . NUMBER($playerinfo[dev_minedeflector]) . "</TD><TD><INPUT TYPE=TEXT NAME=dev_minedeflector_number SIZE=4 MAXLENGTH=4 VALUE=0></TD>";
  echo "<TD>Beam Weapons</TD><TD>" . NUMBER($beams_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[beams]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=beams_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD>Escape Pod</TD><TD>" . NUMBER($dev_escapepod_price) . "</TD>";
  if($playerinfo[dev_escapepod] == "N") 
  {
    echo "<TD>None</TD><TD><INPUT TYPE=CHECKBOX NAME=escapepod_purchase value=1></TD>";
  }
  else
  {
    echo "<TD>Equipped</TD><TD>n/a</TD>";
  }
  echo "<TD>Armour</TD><TD>" . NUMBER($armour_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[armour]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=armour_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>Fuel Scoop</TD><TD>" . NUMBER($dev_fuelscoop_price) . "</TD>";
  if($playerinfo[dev_fuelscoop] == "N") 
  {
    echo "<TD>None</TD><TD><INPUT TYPE=CHECKBOX NAME=fuelscoop_purchase value=1></TD>";
  }
  else
  {
    echo "<TD>Equipped</TD><TD>n/a</TD>";
  }
  echo "<TD>Cloak</TD><TD>" . NUMBER($cloak_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[cloak]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=cloak_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD></TD><TD></TD><TD></TD><TD></TD><TD>Torpedo Launchers</TD><TD>" . NUMBER($torp_launchers_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[torp_launchers]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=torp_launchers_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD></TD><TD></TD><TD></TD><TD></TD><TD>Shields</TD><TD>".NUMBER($shields_upgrade_cost)."</TD><TD>" . NUMBER($playerinfo[shields]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=shields_upgrade VALUE=1></TD>";
  echo "</TR>";
  echo "</TABLE>";
  echo "<BR>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>Item</B></TD><TD><B>Cost</B></TD><TD><B>Current</B></TD><TD><B>Max</B></TD><TD><B>Quantity</B></TD><TD><B>Item</B></TD><TD><B>Cost</B></TD><TD><B>Current</B></TD><TD><B>Max</B></TD><TD><B>Quantity</B></TD></TR>";
  $fighter_max = round(pow($level_factor, $playerinfo[computer]) * 100);
  $fighter_free = $fighter_max - $playerinfo[ship_fighters];
  $torpedo_max = round(pow($level_factor, $playerinfo[torp_launchers]) * 100);
  $torpedo_free = $torpedo_max - $playerinfo[torps];
  $armour_max = round(pow($level_factor, $playerinfo[armour]) * 100);
  $armour_free = $armour_max - $playerinfo[armour_pts];
  $colonist_max = round(pow($level_factor, $playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD>Fighters</TD><TD>" . NUMBER($fighter_price) . "</TD><TD>" . NUMBER($playerinfo[ship_fighters]) . " / " . NUMBER($fighter_max) . "</TD><TD>" . NUMBER($fighter_free) . "</TD>";
  echo "<TD>";
  if($playerinfo[ship_fighters] != $fighter_max)
  {
    echo "<INPUT TYPE=TEXT NAME=fighter_number SIZE=6 MAXLENGTH=10 VALUE=0>";
  }
  else
  {
    echo "Full";
  }
  echo "</TD>";
  echo "<TD>Torpedoes</TD><TD>" . NUMBER($torpedo_price) . "</TD><TD>" . NUMBER($playerinfo[torps]) . " / " . NUMBER($torpedo_max) . "</TD><TD>" . NUMBER($torpedo_free) . "</TD>";
  echo "<TD>";
  if($playerinfo[torps] != $torpedo_max)
  {
    echo "<INPUT TYPE=TEXT NAME=torpedo_number SIZE=6 MAXLENGTH=10 VALUE=0>";
  }
  else
  {
    echo "Full";
  }
  echo "</TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>Armour Points</TD><TD>" . NUMBER($armour_price) . "</TD><TD>" . NUMBER($playerinfo[armour_pts]) . " / " . NUMBER($armour_max) . "</TD><TD>" . NUMBER($armour_free) . "</TD>";
  echo "<TD>";
  if($playerinfo[armour_pts] != $armour_max)
  {
    echo "<INPUT TYPE=TEXT NAME=armour_number SIZE=6 MAXLENGTH=10 VALUE=0>";
  }
  else
  {
    echo "Full";
  }
  echo "</TD>";
  echo "<TD>Colonists</TD><TD>" . NUMBER($colonist_price) . "</TD><TD>" . NUMBER($playerinfo[ship_colonists]) . "</TD><TD>" . NUMBER($colonist_max) . "</TD>";
  echo "<TD>";
  if($colonist_max)
  {
    echo "<INPUT TYPE=TEXT NAME=colonist_number SIZE=6 MAXLENGTH=10 VALUE=0>";
  }
  else
  {
    echo "Full";
  }
  echo "</TD>";
  echo "</TR>";
  echo "</TABLE><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Buy>";
  echo "</FORM>";
  echo "If you would like to dump all your colonists here, click <A HREF=dump.php3>here</A>.";
}
else
{
  echo "There is no port here!";
}

echo "<BR><BR>Click <A HREF=main.php3>here</A> to return to main menu without trading.";

include("footer.php3");

?>
