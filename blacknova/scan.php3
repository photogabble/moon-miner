<?

include("config.php3");

updatecookie();

include($gameroot . $default_lang);
$title=$l_scan_title;
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
  echo $l_planet_noscan;
}
else
{
  if($playerinfo[turns] < 1)
  {
    echo $l_scan_turn;
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
      echo $l_planet_noscan;
      playerlog($targetinfo[ship_id],"$playerinfo[character_name] $l_scan_logfail");
    }
    else
    {
      /* if scan succeeds, show results and inform target. */
      /* scramble results by scan error factor. */
      $sc_error= SCAN_ERROR($playerinfo[sensors], $targetinfo[cloak]);
      echo "$l_scan_ron $targetinfo[ship_name], $l_scan_capt  $targetinfo[character_name]<BR><BR>";
      echo "<b>$l_ship_levels:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>$l_hull:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_hull=round($targetinfo[hull] * $sc_error / 100);
        echo "<td>$sc_hull</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_engines:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_engines=round($targetinfo[engines] * $sc_error / 100);
        echo "<td>$sc_engines</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_power:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_power=round($targetinfo[power] * $sc_error / 100);
        echo "<td>$sc_power</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_computer:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_computer=round($targetinfo[computer] * $sc_error / 100);
        echo "<td>$sc_computer</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_sensors:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_sensors=round($targetinfo[sensors] * $sc_error / 100);
        echo "<td>$sc_sensors</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_beams:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_beams=round($targetinfo[beams] * $sc_error / 100);
        echo "<td>$sc_beams</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_torpedo Launchers:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_torp_launchers=round($targetinfo[torp_launchers] * $sc_error / 100);
        echo "<td>$sc_torp_launchers</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_armour:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_armour=round($targetinfo[armour] * $sc_error / 100);
        echo "<td>$sc_armour</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_shields:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_shields=round($targetinfo[shields] * $sc_error / 100);
        echo "<td>$sc_shields</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_cloak:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_cloak=round($targetinfo[cloak] * $sc_error / 100);
        echo "<td>$sc_cloak</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>$l_scan_arma</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>$l_armourpts:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_armour_pts=round($targetinfo[armour_pts] * $sc_error / 100);
        echo "<td>$sc_armour_pts</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_fighters:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_fighters=round($targetinfo[ship_fighters] * $sc_error / 100);
        echo "<td>$sc_ship_fighters</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_torps:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_torps=round($targetinfo[torps] * $sc_error / 100);
        echo "<td>$sc_torps</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>$l_scan_carry</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>Credits:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_credits=round($targetinfo[credits] * $sc_error / 100);
        echo "<td>$sc_credits</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_colonists:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_colonists=round($targetinfo[ship_colonists] * $sc_error / 100);
        echo "<td>$sc_ship_colonists</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_energy:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_energy=round($targetinfo[ship_energy] * $sc_error / 100);
        echo "<td>$sc_ship_energy</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_ore:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_ore=round($targetinfo[ship_ore] * $sc_error / 100);
        echo "<td>$sc_ship_ore</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_organics:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_organics=round($targetinfo[ship_organics] * $sc_error / 100);
        echo "<td>$sc_ship_organics</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_goods:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_ship_goods=round($targetinfo[ship_goods] * $sc_error / 100);
        echo "<td>$sc_ship_goods</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      echo "<b>$l_devices:</b><BR><BR>";
      echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
      echo "<tr><td>$l_warpedit:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_dev_warpedit=round($targetinfo[dev_warpedit] * $sc_error / 100);
        echo "<td>$sc_dev_warpedit</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_genesis:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_dev_genesis=round($targetinfo[dev_genesis] * $sc_error / 100);
        echo "<td>$sc_dev_genesis</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_deflect:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_dev_minedeflector=round($targetinfo[dev_minedeflector] * $sc_error / 100);
        echo "<td>$sc_dev_minedeflector</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_ewd:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
      {
        $sc_dev_emerwarp=round($targetinfo[dev_emerwarp] * $sc_error / 100);
        echo "<td>$sc_dev_emerwarp</td></tr>";
      }
      else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_escape_pod:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
        {echo "<td>$targetinfo[dev_escapepod]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "<tr><td>$l_fuel_scoop:</td>";
      $roll=rand(1,100);
      if ($roll<$success)
        {echo "<td>$targetinfo[dev_fuelscoop]</td></tr>";} else {echo"<td>???</td></tr>";}
      echo "</table><BR>";
      playerlog($targetinfo[ship_id],"$l_scan_log $playerinfo[character_name].");
    }

    mysql_query("UPDATE ships SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
  }
}


echo "<BR><BR>";
TEXT_GOTOMAIN();

include("footer.php3");
?>
