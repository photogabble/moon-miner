<?
	include("config.php3");
	updatecookie();

	$title="Real Space Move";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}


	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
        bigtitle();
	$deg = pi()/180;	
	if (isset($destination)){$destination=round(abs($destination));}

	if (!isset($destination))
	{
		echo "<form action=rsmove.php3 method=post>";
		echo "You are presently in sector $playerinfo[sector] - and there are sectors available from 0 to $sector_max.<BR><BR>";
		echo "What sector would you like to attempt to get to through realspace:  <input type=text name=destination size=10 maxlength=10><BR><BR>";
		echo "<input type=submit value=Submit><input type=reset value=Reset><BR><BR>";
		echo "</form>";
	} elseif ($destination<=$sector_max   && empty($engage)) {

		$result2 = mysql_query ("SELECT angle1, angle2, distance FROM universe WHERE sector_id=$playerinfo[sector]");
		$start = mysql_fetch_array($result2);
		$result3 = mysql_query ("SELECT angle1, angle2, distance FROM universe WHERE sector_id=$destination");
		$finish = mysql_fetch_array($result3);
		$sa1=$start[angle1]*$deg;
		$sa2=$start[angle2]*$deg;
		$fa1=$finish[angle1]*$deg;
		$fa2=$finish[angle2]*$deg;
		$x = ($start[distance]*sin($sa1)*cos($sa2))-($finish[distance]*sin($fa1)*cos($fa2));
		$y = ($start[distance]*sin($sa1)*sin($sa2))-($finish[distance]*sin($fa1)*sin($fa2));
		$z = ($start[distance]*cos($sa1))-($finish[distance]*cos($fa1));
		$distance = round(sqrt(pow($x,2)+pow($y,2)+pow($z,2)));
		$shipspeed = pow($level_factor,$playerinfo[engines]);
		$triptime = round($distance/$shipspeed);
		if ($triptime==0 && $destination!=$playerinfo[sector]) {$triptime=1;}
		if ($playerinfo[dev_fuelscoop]=="Y"){$energyscooped = $distance * 100;} else {$energyscooped = 0;}
		if ($playerinfo[dev_fuelscoop]=="Y" && $energyscooped==0 && $triptime==1) {$energyscooped=100;}
		$free_power=round((pow($level_factor,$playerinfo[power]) * 500) - $playerinfo[ship_energy]);
		if ($free_power<$energyscooped) {$energyscooped=$free_power;}
		if ($energyscooped<1) {$energyscooped=0;}
		if ($destination==$playerinfo[sector]) {$triptime=0;$energyscooped=0;}
		echo "With your engines, it will take $triptime turns to complete the journey.  You would gather $energyscooped units of energy.<BR><BR>";
		if ($triptime>$playerinfo[turns])
		{
			echo "You only have $playerinfo[turns], and cannot embark on this journey.";
		} else {
			echo "You have $playerinfo[turns] turns.  <a href=rsmove.php3?engage=1&destination=$destination>Engage</a> engines?<BR><BR>";
		}
	} elseif ($destination<=$sector_max   && $engage==1) {

		$result2 = mysql_query ("SELECT angle1, angle2, distance  FROM universe WHERE sector_id=$playerinfo[sector]");
		$start = mysql_fetch_array($result2);
		$result3 = mysql_query ("SELECT angle1, angle2, distance  FROM universe WHERE sector_id=$destination");
		$finish = mysql_fetch_array($result3);
		$sa1=$start[angle1]*$deg;
		$sa2=$start[angle2]*$deg;
		$fa1=$finish[angle1]*$deg;
		$fa2=$finish[angle2]*$deg;
		$x = ($start[distance]*sin($sa1)*cos($sa2))-($finish[distance]*sin($fa1)*cos($fa2));
		$y = ($start[distance]*sin($sa1)*sin($sa2))-($finish[distance]*sin($fa1)*sin($fa2));
		$z = ($start[distance]*cos($sa1))-($finish[distance]*cos($fa1));
		$distance = round(sqrt(pow($x,2)+pow($y,2)+pow($z,2)));
		$shipspeed = pow($level_factor,$playerinfo[engines]);
		$triptime = round($distance/$shipspeed);
		if ($triptime==0 && $destination!=$playerinfo[sector]) {$triptime=1;}
		if ($playerinfo[dev_fuelscoop]=="Y"){$energyscooped = $distance * 100;} else {$energyscooped = 0;}
		if ($playerinfo[dev_fuelscoop]=="Y" && $energyscooped==0 && $triptime==1) {$energyscooped=100;}
		$free_power=round((pow($level_factor,$playerinfo[power]) * 500) - $playerinfo[ship_energy]);
		if ($free_power<$energyscooped) {$energyscooped=$free_power;}
		if (!isset($energyscooped)) {$energyscooped="0";}
		if ($energyscooped<1) {$energyscooped=0;}
		if ($destination==$playerinfo[sector]) {$triptime=0;$energyscooped=0;}
		if ($triptime>$playerinfo[turns])
		{
			echo "You only have $playerinfo[turns], and cannot embark on this journey.";
		} else {
			$update = mysql_query ("UPDATE ships SET sector=$destination, ship_energy=ship_energy+$energyscooped, turns=turns-$triptime, turns_used=turns_used+$triptime WHERE ship_id=$playerinfo[ship_id]");
			echo "You are now in sector $destination.  You used $triptime turns, and gained $energyscooped energy units.<BR><BR>";

		}
		


	} else {
		echo "Invalid destination.<BR><BR>";
	}
    TEXT_GOTOMAIN();
	include("footer.php3");

?>
