<?
include("extension.inc");
include("config.php3");

updatecookie();

$title="Scan Ship";
include("header.php3");

connectdb();
if(checklogin())
{
  die();
}
$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query ("SELECT * FROM ships WHERE ship_id='$ship_id'");
$targetinfo=mysql_fetch_array($result2);

bigtitle();

srand((double)microtime()*1000000);

/* check to ensure target is in the same sector as player */
if($targetinfo[sector] != $playerinfo[sector])
{
  echo "Sensors cannot get a fix on target!";
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
      /* scramble results by scan error factor. */
      $sc_error= SCAN_ERROR($playerinfo[sensors], $targetinfo[cloak]);
      echo "Scan results on $targetinfo[ship_name], Captained by:  $targetinfo[character_name]<BR><BR>";
      echo "<b>Ship Component levels:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Hull:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_hull=round($targetinfo[hull] * $sc_error / 100);
        echo "<td>$sc_hull</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Engines:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_engines=round($targetinfo[engines] * $sc_error / 100);
        echo "<td>$sc_engines</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Power:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_power=round($targetinfo[power] * $sc_error / 100);
        echo "<td>$sc_power</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Computer:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_computer=round($targetinfo[computer] * $sc_error / 100);
        echo "<td>$sc_computer</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Sensors:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_sensors=round($targetinfo[sensors] * $sc_error / 100);
        echo "<td>$sc_sensors</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Beams:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_beams=round($targetinfo[beams] * $sc_error / 100);
        echo "<td>$sc_beams</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Torpedo Launchers:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_torp_launchers=round($targetinfo[torp_launchers] * $sc_error / 100);
        echo "<td>$sc_torp_launchers</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Armour:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_armour=round($targetinfo[armour] * $sc_error / 100);
        echo "<td>$sc_armour</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Shields:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_shields=round($targetinfo[shields] * $sc_error / 100);
        echo "<td>$sc_shields</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Cloak:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_cloak=round($targetinfo[cloak] * $sc_error / 100);
        echo "<td>$sc_cloak</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>Armament:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Armour Points:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_armour_pts=round($targetinfo[armour_pts] * $sc_error / 100);
        echo "<td>$sc_armour_pts</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Fighters:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_fighters=round($targetinfo[ship_fighters] * $sc_error / 100);
        echo "<td>$sc_ship_fighters</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Torpedoes:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_torps=round($targetinfo[torps] * $sc_error / 100);
        echo "<td>$sc_torps</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>Carrying:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Credits:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_credits=round($targetinfo[credits] * $sc_error / 100);
        echo "<td>$sc_credits</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Colonists:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_colonists=round($targetinfo[ship_colonists] * $sc_error / 100);
        echo "<td>$sc_ship_colonists</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Energy:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_energy=round($targetinfo[ship_energy] * $sc_error / 100);
        echo "<td>$sc_ship_energy</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Ore:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_ore=round($targetinfo[ship_ore] * $sc_error / 100);
        echo "<td>$sc_ship_ore</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Organics:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_organics=round($targetinfo[ship_organics] * $sc_error / 100);
        echo "<td>$sc_ship_organics</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Goods:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_goods=round($targetinfo[ship_goods] * $sc_error / 100);
        echo "<td>$sc_ship_goods</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>Devices:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Warp Editors:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_dev_warpedit=round($targetinfo[dev_warpedit] * $sc_error / 100);
        echo "<td>$sc_dev_warpedit</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Genesis Torpedoes:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_dev_genesis=round($targetinfo[dev_genesis] * $sc_error / 100);
        echo "<td>$sc_dev_genesis</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Mine Deflectors:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_dev_minedeflector=round($targetinfo[dev_minedeflector] * $sc_error / 100);
        echo "<td>$sc_dev_minedeflector</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Emergency Warp Devices:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_dev_emerwarp=round($targetinfo[dev_emerwarp] * $sc_error / 100);
        echo "<td>$sc_dev_emerwarp</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>Escape Pods:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
        {echo "<td>$targetinfo[dev_escapepod]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>Fuel Scoop:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
        {echo "<td>$targetinfo[dev_fuelscoop]</td></tr>";} else {echo"<td>???</td></tr>";}
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
