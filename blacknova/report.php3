<?

include("config.php3");

updatecookie();


$title="Ship Report";

include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");

$playerinfo=mysql_fetch_array($result);

bigtitle();

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Player: $playerinfo[character_name]</B></TD><TD ALIGN=CENTER><B>Ship: $playerinfo[ship_name]</B></TD><TD ALIGN=RIGHT><B>Credits: " . NUMBER($playerinfo[credits]) . "</B></TD></TR>";
echo "</TABLE>";
echo "<BR>";

echo "<TABLE BORDER=0 CELLSPACING=5 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR><TD>";

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Ship Component Levels</B></TD><TD></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Hull</TD><TD>Level $playerinfo[hull]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Engines</TD><TD>Level $playerinfo[engines]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Power</TD><TD>Level $playerinfo[power]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Computer</TD><TD>Level $playerinfo[computer]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Sensors</TD><TD>Level $playerinfo[sensors]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Armour</TD><TD>Level $playerinfo[armour]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Shields</TD><TD>Level $playerinfo[shields]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Beams</TD><TD>Level $playerinfo[beams]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Torpedo launchers</TD><TD>Level $playerinfo[torp_launchers]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Cloak</TD><TD>Level $playerinfo[cloak]</TD></TR>";
echo "</TABLE>";
echo "</TD><TD VALIGN=TOP>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
$holds_used = $playerinfo[ship_ore] + $playerinfo[ship_organics] + $playerinfo[ship_goods] + $playerinfo[ship_colonists];
$holds_max = round(pow($level_factor,$playerinfo[hull])*100);
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Holds</B></TD><TD ALIGN=RIGHT><B>" . NUMBER($holds_used) . " / " . NUMBER($holds_max) . "</B></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Ore</TD><TD ALIGN=RIGHT>" . NUMBER($playerinfo[ship_ore]) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Organics</TD><TD ALIGN=RIGHT>" . NUMBER($playerinfo[ship_organics]) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Goods</TD><TD ALIGN=RIGHT>" . NUMBER($playerinfo[ship_goods]) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Colonists</TD><TD ALIGN=RIGHT>" . NUMBER($playerinfo[ship_colonists]) . "</TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";
$armour_pts_max = round(pow($level_factor,$playerinfo[armour])*100);
$ship_fighters_max = round(pow($level_factor,$playerinfo[computer])*100);
$torps_max = round(pow($level_factor,$playerinfo[torp_launchers])*100);
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Armour & Weapons</B></TD><TD></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Armour points</TD><TD ALIGN=RIGHT>" . NUMBER($playerinfo[armour_pts]) . " / " . NUMBER($armour_pts_max) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Fighters</TD><TD ALIGN=RIGHT>" . NUMBER($playerinfo[ship_fighters]) . " / " . NUMBER($ship_fighters_max) . "</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Torpedoes</TD><TD ALIGN=RIGHT>" . NUMBER($playerinfo[torps]) . " / " . NUMBER($torps_max) . "</TD></TR>";
echo "</TABLE>";
echo "</TD><TD VALIGN=TOP>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
$energy_max = round(pow($level_factor,$playerinfo[power])*500);
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Energy</B></TD><TD ALIGN=RIGHT><B>" . NUMBER($playerinfo[ship_energy]) . " / " . NUMBER($energy_max) . "</B></TD></TR>";
echo "<TR><TD>&nbsp;</TD></TR>";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Devices</B></TD><TD></B></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Beacons</TD><TD ALIGN=RIGHT>$playerinfo[dev_beacon]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Warp Editors</TD><TD ALIGN=RIGHT>$playerinfo[dev_warpedit]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Genesis Torpedoes</TD><TD ALIGN=RIGHT>$playerinfo[dev_genesis]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Mine Deflectors</TD><TD ALIGN=RIGHT>$playerinfo[dev_minedeflector]</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Emergency Warp</TD><TD ALIGN=RIGHT>$playerinfo[dev_emerwarp]</TD></TR>";
$escape_pod = ($playerinfo[dev_escapepod] == 'Y') ? "Yes" : "No";
$fuel_scoop = ($playerinfo[dev_fuelscoop] == 'Y') ? "Yes" : "No";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Escape Pod</TD><TD ALIGN=RIGHT>$escape_pod</TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Fuel Scoop</TD><TD ALIGN=RIGHT>$fuel_scoop</TD></TR>";
echo "</TABLE>";

echo "</TD></TR>";
echo "</TABLE>";

echo "<BR><BR>";

echo "Click <a href=main.php3>here</a> to return to Main Menu.";



include("footer.php3");

?>
