<?

include("config.php3");

updatecookie();

$title="Scan Ship";
include("header.php3");

connectdb();
checklogin();

$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query ("SELECT * FROM ships WHERE ship_id='$ship_id'");
$targetinfo=mysql_fetch_array($result2);

bigtitle();

srand((double)microtime()*1000000);

/* check to ensure target is in the same sector as player */
if($targetinfo[sector] != $playerinfo[sector])
{
  echo "Target not in this sector.";
}
else
{
  if($playerinfo[turns] < 1)
  {
    echo "You need at least one turn to scan another ship.";
  }
  else
  {
    /* determine per cent chance of success in scanning target ship - based on player's sensors and opponent's cloak */
    $success= SCAN_SUCCESS($playerinfo[sensors], $targetinfo[cloak]);
    if($success < 5)
    {
      $success = 5;
    }
    if($success > 95)
    {
      $success = 95;
    }
    $roll = rand(1, 100);
    if($roll > $success)
    {
      /* if scan fails - inform both player and target. */
      echo "Sensors cannot get a fix on target!";
      playerlog($targetinfo[ship_id],"$playerinfo[character_name] attempted to scan your ship, but failed.");
    }
    else
    {
      /* if scan succeeds, show results and inform target. */
      echo "Scan results on $targetinfo[ship_name], Captained by:  $targetinfo[character_name]<BR><BR>";
      echo "<b>Ship Component levels:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Hull:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[hull]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Engines:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[engines]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Power:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[power]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Computer:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[computer]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Sensors:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[sensors]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Beams:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[beams]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Torpedo Launchers:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[torp_launchers]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Armour:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[armour]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Shields:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[shields]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Cloak:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[cloak]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>Armament:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Armour Points:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[armour_pts]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Fighters:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[ship_fighters]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Torpedoes:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[torps]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>Carrying:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Credits:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[credits]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Colonists:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[ship_colonists]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Energy:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[ship_energy]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Ore:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[ship_ore]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Organics:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[ship_organics]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Goods:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[ship_goods]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>Devices:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Warp Editors:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[dev_warpedit]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Genesis Torpedoes:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[dev_genesis]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Mine Deflectors:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[dev_minedeflector]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Emergency Warp Devices:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[dev_emerwarp]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Escape Pods:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[dev_escapepod]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Fuel Scoop:</td>";
      $roll=rand(1,100);
      if ($roll<$success) {echo "<td>$targetinfo[dev_fuelscoop]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      playerlog($targetinfo[ship_id],"You were scanned by $playerinfo[character_name].");
    }
    
    mysql_query("UPDATE ships SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
  }
}


echo "<BR><BR>";
TEXT_GOTOMAIN();

include("footer.php3");

?>
