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
$res = mysql_query("SELECT planet_owner,COUNT(*) AS num_planets,ROUND(SQRT($calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits)) AS score FROM universe WHERE planet='Y' GROUP BY planet_owner");
if(mysql_num_rows($res))
{
  while($row = mysql_fetch_array($res))
  {
    $planet_score[$row[planet_owner]] = $row[score];
  }
}

$res = mysql_query("SELECT ship_id,character_name,ship_name,ROUND(SQRT($calc_levels+$calc_equip+$calc_dev+credits)) AS score FROM ships WHERE ship_destroyed='N' ORDER BY score DESC,character_name LIMIT $max_rank");
$num_players = mysql_num_rows($res);
echo "Total players: $num_players<BR>Players with destroyed ships are not counted.<BR><BR>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"SILVER\"><TD><B>Rank</B></TD><TD><B>Player</B><TD><B>Ship</B></TD><TD><B>Ship Score</B></TD><TD><B>Planet Score</B></TD><TD><B>TOTAL</B></TD></TR>";
$color = "WHITE";
$i = 1;
while($row = mysql_fetch_array($res))
{
  $score_ship = $row[score];
  $score_planet = (int)($planet_score[$row[ship_id]]);
  $score_total = $score_ship + $score_planet;
  echo "<TR BGCOLOR=\"$color\"><TD>$i</TD><TD>$row[character_name]</TD><TD>$row[ship_name]</TD><TD>$score_ship</TD><TD>$score_planet</TD><TD>$score_total</TD></TR>";
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

$result1 = mysql_query("SELECT * FROM ships WHERE ship_destroyed='N'");

$i=0;
while($row = mysql_fetch_array($result1))
{
  $i++;
  $score = 1 + round($row[credits]/10000+$row[hull]/10+$row[engines]/10+$row[power]/10+$row[computers]/10+$row[sensors]/10+$row[beams]/10+$row[torp_launchers]/10+$row[armour]/10+$row[cloak]/10+$row[shields]/10+$row[ship_fighters]/1000+$row[ship_ore]/1000+$row[ship_organics]/1000+$row[ship_goods]/1000+$row[energy]/1000+$row[torps]/1000+$row[dev_genesis]/100+$row[dev_minedeflectors]/1000+$row[dev_warpedit]/100+$row[dev_beacon]/100);
  if($row[dev_escapepod] == "Y")
  {
    $score = $score + 100;
  }
  if($row[dev_fuelscoop] == "Y")
  {
    $score = $score + 100;
  }
  $result2 = mysql_query("SELECT * FROM universe WHERE planet_owner=$row[ship_id]");
  while($planet = mysql_fetch_array($result2))
  {
    $score = $score + round($planet[planet_organics] / 1000 + $planet[planet_ore] / 1000 + $planet[planet_goods] / 1000 + $planet[planet_energy] / 1000 + $planet[planet_credits] / 10000 + $planet[planet_colonists] / 1000 + $planet[planet_fighters] / 1000 + $planet[base_torp] / 1000);
    if($planet[base] == "Y")
    {
      $score = $score + 1000;
    }
  }
  mysql_free_result($result2);
  /*    $rank[$i]=array ($score, $row[character_name], $row[ship_id]); */
  $rank[$row[ship_id]] = $score;
  $name[$row[ship_id]] = $row[character_name];
}

$num_players = count($rank);
arsort($rank, SORT_NUMERIC);
reset($rank);

echo "Generated at:  ";
print(date("l dS of F Y h:i:s A"));
echo "<BR>Total players: $num_players<BR>Players with destroyed ships are not counted.<BR><BR>";

echo "<table>";
echo "<tr><td>Rank</td><td>Score</td><td>Player</td></tr>";
$offset = 100;
if($offset > $num_players)
{
  $offset = $num_players;
}
for($i=1; $i<=$offset; $i++)
{
  list($key, $value) = each($rank);
  echo "<tr><td>$i</td><td>$value</td><td>$name[$key]</td></tr>";
}
if($i == $num_players) 
{
  list($key, $value) = each($rank);
  echo "<tr><td>$i</td><td>$value</td><td>$name[$key]</td></tr>";
}
echo "</table>";
echo "<BR>Click <a href=main.php3>here</a> to return to main menu.";
include("footer.php3");

?>
