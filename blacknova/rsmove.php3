<?
include("config.php3");

updatecookie();

include_once($gameroot . "/languages/$lang");

$title=$l_rs_title;
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

bigtitle();

$deg = pi() / 180;

if(isset($destination))
{
  $destination = round(abs($destination));
}

if(!isset($destination))
{
  echo "<FORM ACTION=rsmove.php3 METHOD=POST>";
  $l_rs_insector=str_replace("[sector]",$playerinfo[sector],$l_rs_insector);
  $l_rs_insector=str_replace("[sector_max]",$sector_max,$l_rs_insector);
  echo "$l_rs_insector<BR><BR>";
  echo "$l_rs_whichsector:  <INPUT TYPE=TEXT NAME=destination SIZE=10 MAXLENGTH=10><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=$l_rs_submit><BR><BR>";
  echo "</FORM>";
}
elseif($destination <= $sector_max && empty($engage))
{
  $result2 = mysql_query("SELECT angle1,angle2,distance FROM universe WHERE sector_id=$playerinfo[sector]");
  $start = mysql_fetch_array($result2);
  $result3 = mysql_query("SELECT angle1,angle2,distance FROM universe WHERE sector_id=$destination");
  $finish = mysql_fetch_array($result3);
  $sa1 = $start[angle1] * $deg;
  $sa2 = $start[angle2] * $deg;
  $fa1 = $finish[angle1] * $deg;
  $fa2 = $finish[angle2] * $deg;
  $x = ($start[distance] * sin($sa1) * cos($sa2)) - ($finish[distance] * sin($fa1) * cos($fa2));
  $y = ($start[distance] * sin($sa1) * sin($sa2)) - ($finish[distance] * sin($fa1) * sin($fa2));
  $z = ($start[distance] * cos($sa1)) - ($finish[distance] * cos($fa1));
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
  if($playerinfo[dev_fuelscoop] == "Y" && $energyscooped == 0 && $triptime == 1)
  {
    $energyscooped = 100;
  }
  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
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
 $l_rs_movetime=str_replace("[triptime]",NUMBER($triptime),$l_rs_movetime);
 $l_rs_energy=str_replace("[energy]",NUMBER($energyscooped),$l_rs_energy);
  echo "$l_rs_movetime $l_rs_energy<BR><BR>";
  if($triptime > $playerinfo[turns])
  {
    echo "$l_rs_noturns";
  }
  else
  {
    $l_rs_engage_link= "<A HREF=rsmove.php3?engage=1&destination=$destination>" . $l_rs_engage_link . "</A>";
    $l_rs_engage=str_replace("[turns]",NUMBER($playerinfo[turns]),$l_rs_engage);
    $l_rs_engage=str_replace("[engage]",$l_rs_engage_link,$l_rs_engage);
    echo "$l_rs_engage<BR><BR>";
  }
}
elseif($destination <= $sector_max && $engage == 1)
{
  $result2 = mysql_query("SELECT angle1,angle2,distance FROM universe WHERE sector_id=$playerinfo[sector]");
  $start = mysql_fetch_array($result2);
  $result3 = mysql_query("SELECT angle1,angle2,distance FROM universe WHERE sector_id=$destination");
  $finish = mysql_fetch_array($result3);
  $sa1 = $start[angle1] * $deg;
  $sa2 = $start[angle2] * $deg;
  $fa1 = $finish[angle1] * $deg;
  $fa2 = $finish[angle2] * $deg;
  $x = ($start[distance] * sin($sa1) * cos($sa2)) - ($finish[distance] * sin($fa1) * cos($fa2));
  $y = ($start[distance] * sin($sa1) * sin($sa2)) - ($finish[distance] * sin($fa1) * sin($fa2));
  $z = ($start[distance] * cos($sa1)) - ($finish[distance] * cos($fa1));
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
  if($playerinfo[dev_fuelscoop] == "Y" && $energyscooped == 0 && $triptime == 1)
  {
    $energyscooped = 100;
  }
  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
  if($free_power < $energyscooped)
  {
    $energyscooped = $free_power;
  }
  if(!isset($energyscooped))
  {
    $energyscooped = "0";
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
  if($triptime > $playerinfo[turns])
  {
   $l_rs_movetime=str_replace("[triptime]",NUMBER($triptime),$l_rs_movetime);
    echo "$l_rs_movetime<BR><BR>";
    echo "$l_rs_noturns";
  }
  else
  {
    $ok=1;
    $sector = $destination;
    $calledfrom = "rsmove.php3";
    include("check_fighters.php3");
    if($ok>0)
    {
       $stamp = date("Y-m-d H-i-s");
       $update = mysql_query("UPDATE ships SET last_login='$stamp',sector=$destination,ship_energy=ship_energy+$energyscooped,turns=turns-$triptime,turns_used=turns_used+$triptime WHERE ship_id=$playerinfo[ship_id]");
       $l_rs_ready=str_replace("[sector]",$destination,$l_rs_ready);
       $l_rs_ready=str_replace("[triptime]",NUMBER($triptime),$l_rs_ready);
       $l_rs_ready=str_replace("[energy]",NUMBER($energyscooped),$l_rs_ready);
       echo "$l_rs_ready<BR><BR>";
       include("check_mines.php3");
    }
  }
}
else
{
  echo "$l_rs_invalid.<BR><BR>";
}


//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php3");

?>
