<?

include("config.php3");
updatecookie();

$max_rank = 100;

$title="Top $max_rank Players";
include("header.php3");

connectdb();

bigtitle();

$calc_hull = "ROUND(POW($upgrade_factor,hull-2))";
$calc_engines = "ROUND(POW($upgrade_factor,engines-2))";
$calc_power = "ROUND(POW($upgrade_factor,power-2))";
$calc_computer = "ROUND(POW($upgrade_factor,computer-2))";
$calc_sensors = "ROUND(POW($upgrade_factor,sensors-2))";
$calc_beams = "ROUND(POW($upgrade_factor,beams-2))";
$calc_torp_launchers = "ROUND(POW($upgrade_factor,torp_launchers-2))";
$calc_shields = "ROUND(POW($upgrade_factor,shields-2))";
$calc_armour = "ROUND(POW($upgrade_factor,armour-2))";
$calc_cloak = "ROUND(POW($upgrade_factor,cloak-2))";
$calc_levels = "($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak)*$upgrade_cost";

$calc_torps = "torps*$torpedo_price";
$calc_armour_pts = "armour_pts*$armour_price";
$calc_ship_ore = "ship_ore*$ore_price";
$calc_ship_organics = "ship_organics*$organics_price";
$calc_ship_goods = "ship_goods*$goods_price";
$calc_ship_energy = "ship_energy*$energy_price";
$calc_ship_colonists = "ship_colonists*$colonist_price";
$calc_ship_fighters = "ship_fighters*$fighter_price";
$calc_equip = "$calc_torps+$calc_armour_pts+$calc_ship_ore+$calc_ship_organics+$calc_ship_goods+$calc_ship_energy+$calc_ship_colonists+$calc_ship_fighters";

$calc_dev_warpedit = "dev_warpedit*$dev_warpedit_price";
$calc_dev_genesis = "dev_genesis*$dev_genesis_price";
$calc_dev_beacon = "dev_beacon*$dev_beacon_price";
$calc_dev_emerwarp = "dev_emerwarp*$dev_emerwarp_price";
$calc_dev_escapepod = "IF(dev_escapepod='Y', $dev_escapepod_price, 0)";
$calc_dev_fuelscoop = "IF(dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
$calc_dev_minedeflector = "dev_minedeflector*$dev_minedeflector_price";
$calc_dev = "$calc_dev_warpedit+$calc_dev_genesis+$calc_dev_beacon+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_fuelscoop+$calc_dev_minedeflector";
// planet totals are not accounted for right now...

$calc_planet_goods = "SUM(planet_organics)*$organics_price+SUM(planet_ore)*$ore_price+SUM(planet_goods)*$goods_price+SUM(planet_energy)*$energy_price";
$calc_planet_colonists = "SUM(planet_colonists)*$colonist_price";
$calc_planet_defence = "SUM(planet_fighters)*$fighter_price+IF(base='Y', $base_credits+SUM(base_torp)*$torpedo_price, 0)";
$calc_planet_credits = "SUM(planet_credits)";

$res = mysql_query("SELECT character_name,ship_name,ROUND(SQRT($calc_levels+$calc_equip+$calc_dev+credits+$calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits)) AS score FROM ships LEFT JOIN universe ON planet_owner=ship_id WHERE ship_destroyed='N' GROUP BY ship_id ORDER BY score DESC,character_name LIMIT $max_rank");
$num_players = mysql_num_rows($res);
echo "Total players: $num_players<BR>Players with destroyed ships are not counted.<BR><BR>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"SILVER\"><TD><B>Rank</B></TD><TD><B>Player</B><TD><B>Ship</B></TD><TD><B>Score</B></TD></TR>";
$color = "WHITE";
$i = 1;
while($row = mysql_fetch_array($res))
{
  echo "<TR BGCOLOR=\"$color\"><TD>$i</TD><TD>$row[character_name]</TD><TD>$row[ship_name]</TD><TD>$row[score]</TD></TR>";
  if($color == "WHITE")
  {
    $color = "LIGHTGREY";
  }
  else
  {
    $color = "WHITE";
  }
  $i++;
}
echo "</TABLE>";
echo "<BR><BR>";

echo "Generated on:  ";
print(date("l dS of F Y h:i:s A"));

echo "<BR><BR>Click <a href=main.php3>here</a> to return to main menu.";
include("footer.php3");

?>
