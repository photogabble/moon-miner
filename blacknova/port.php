<?
include("config.php");
updatecookie();


include_once($gameroot . "/languages/$lang");
$title = $l_title_port;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

// fix negative quantities, i guess theres a better way to do but i'm in a hurry
// i dont know how the quantities acutally get negative ...

if ($playerinfo[ship_ore]<0)
		{
        $fixres = $db->Execute("UPDATE $dbtables[ships] set ship_ore=0 WHERE email='$username'");
        $playerinfo[ship_ore] = 0;
        }

if ($playerinfo[ship_organics]<0)
		{
        $fixres = $db->Execute("UPDATE $dbtables[ships] set ship_organics=0 WHERE email='$username'");
        $playerinfo[ship_organics] = 0;
        }

if ($playerinfo[ship_energy]<0)
		{
        $fixres = $db->Execute("UPDATE $dbtables[ships] set ship_energy=0 WHERE email='$username'");
        $playerinfo[ship_energy] = 0;
        }

if ($playerinfo[ship_goods]<0)
		{
        $fixres = $db->Execute("UPDATE $dbtables[ships] set ship_goods=0 WHERE email='$username'");
        $playerinfo[ship_goods] = 0;
        }



$res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[zones] WHERE zone_id=$sectorinfo[zone_id]");

$zoneinfo = $res->fields;

if($zoneinfo[zone_id] == 4)
{
  $title=$l_sector_war;
  bigtitle();
  echo "$l_war_info <p>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}
elseif($zoneinfo[allow_trade] == 'N')
{
  $title="Trade forbidden";
  bigtitle();
  echo "$l_no_trade_info<p>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

elseif($zoneinfo[allow_trade] == 'L')
{
  if($zoneinfo[corp_zone] == 'N')
  {
    $res = $db->Execute("SELECT team FROM $dbtables[ships] WHERE ship_id=$zoneinfo[owner]");
    $ownerinfo = $res->fields;

    if($playerinfo[ship_id] != $zoneinfo[owner] && $playerinfo[team] == 0 || $playerinfo[team] != $ownerinfo[team])
    {
      $title="Trade forbidden";
      bigtitle();
      echo "Trading at this port is not allowed for outsiders<p>";
      TEXT_GOTOMAIN();
      include("footer.php");
      die();
    }
  }
  else
  {
    if($playerinfo[team] != $zoneinfo[owner])
    {
      $title=$l_no_trade;
      bigtitle();
      echo "$l_no_trade_out<p>";
      TEXT_GOTOMAIN();
      include("footer.php");
      die();
    }
  }
}

//-------------------------------------------------------------------------------------------------

if($sectorinfo[port_type] != "none" && $sectorinfo[port_type] != "special")
{
  $title=$l_title_trade;
  bigtitle();

  if($sectorinfo[port_type] == "ore")
  {
    $ore_price = $ore_price - $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $sb_ore = $l_selling;
  }
  else
  {
    $ore_price = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $sb_ore = $l_buying;
  }
  if($sectorinfo[port_type] == "organics")
  {
    $organics_price = $organics_price - $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $sb_organics = $l_selling;
  }
  else
  {
    $organics_price = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $sb_organics = $l_buying;
  }
  if($sectorinfo[port_type] == "goods")
  {
    $goods_price = $goods_price - $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $sb_goods = $l_selling;
  }
  else
  {
    $goods_price = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $sb_goods = $l_buying;
  }
  if($sectorinfo[port_type] == "energy")
  {
    $energy_price = $energy_price - $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
    $sb_energy = $l_selling;
  }
  else
  {
    $energy_price = $energy_price + $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
    $sb_energy = $l_buying;
  }
  // establish default amounts for each commodity
  if($sb_ore == $l_buying)
  {
    $amount_ore = $playerinfo[ship_ore];
  }
  else
  {
    $amount_ore = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_colonists];
  }

  if($sb_organics == $l_buying)
  {
    $amount_organics = $playerinfo[ship_organics];
  }
  else
  {
    $amount_organics = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_organics] - $playerinfo[ship_colonists];
  }

  if($sb_goods == $l_buying)
  {
    $amount_goods = $playerinfo[ship_goods];
  }
  else
  {
    $amount_goods = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
  }

  if($sb_energy == $l_buying)
  {
    $amount_energy = $playerinfo[ship_energy];
  }
  else
  {
    $amount_energy = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
  }

  // limit amounts to port quantities
  $amount_ore = min($amount_ore, $sectorinfo[port_ore]);
  $amount_organics = min($amount_organics, $sectorinfo[port_organics]);
  $amount_goods = min($amount_goods, $sectorinfo[port_goods]);
  $amount_energy = min($amount_energy, $sectorinfo[port_energy]);

  // limit amounts to what the player can afford
  if($sb_ore == $l_selling)
  {
    $amount_ore = min($amount_ore, floor(($playerinfo[credits] + $amount_organics * $organics_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $ore_price));
  }
  if($sb_organics == $l_selling)
  {
    $amount_organics = min($amount_organics, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $organics_price));
  }
  if($sb_goods == $l_selling)
  {
    $amount_goods = min($amount_goods, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_energy * $energy_price) / $goods_price));
  }
  if($sb_energy == $l_selling)
  {
    $amount_energy = min($amount_energy, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_goods * $goods_price) / $energy_price));
  }

  echo "<FORM ACTION=port2.php METHOD=POST>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_commodity</B></TD><TD><B>$l_buying/$l_selling</B></TD><TD><B>$l_amount</B></TD><TD><B>$l_price</B></TD><TD><B>$l_buy/$l_sell</B></TD><TD><B>$l_cargo</B></TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_ore</TD><TD>$sb_ore</TD><TD>" . NUMBER($sectorinfo[port_ore]) . "</TD><TD>$ore_price</TD><TD><INPUT TYPE=TEXT NAME=trade_ore SIZE=10 MAXLENGTH=20 VALUE=$amount_ore></TD><TD>" . NUMBER($playerinfo[ship_ore]) . "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_organics</TD><TD>$sb_organics</TD><TD>" . NUMBER($sectorinfo[port_organics]) . "</TD><TD>$organics_price</TD><TD><INPUT TYPE=TEXT NAME=trade_organics SIZE=10 MAXLENGTH=20 VALUE=$amount_organics></TD><TD>" . NUMBER($playerinfo[ship_organics]) . "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_goods</TD><TD>$sb_goods</TD><TD>" . NUMBER($sectorinfo[port_goods]) . "</TD><TD>$goods_price</TD><TD><INPUT TYPE=TEXT NAME=trade_goods SIZE=10 MAXLENGTH=20 VALUE=$amount_goods></TD><TD>" . NUMBER($playerinfo[ship_goods]) . "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_energy</TD><TD>$sb_energy</TD><TD>" . NUMBER($sectorinfo[port_energy]) . "</TD><TD>$energy_price</TD><TD><INPUT TYPE=TEXT NAME=trade_energy SIZE=10 MAXLENGTH=20 VALUE=$amount_energy></TD><TD>" . NUMBER($playerinfo[ship_energy]) . "</TD></TR>";
  echo "</TABLE><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=$l_trade>";
  echo "</FORM>";

  $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];

 $l_trade_st_info=str_replace("[free_holds]",NUMBER($free_holds),$l_trade_st_info);
 $l_trade_st_info=str_replace("[free_power]",NUMBER($free_power),$l_trade_st_info);
 $l_trade_st_info=str_replace("[credits]",NUMBER($playerinfo[credits]),$l_trade_st_info);

 echo $l_trade_st_info;

}
elseif($sectorinfo[port_type] == "special")
{
  $title=$l_special_port;
  bigtitle();

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
  $fighter_max = NUM_FIGHTERS($playerinfo[computer]);
  $fighter_free = $fighter_max - $playerinfo[ship_fighters];
  $torpedo_max = NUM_TORPEDOES($playerinfo[torp_launchers]);
  $torpedo_free = $torpedo_max - $playerinfo[torps];
  $armour_max = NUM_ARMOUR($playerinfo[armour]);
  $armour_free = $armour_max - $playerinfo[armour_pts];
  $colonist_max = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] -
    $playerinfo[ship_goods] - $playerinfo[ship_colonists];

  TEXT_JAVASCRIPT_BEGIN();
  echo "function countTotal(form)\n";
  echo "{\n";
  echo "  form.total_cost.value = form.dev_genesis_number.value * $dev_genesis_price";
  echo " + form.dev_beacon_number.value * $dev_beacon_price";
  if($max_emerwarp - $playerinfo[dev_emerwarp] > 0)
  {
    echo " + form.dev_emerwarp_number.value * $dev_emerwarp_price";
  }
  echo " + form.dev_warpedit_number.value * $dev_warpedit_price";
  echo " + form.dev_minedeflector_number.value * $dev_minedeflector_price";
  if($playerinfo[dev_escapepod] == 'N')
  {
    echo " + (form.escapepod_purchase.checked ?  $dev_escapepod_price : 0)";
  }
  if($playerinfo[dev_fuelscoop] == 'N')
  {
    echo " + (form.fuelscoop_purchase.checked ?  $dev_fuelscoop_price : 0)";
  }
  echo " + (form.hull_upgrade.checked ? $hull_upgrade_cost : 0)";
  echo " + (form.engine_upgrade.checked ? $engine_upgrade_cost : 0)";
  echo " + (form.power_upgrade.checked ? $power_upgrade_cost : 0)";
  echo " + (form.computer_upgrade.checked ? $computer_upgrade_cost : 0)";
  echo " + (form.sensors_upgrade.checked ? $sensors_upgrade_cost : 0)";
  echo " + (form.beams_upgrade.checked ? $beams_upgrade_cost : 0)";
  echo " + (form.armour_upgrade.checked ? $armour_upgrade_cost : 0)";
  echo " + (form.cloak_upgrade.checked ? $cloak_upgrade_cost : 0)";
  echo " + (form.torp_launchers_upgrade.checked ? $torp_launchers_upgrade_cost : 0)";
  echo " + (form.shields_upgrade.checked ? $shields_upgrade_cost : 0)";
  if($playerinfo[ship_fighters] != $fighter_max)
  {
    echo " + form.fighter_number.value * $fighter_price";
  }
  if($playerinfo[torps] != $torpedo_max)
  {
    echo " + form.torpedo_number.value * $torpedo_price";
  }
  if($playerinfo[armour_pts] != $armour_max)
  {
    echo " + form.armour_number.value * $armour_price";
  }
  if($colonist_max)
  {
    echo " + form.colonist_number.value * $colonist_price";
  }
  echo ";\n";
  echo "  if(form.total_cost.value > $playerinfo[credits])\n";
  echo "  {\n";
  echo "    form.total_cost.value = '$l_no_credits';\n";
  echo "  }\n";
  echo "  form.total_cost.size = form.total_cost.value.length;\n";
  echo "}";
  TEXT_JAVASCRIPT_END();

  $onchange = "ONCHANGE=\"countTotal(this.form)\"";
  $onclick =  "ONCLICK=\"countTotal(this.form)\"";

   $l_creds_to_spend=str_replace("[credits]",NUMBER($playerinfo[credits]),$l_creds_to_spend);
  echo "$l_creds_to_spend<BR>";
  if($allow_ibank)
  {
    $igblink = "<A HREF=IGB.php>$l_igb_term</a>";
    $l_ifyouneedmore=str_replace("[igb]",$igblink,$l_ifyouneedmore);

    echo "$l_ifyouneedmore<BR>";
  }
  echo "<BR>";
  echo "<FORM ACTION=port2.php METHOD=POST>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR BGCOLOR=\"$color_header\">";
  echo "<TD><B>$l_device</B></TD><TD><B>$l_cost</B></TD><TD><B>$l_current</B></TD><TD><B>$l_max</B></TD><TD><B>$l_qty</B></TD>";
  echo "<TD><B>$l_ship_levels</B></TD><TD><B>$l_cost</B></TD><TD><B>$l_current</B></TD><TD><B>$l_Upgrade</B></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD>$l_genesis</TD><TD>" . NUMBER($dev_genesis_price) . "</TD><TD>" . NUMBER($playerinfo[dev_genesis]) . "</TD><TD>$l_unlimited</TD><TD><INPUT TYPE=TEXT NAME=dev_genesis_number SIZE=4 MAXLENGTH=4 VALUE=0 $onchange></TD>";
  echo "<TD>$l_hull</TD><TD>" . NUMBER($hull_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[hull]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=hull_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>$l_beacons</TD><TD>" . NUMBER($dev_beacon_price) . "</TD><TD>" . NUMBER($playerinfo[dev_beacon]) . "</TD><TD>$l_unlimited</TD><TD><INPUT TYPE=TEXT NAME=dev_beacon_number SIZE=4 MAXLENGTH=4 VALUE=0 $onchange></TD>";
  echo "<TD>$l_engines</TD><TD>" . NUMBER($engine_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[engines]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=engine_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD>$l_ewd</TD><TD>" . NUMBER($dev_emerwarp_price) . "</TD><TD>" . NUMBER($playerinfo[dev_emerwarp]) . "</TD><TD>";
  $avail_emerwarp = $max_emerwarp - $playerinfo[dev_emerwarp];
  if($avail_emerwarp > 0)
  {
    echo NUMBER($avail_emerwarp) . "</TD><TD><INPUT TYPE=TEXT NAME=dev_emerwarp_number SIZE=4 MAXLENGTH=4 VALUE=0 $onchange>";
  }
  else
  {
    echo "0</TD><TD>$l_full</TD>";
  }
  echo "</TD>";
  echo "<TD>$l_power</TD><TD>" . NUMBER($power_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[power]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=power_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>$l_warpedit</TD><TD>" . NUMBER($dev_warpedit_price) . "</TD><TD>" . NUMBER($playerinfo[dev_warpedit]) . "</TD><TD>$l_unlimited</TD><TD><INPUT TYPE=TEXT NAME=dev_warpedit_number SIZE=4 MAXLENGTH=4 VALUE=0 $onchange></TD>";
  echo "<TD>$l_computer</TD><TD>" . NUMBER($computer_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[computer]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=computer_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD></TD><TD></TD><TD></TD><TD></TD><TD></TD>";
  echo "<TD>$l_sensors</TD><TD>" . NUMBER($sensors_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[sensors]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=sensors_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>$l_deflect</TD><TD>" . NUMBER($dev_minedeflector_price) . "</TD><TD>" . NUMBER($playerinfo[dev_minedeflector]) . "</TD><TD>$l_unlimited</TD><TD><INPUT TYPE=TEXT NAME=dev_minedeflector_number SIZE=4 MAXLENGTH=10 VALUE=0 $onchange></TD>";
  echo "<TD>$l_beams</TD><TD>" . NUMBER($beams_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[beams]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=beams_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD>$l_escape_pod</TD><TD>" . NUMBER($dev_escapepod_price) . "</TD>";
  if($playerinfo[dev_escapepod] == "N")
  {
    echo "<TD>$l_none</TD><TD></TD><TD><INPUT TYPE=CHECKBOX NAME=escapepod_purchase VALUE=1 $onchange $onclick></TD>";
  }
  else
  {
    echo "<TD>$l_equipped</TD><TD></TD><TD>$l_n_a</TD>";
  }
  echo "<TD>$l_armour</TD><TD>" . NUMBER($armour_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[armour]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=armour_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>$l_fuel_scoop</TD><TD>" . NUMBER($dev_fuelscoop_price) . "</TD>";
  if($playerinfo[dev_fuelscoop] == "N")
  {
    echo "<TD>$l_none</TD><TD></TD><TD><INPUT TYPE=CHECKBOX NAME=fuelscoop_purchase VALUE=1 $onchange $onclick></TD>";
  }
  else
  {
    echo "<TD>$l_equipped</TD><TD></TD><TD>$l_n_a</TD>";
  }
  echo "<TD>$l_cloak</TD><TD>" . NUMBER($cloak_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[cloak]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=cloak_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD></TD><TD></TD><TD></TD><TD></TD><TD></TD><TD>$l_torp_launch</TD><TD>" . NUMBER($torp_launchers_upgrade_cost) . "</TD><TD>" . NUMBER($playerinfo[torp_launchers]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=torp_launchers_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD></TD><TD></TD><TD></TD><TD></TD><TD></TD><TD>$l_shields</TD><TD>".NUMBER($shields_upgrade_cost)."</TD><TD>" . NUMBER($playerinfo[shields]) . "</TD><TD><INPUT TYPE=CHECKBOX NAME=shields_upgrade VALUE=1 $onchange $onclick></TD>";
  echo "</TR>";
  echo "</TABLE>";
  echo "<BR>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_item</B></TD><TD><B>$l_cost</B></TD><TD><B>$l_current</B></TD><TD><B>$l_max</B></TD><TD><B>$l_qty</B></TD><TD><B>$l_item</B></TD><TD><B>$l_cost</B></TD><TD><B>$l_current</B></TD><TD><B>$l_max</B></TD><TD><B>$l_qty</B></TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\">";
  echo "<TD>$l_fighters</TD><TD>" . NUMBER($fighter_price) . "</TD><TD>" . NUMBER($playerinfo[ship_fighters]) . " / " . NUMBER($fighter_max) . "</TD><TD>" . NUMBER($fighter_free) . "</TD>";
  echo "<TD>";
  if($playerinfo[ship_fighters] != $fighter_max)
  {
    echo "<INPUT TYPE=TEXT NAME=fighter_number SIZE=6 MAXLENGTH=10 VALUE=0 $onchange>";
  }
  else
  {
    echo "$l_full";
  }
  echo "</TD>";
  echo "<TD>$l_torps</TD><TD>" . NUMBER($torpedo_price) . "</TD><TD>" . NUMBER($playerinfo[torps]) . " / " . NUMBER($torpedo_max) . "</TD><TD>" . NUMBER($torpedo_free) . "</TD>";
  echo "<TD>";
  if($playerinfo[torps] != $torpedo_max)
  {
    echo "<INPUT TYPE=TEXT NAME=torpedo_number SIZE=6 MAXLENGTH=10 VALUE=0 $onchange>";
  }
  else
  {
    echo "$l_full";
  }
  echo "</TD>";
  echo "</TR>";
  echo "<TR BGCOLOR=\"$color_line2\">";
  echo "<TD>$l_armourpts</TD><TD>" . NUMBER($armour_price) . "</TD><TD>" . NUMBER($playerinfo[armour_pts]) . " / " . NUMBER($armour_max) . "</TD><TD>" . NUMBER($armour_free) . "</TD>";
  echo "<TD>";
  if($playerinfo[armour_pts] != $armour_max)
  {
    echo "<INPUT TYPE=TEXT NAME=armour_number SIZE=6 MAXLENGTH=10 VALUE=0 $onchange>";
  }
  else
  {
    echo "$l_full";
  }
  echo "</TD>";
  echo "<TD>$l_colonists</TD><TD>" . NUMBER($colonist_price) . "</TD><TD>" . NUMBER($playerinfo[ship_colonists]) . "</TD><TD>" . NUMBER($colonist_max) . "</TD>";
  echo "<TD>";
  if($colonist_max)
  {
    echo "<INPUT TYPE=TEXT NAME=colonist_number SIZE=6 MAXLENGTH=10 VALUE=0 $onchange>";
  }
  else
  {
    echo "$l_full";
  }
  echo "</TD>";
  echo "</TR>";
  echo "</TABLE><BR>";
  echo "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR><TD><INPUT TYPE=SUBMIT VALUE=$l_buy></TD>";
  echo "<TD ALIGN=RIGHT>";
  TEXT_JAVASCRIPT_BEGIN();
  echo "document.write('$l_totalcost: <INPUT TYPE=TEXT NAME=total_cost SIZE=10 VALUE=0>');";
  TEXT_JAVASCRIPT_END();
  echo "</TD></TR>";
  echo "</TABLE>";
  echo "</FORM>";
  echo "$l_would_dump <A HREF=dump.php>$l_here</A>.";
}
else
{
  echo "$l_noport!";
}

echo "<BR><BR>";

TEXT_GOTOMAIN();

include("footer.php");

?>
