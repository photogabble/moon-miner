<?

function calcplanetbeams()
{
	global $playerinfo;
	global $ownerinfo;
	global $sectorinfo;
  global $basedefense;

  global $planetinfo;
	
	$energy_available = $planetinfo[energy];
  $base_factor = ($planetinfo[base] == 'Y') ? $basedefense : 0;
	$planetbeams = NUM_BEAMS($ownerinfo[beams] + $base_factor);
	$res = mysql_query("SELECT * FROM ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
	while($row = mysql_fetch_array($res))
	{
		$planetbeams = $planetbeams + NUM_BEAMS($row[beams]);
	}
		
	if ($planetbeams > $energy_available) $planetbeams = $energy_available;
	
	return $planetbeams;
}

function calcplanetfighters()
{
	global $planetinfo;
	
	$planetfighters = $planetinfo[fighters];
	return $planetfighters;
}
		

function calcplanettorps()
{
	global $playerinfo;
	global $ownerinfo;
	global $sectorinfo;
	global $level_factor;
  global $basedefense;

  global $planetinfo;
  $base_factor = ($planetinfo[base] == 'Y') ? $basedefense : 0;
	
	$res = mysql_query("SELECT * FROM ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
	$torp_launchers = round(pow($level_factor, ($ownerinfo[torp_launchers])+ $base_factor)) * 2;
	$torps = $planetinfo[torps];
	while($row = mysql_fetch_array($res))
	{
		$torp_launchers = $torp_launchers + $row[torp_launchers];
	}
	
	if ($torp_launchers > $torps) {$planettorps = $torps;}
	else $planettorps = $torp_launchers;
	$planetinfo[torps] -= $planettorps;
		
	return $planettorps;
}

function calcplanetshields()
{
	global $playerinfo;
	global $ownerinfo;
	global $sectorinfo;
  global $basedefense;

  global $planetinfo;


  $base_factor = ($planetinfo[base] == 'Y') ? $basedefense : 0;	
	$res = mysql_query("SELECT * FROM ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
	$planetshields = NUM_SHIELDS($ownerinfo[shields] + $base_factor);
	$energy_available = $planetinfo[energy];
	while($row = mysql_fetch_array($res))
	{
		$planetshields += NUM_SHIELDS($row[shields]);
	}
	
	if ($planetshields > $energy_available) {$planetshields = $energy_available;}
	$planetinfo[energy] -= $planetshields;
	return $planetshields;
}

function planetcombat()
{
global $playerinfo;
global $ownerinfo;
global $sectorinfo;
global $planetinfo;

global $planetbeams;
global $planetfighters;
global $planetshields;
global $planettorps;
global $attackerbeams;
global $attackerfighters;
global $attackershields;
global $attackertorps;
global $attackerarmor;
global $torp_dmg_rate;
global $level_factor;
global $attackertorpdamage;
global $start_energy;
global $min_value_capture;
//$debug = true;



      
      if($playerinfo[turns] < 1)
      {
        echo "You need at least one turn to attack a planet.<BR><BR>";
        TEXT_GOTOMAIN();
        include("footer.php3");   
        die();
      }
      
		// Planetary defense system calculation

		$planetbeams 		= calcplanetbeams();
		$planetfighters		= calcplanetfighters();
		$planetshields		= calcplanetshields();
		$planettorps		= calcplanettorps();

		// Attacking ship calculations
		
		$attackerbeams		= NUM_BEAMS($playerinfo[beams]);
		$attackerfighters	= $playerinfo[ship_fighters];
		$attackershields	= NUM_SHIELDS($playerinfo[shields]);
		$attackertorps		= round(pow($level_factor, $playerinfo[torp_launchers])) * 2;
		$attackerarmor		= $playerinfo[armour_pts];

		// Now modify player beams, shields and torpedos on available materiel
		
		// Beams
		if ($debug) echo "Ship energy before beams: $playerinfo[ship_energy]<BR>\n";
		if ($attackerbeams > $playerinfo[ship_energy]) $attackerbeams   = $playerinfo[ship_energy];
		$playerinfo[ship_energy] = $playerinfo[ship_energy] - $attackerbeams;
		if ($debug) echo "Ship energy after beams (before shields): $playerinfo[ship_energy]<BR>\n";
		
		// Shields
		if ($attackershields > $playerinfo[ship_energy]) $attackershields = $playerinfo[ship_energy];
		$playerinfo[ship_energy] = $playerinfo[ship_energy] - $attackershields;
		if ($debug) echo "Ship energy after shields: $playerinfo[ship_energy]<BR>\n";
		
		// Torpedos
		if ($debug) echo "Ship torpedos before torp launch: $attackertorps ($playerinfo[torps] / $playerinfo[torp_launchers])<BR>\n";
		if ($attackertorps > $playerinfo[torps]) $attackertorps = $playerinfo[torps];
		$playerinfo[torps] = $playerinfo[torps] - $attackertorps;
		if ($debug) echo "Ship torpedos after torp launch: $attackertorps ($playerinfo[torps] / $playerinfo[torp_launchers])<BR>\n";

		// Setup torp damage rate for both Planet and Ship
		$planettorpdamage	= $torp_dmg_rate * $planettorps;
		$attackertorpdamage	= $torp_dmg_rate * $attackertorps;
		if ($debug) echo "Planet torp damage: $planettorpdamage<BR>\n";
		if ($debug) echo "Attacker torp damage: $attackertorpdamage<BR>\n";


echo "
<CENTER>
<HR>
<table width='75%' border='0'>
  <tr ALIGN='CENTER'>
  	<td width='9%' height='27'></td>
    <td width='12%' height='27'><FONT COLOR='WHITE'>Beams</FONT></td>
    <td width='17%' height='27'><FONT COLOR='WHITE'>Fighters</FONT></td>
    <td width='18%' height='27'><FONT COLOR='WHITE'>Shields</FONT></td>
    <td width='11%' height='27'><FONT COLOR='WHITE'>Torps</FONT></td>
    <td width='22%' height='27'><FONT COLOR='WHITE'>Torp Damage</FONT></td>
    <td width='11%' height='27'><FONT COLOR='WHITE'>Armor</FONT></td>
  </tr>
  <tr ALIGN='CENTER'>
    <td width='9%'> <FONT COLOR='RED'>You</td>
    <td width='12%'><FONT COLOR='RED'><B>$attackerbeams</B></FONT></td>
    <td width='17%'><FONT COLOR='RED'><B>$attackerfighters</B></FONT></td>
    <td width='18%'><FONT COLOR='RED'><B>$attackershields</B></FONT></td>
    <td width='11%'><FONT COLOR='RED'><B>$attackertorps</B></FONT></td>
    <td width='22%'><FONT COLOR='RED'><B>$attackertorpdamage</B></FONT></td>
    <td width='11%'><FONT COLOR='RED'><B>$attackerarmor</B></FONT></td>
  </tr>
  <tr ALIGN='CENTER'>
    <td width='9%'> <FONT COLOR='#6098F8'>Planet</FONT></td>
    <td width='12%'><FONT COLOR='#6098F8'><B>$planetbeams</B></FONT></td>
    <td width='17%'><FONT COLOR='#6098F8'><B>$planetfighters</B></FONT></td>
    <td width='18%'><FONT COLOR='#6098F8'><B>$planetshields</B></FONT></td>
    <td width='11%'><FONT COLOR='#6098F8'><B>$planettorps</B></FONT></td>
    <td width='22%'><FONT COLOR='#6098F8'><B>$planettorpdamage</B></FONT></td>
    <td width='11%'><FONT COLOR='#6098F8'><B>N/A</B></FONT></td>
  </tr>
</table>
<HR>
</CENTER>
";

		
// Begin actual combat calculations
		
		$planetdestroyed   = 0;
		$attackerdestroyed = 0;
		
		echo "<BR><CENTER><B><FONT SIZE='+2'>Combat Flow</FONT></B><BR><BR>\n";
		echo "<table width='75%' border='0'><tr align='center'><td><FONT COLOR='RED'>You</FONT></td><td><FONT COLOR='#6098F8'>Defender</FONT></td>\n";
        echo "<tr align='center'><td><FONT COLOR='RED'><B>Attacking planet in sector $playerinfo[sector]</b></FONT></td><td></td>";
        echo "<tr align='center'><td><FONT COLOR='RED'><B>You fire your beams</b></FONT></td><td></td>\n";
        if($planetfighters > 0 && $attackerbeams > 0)
        {
          if($attackerbeams > $planetfighters)
          {
            echo "<tr align='center'><td></td><td><FONT COLOR='#6098F8'><B>Planetary defense lost $planetfighters fighters to your beams</B></FONT>";
            $planetfighters = 0;
            $attackerbeams = $attackerbeams - $planetfighters;
          }
          else
          {
            $planetfighters = $planetfighters - $attackerbeams;
            echo "<tr align='center'><td></td><td><FONT COLOR='#6098F8'><B>Planetary Defense lost $attackerbeams fighters, but there are more coming!</B></FONT>";
            $attackerbeams = 0;
          }
        }
        
        if($attackerfighters > 0 && $planetbeams > 0)
        {
          // If there are more beams on the planet than attacker has fighters 
          if($planetbeams > round($attackerfighters / 2))
          {
            // Half the attacker fighters
            $temp = round($attackerfighters / 2);
            // Attacker loses half his fighters
            $lost = $attackerfighters - $temp;
            // Set attacker fighters to 1/2 it's original value
            $attackerfighters = $temp;
            // Subtract half the attacker fighters from available planetary beams
            $planetbeams = $planetbeams - $lost;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Planetary beams destroy $temp of your fighters</B></FONT><TD></TD>";
          }
          else
          {
            $attackerfighters = $attackerfighters - $planetbeams;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Planetary beams destroy $planetbeams of your fighters</B></FONT><TD></TD>";
            $planetbeams = 0;
          }
        }
        if($attackerbeams > 0)
        {
          if($attackerbeams > $planetshields)
          {
            $attackerbeams = $attackerbeams - $planetshields;
            $planetshields = 0;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Your beams have destroyed the planetary shields</FONT></B><td></td>";
          }
          else
          {
            echo "<tr align='center'><td><FONT COLOR='RED'><B>You destroy $attackerbeams planetary shields before your beams are exhausted</FONT></B><td></td>";
            $planetshields = $planetshields - $attackerbeams;
            $attackerbeams = 0;
          }
        }
        if($planetbeams > 0)
        {
          if($planetbeams > $attackershields)
          {
            $planetbeams = $planetbeams - $attackershields;
            $attackershields = 0;
            echo "<tr align='center'><td></td><td><FONT COLOR='#6098F8'><B>Planetary beams have breached your shields</FONT></B></td>";
          }
          else
          {
            $attackershields = $attackershields - $planetbeams;
            echo "<tr align='center'><td></td><FONT COLOR='#6098F8'><B>Planetary beams have destroyed $planetbeams of your shields</FONT></B></td>";
            $planetbeams = 0;
          }
        }
        if($planetbeams > 0)
        {
          if($planetbeams > $attackerarmor)
          {
            $attackerarmor = 0;
            echo "<tr align='center'><td></td><td><FONT COLOR='#6098F8'><B>Planetary beams have breached your armor</B></FONT></td>";
          }
          else
          {
            $attackerarmor = $attackerarmor - $planetbeams;
            echo "<tr align='center'><td></td><td><FONT COLOR='#6098F8'><B>Planetary beams have destroyed $planetbeams points of armor</FONT></B></td>";
          } 
        } 
        echo "<tr align='center'><td><FONT COLOR='YELLOW'><B>Torpedo Exchange Phase</b></FONT></td><td><b><FONT COLOr='YELLOW'>Torpedo Exchange Phase</b></FONT></td><BR>";
        if($planetfighters > 0 && $attackertorpdamage > 0)
        {
          if($attackertorpdamage > $planetfighters)
          {
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Your torpedos destroy $planetfighters planetary fighters, no fighters are left</FONT></B></td><td></td>";
            $planetfighters = 0;
            $attackertorpdamage = $attackertorpdamage - $planetfighters;
          }
          else
          {
            $planetfighters = $planetfighters - $attackertorpdamage;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Your torpedos destroy $attackertorpdamage planetary fighters</FONT></B></td><td></td>";
            $attackertorpdamage = 0;
          }
        }
        if($attackerfighters > 0 && $planettorpdamage > 0)
        {
          if($planettorpdamage > round($attackerfighters / 2))
          {
            $temp = round($attackerfighters / 2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $planettorpdamage = $planettorpdamage - $lost;
            echo "<tr align='center'><td></td><td><FONT COLOR='RED'><B>Planetary torpedos destroy $temp of your fighters</B></FONT></td>";
          }
          else
          {
            $attackerfighters = $attackerfighters - $planettorpdamage;
            echo "<tr align='center'><td></td><td><FONT COLOR='RED'><B>Planetary torpedos destroy $planettorpdamage of your fighters</B></FONT></td>";
            $planettorpdamage = 0;
          }
        }
        if($planettorpdamage > 0)
        {
          if($planettorpdamage > $attackerarmor)
          {
            $attackerarmor = 0;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Planetary torpedos have breached your armor</B></FONT></td><td></td>";
          }
          else
          {
            $attackerarmor = $attackerarmor - $planettorpdamage;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Planetary torpedos have destroyed $planettorpdamage points of armor</B></FONT></td><td></td>";
          } 
        }
        if($attackertorpdamage > 0 && $planetfighters > 0)
        {
          $planetfighters = $planetfighters - $attackertorpdamage;
          if ($planetfighters < 0) 
          {
          	$planetfighters = 0;
          	echo "<tr align='center'><td><FONT COLOR='RED'><B>Your torpedos have destroyed all the planetary fighters</B></FONT></td><td></td>";
          }
          else { echo "<tr align='center'><td><FONT COLOR='RED'><B>Your torpeods destroy $attackertorpdamage planetary fighters</B></FONT></td><td></td>"; }
        }
        echo "<tr align='center'><td><FONT COLOR='YELLOW'><B>Fighter Combat Phase</b></FONT></td><td><b><FONT COLOr='YELLOW'>Fighter Combat Phase</b></FONT></td><BR>";
        if($attackerfighters > 0 && $planetfighters > 0)
        {
          if($attackerfighters > $planetfighters)
          {
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Your fighters have destroyed all the planetary fighters.</B></FONT></td><td></td>";
            $tempplanetfighters = 0;
          }
          else
          {
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Your fighters have destroyed $attackerfighters planetary fighters</B></FONT></td><td></td>";
            $tempplanetfighters = $planetfighters - $attackerfighters;
          }
          if($planetfighters > $attackerfighters)
          {
            echo "<tr align='center'><td><FONT COLOR='RED'><B>All your fighters were destroyed</B></FONT></td><td></td>";
            $tempplayfighters = 0;
          }
          else
          {
            $tempplayfighters = $attackerfighters - $planetfighters;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>You lost $planetfighters fighters in fighter to fighter combat</B></FONT></td><td></td>";
          }     
          $attackerfighters = $tempplayfighters;
          $planetfighters = $tempplanetfighters;
        }
        if($attackerfighters > 0 && $planetshields > 0)
        {
          if($attackerfighters > $planetshields)
          {
            $attackerfighters = $attackerfighters - round($planetshields / 2);
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Your fighters have breached the planetary shields</B></FONT></td><td></td>";
            $planetshields = 0;
          }
          else
          {
            echo "<tr align='center'><td></td><FONT COLOR='#6098F8'><B>Your fighters destroyed $attackerfighters planetary shields, but they remain up</B></FONT></td>";
            $planetshields = $planetshields - $attackerfighters;
          }          
        }            
        if($planetfighters > 0)
        {
          if($planetfighters > $attackerarmor)
          {
            $attackerarmor = 0;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Planetary fighters swarm your ship, your armor has been breached</B></FONT></td><td></td>";
          }
          else
          {
            $attackerarmor = $attackerarmor - $planetfighters;
            echo "<tr align='center'><td><FONT COLOR='RED'><B>Planetary fighters swarm your ship, but your armor repels them</B></FONT></td><td></td>";
          }
        }
        
        echo "</TABLE></CENTER>\n";
        // Send each docked ship in sequence to attack agressor
 		$result4 = mysql_query("SELECT * FROM ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
		$shipsonplanet = mysql_num_rows($result4);
		
		if ($shipsonplanet > 0)
		{
			echo "<BR><BR><CENTER>There are $shipsonplanet ships docked at Spacedock!<BR>Engaging in Ship to Ship combat.</CENTER><BR><BR>\n";
			while ($onplanet = mysql_fetch_array($result4))
      		{ 
      		//$playerinfo[ship_fighters] 	= $attackerfighters;
      		//$playerinfo[armour_pts] 	= $attackerarmor;
      		//$playerinfo[torps]			= $playerinfo[torps] - $attackertorps;
        	
        	if ($attackerfighters < 0) $attackerfighters = 0;
        	if ($attackertorps    < 0) $attackertorps = 0;
        	if ($attackershields  < 0) $attackershields = 0;
        	if ($attackerbeams    < 0) $attackerbeams = 0;
        	if ($attackerarmor    < 1) break;
        	
        	echo "<BR>-$onplanet[ship_name] is approaching on an attack vector-<BR>"; 
        	shiptoship($onplanet[ship_id]);
        	}
        }
    	else echo "<BR><BR><CENTER>There are NO ships docked at Spacedock!</CENTER><BR><BR>\n";    
        
        if($attackerarmor < 1)
        {
          $free_ore = round($playerinfo[ship_ore]/2);
          $free_organics = round($playerinfo[ship_organics]/2);
          $free_goods = round($playerinfo[ship_goods]/2);
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $playerinfo[hull]))+round(pow($upgrade_factor, $playerinfo[engines]))+round(pow($upgrade_factor, $playerinfo[power]))+round(pow($upgrade_factor, $playerinfo[computer]))+round(pow($upgrade_factor, $playerinfo[sensors]))+round(pow($upgrade_factor, $playerinfo[beams]))+round(pow($upgrade_factor, $playerinfo[torp_launchers]))+round(pow($upgrade_factor, $playerinfo[shields]))+round(pow($upgrade_factor, $playerinfo[armor]))+round(pow($upgrade_factor, $playerinfo[cloak])));
          $ship_salvage_rate=rand(0,10);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;
          echo "<BR><CENTER><FONT SIZE='+2' COLOR='RED'><B>Your ship has been destroyed!</FONT></B></CENTER><BR>";
          if($playerinfo[dev_escapepod] == "Y")
          {
            echo "<CENTER><FONT COLOR='WHITE'>Luckily you have an escape pod!</FONT></CENTER><BR><BR>";
            mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
          }
          else
          {
            db_kill_player($playerinfo['ship_id']);
          }
        }
        else
        { 
          $free_ore=0;
          $free_goods=0;
          $free_organics=0;
          $ship_salvage_rate=0;
          $ship_salvage=0;
          $planetrating = $ownerinfo[hull] + $ownerinfo[engines] + $ownerinfo[computer] + $ownerinfo[beams] + $ownerinfo[torp_launchers] + $ownerinfo[shields] + $ownerinfo[armor];
          if($ownerinfo[rating]!=0)
          {
            $rating_change=($ownerinfo[rating]/abs($ownerinfo[rating]))*$planetrating*10;
          }
          else
          {
            $rating_change=-100;
          }
          echo "<CENTER><BR><B><FONT SIZE='+2'>Final Combat Stats</FONT></B><BR><BR>";
          $fighters_lost = $playerinfo[ship_fighters] - $attackerfighters;
          echo "You lost $fighters_lost out of $playerinfo[ship_fighters] total fighters.<BR>";
          $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
          echo "You lost $armor_lost out of $playerinfo[armour_pts] total armor points, you have $attackerarmor points remaining.<BR>";
          $energy=$playerinfo[ship_energy];
          echo "You used $energy energy, from a total of $playerinfo[ship_energy] energy.<BR></CENTER>";
          mysql_query("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps,armour_pts=armour_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
        } 
		
		$result4 = mysql_query("SELECT * FROM ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
		$shipsonplanet = mysql_num_rows($result4);
		
		if($planetshields < 1 && $planetfighters < 1 && $attackerarmor > 0 && $shipsonplanet == 0)
        {
          echo "<BR><BR><CENTER><FONT COLOR='GREEN'><B>Planet defeated</b></FONT></CENTER><BR><BR>";
          
          if($min_value_capture != 0)
          {
            $playerscore = gen_score($playerinfo[ship_id]);
            $playerscore *= $playerscore;

            $planetscore = $planetinfo[organics] * $organics_price + $planetinfo[ore] * $ore_price + $planetinfo[goods] * $goods_price + $planetinfo[energy] * $energy_price + $planetinfo[fighters] * $fighter_price + $planetinfo[torps] * $torpedo_price + $planetinfo[colonists] * $colonist_price + $planetinfo[credits];
            $planetscore = $planetscore * $min_value_capture / 100;

            if($playerscore < $planetscore)
            {
              echo "<CENTER>The citizens of this planet have decided they'd rather die than serve a pathetic ruler like you. They use a laser drill to dig a hole to the planet's core. You barely have time to escape into orbit before the whole planet is reduced to a ball of molten lava.</CENTER><BR><BR>";
              mysql_query("DELETE FROM planets WHERE planet_id=$planetinfo[planet_id]");
              playerlog($ownerinfo[ship_id], LOG_PLANET_DEFEATED_D, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
              gen_score($ownerinfo[ship_id]);
            }
            else
            {
              echo "<CENTER><font color=red>You may <a href=planet.php3?planet_id=$planetinfo[planet_id]&command=capture>capture</a> the planet or just leave it undefended.</font></CENTER><BR><BR>";
              playerlog($ownerinfo[ship_id], LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
              gen_score($ownerinfo[ship_id]);
              $update7a = mysql_query("UPDATE planets SET fighters=0, torps=torps-$planettorps, base='N', defeated='Y' WHERE planet_id=$planetinfo[planet_id]");
            }
          }
          else
          {
            echo "<CENTER>You may <a href=planet.php3?planet_id=$planetinfo[planet_id]&command=capture>capture</a> the planet or just leave it undefended.</CENTER><BR><BR>";
            playerlog($ownerinfo[ship_id], LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
            gen_score($ownerinfo[ship_id]);
            $update7a = mysql_query("UPDATE planets SET fighters=0, torps=torps-$planettorps, base='N', defeated='Y' WHERE planet_id=$planetinfo[planet_id]");
          }
          calc_ownership($planetinfo[sector_id]);
        }
        else
        {
          echo "<BR><BR><CENTER><FONT COLOR='#6098F8'><B>Planet not defeated</B></FONT></CENTER>BR><BR>";
          if ($debug) echo "<BR><BR>Planet statistics<BR><BR>";
          $fighters_lost = $planetinfo[fighters] - $planetfighters;
          if ($debug) echo "Fighters lost: $fighters_lost out of $planetinfo[fighters] ($planetfighters alive)<BR>";
          $energy=$planetinfo[energy];
          if ($debug) echo "Energy left: $planetinfo[energy]<BR>";
          playerlog($ownerinfo[ship_id], LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]|$free_ore|$free_organics|$free_goods|$ship_salvage_rate|$ship_salvage");
          gen_score($ownerinfo[ship_id]);
          $update7b = mysql_query("UPDATE planets SET energy=$energy,fighters=fighters-$fighters_lost, torps=torps-$planettorps, ore=ore+$free_ore, goods=goods+$free_goods, organics=organics+$free_organics, credits=credits+$ship_salvage WHERE planet_id=$planetinfo[planet_id]");
          if ($debug) echo "<BR>Set: energy=$energy, fighters lost=$fighters_lost, torps=$planetinfo[torps], sectorid=$sectorinfo[sector_id]<BR>";
        }
        $update = mysql_query("UPDATE ships SET turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
}     

function shiptoship($ship_id)
{
		global $attackerbeams;
		global $attackerfighters;
		global $attackershields;
		global $attackertorps;
		global $attackerarmor;
		global $attackertorpdamage;
		global $start_energy;
		global $playerinfo;


	mysql_query("LOCK TABLES ships WRITE, universe WRITE, zones READ");

	$result2 = mysql_query ("SELECT * FROM ships WHERE ship_id='$ship_id'");
	$targetinfo=mysql_fetch_array($result2);
	 
echo "<BR><BR>-=-=-=-=-=-=-=--<BR>
Starting stats:<BR>
<BR>
Attackerbeams: $attackerbeams<BR>
Attackerfighters: $attackerfighters<BR>
Attackershields: $attackershields<BR>
Attackertorps: $attackertorps<BR>
Attackerarmor: $attackerarmor<BR>
Attackertorpdamage: $attackertorpdamage<BR>";
	  
	  $targetbeams = NUM_BEAMS($targetinfo[beams]);
      if($targetbeams>$targetinfo[ship_energy])
      {
        $targetbeams=$targetinfo[ship_energy];
      }
      $targetinfo[ship_energy]=$targetinfo[ship_energy]-$targetbeams;
      $targetshields = NUM_SHIELDS($targetinfo[shields]);
      if($targetshields>$targetinfo[ship_energy])
      {
        $targetshields=$targetinfo[ship_energy];
      }
      $targetinfo[ship_energy]=$targetinfo[ship_energy]-$targetshields;
      
      $targettorpnum = round(pow($level_factor,$targetinfo[torp_launchers]))*2;
      if($targettorpnum > $targetinfo[torps])
      {
        $targettorpnum = $targetinfo[torps];
      }
      $targettorpdmg = $torp_dmg_rate*$targettorpnum;
      $targetarmor = $targetinfo[armour_pts];
      $targetfighters = $targetinfo[ship_fighters];
      $targetdestroyed = 0;
      $playerdestroyed = 0;
      echo "-->$targetinfo[ship_name] is attacking you<BR><BR>";
      echo "Beam exchange<BR>";
      if($targetfighters > 0 && $attackerbeams > 0)
      {
        if($attackerbeams > round($targetfighters / 2))
        {
          $temp = round($targetfighters/2);
          $lost = $targetfighters-$temp;
          $targetfighters = $temp;
          $attackerbeams = $attackerbeams-$lost;
          echo "<-- Your beams destroy $lost fighters<BR>";
        }
        else
        {
          $targetfighters = $targetfighters-$attackerbeams;
          echo "--> Your beams destroy $attackerbeams fighters, but there are more coming<BR>";
          $attackerbeams = 0;
        }   
      }
      elseif ($targetfighters > 0 && $attackerbeams < 1) echo "You have no beams left to destroy the incoming fighters!<BR>";
      else echo "Your beams have no fighter targets to destroy<BR>";
      if($attackerfighters > 0 && $targetbeams > 0)
      {
        if($targetbeams > round($attackerfighters / 2))
        {
          $temp=round($attackerfighters/2);
          $lost=$attackerfighters-$temp;
          $attackerfighters=$temp;
          $targetbeams=$targetbeams-$lost;
          echo "--> $targetinfo[ship_name] destroyed $lost of your fighters with beams<BR>";
        }
        else
        {
          $attackerfighters=$attackerfighters-$targetbeams;
          echo "<-- $targetinfo[ship_name] destroyed $targetbeams of your fighters with beams, but you still have $attackerfighters left<BR>";
          $targetbeams=0;
        }
      }
      elseif ($attackerfighters > 0 && $targetbeams < 1) echo "Your fighters attack unhindered, the enemy has no beams left.<BR>";
      else echo "You have no fighters left to attack with<BR>";
      if($attackerbeams > 0)
      {
        if($attackerbeams > $targetshields)
        {
          $attackerbeams=$attackerbeams-$targetshields;
          $targetshields=0;
          echo "<-- You have breached $targetinfo[ship_name]'s shields with your beams<BR>";
        }
        else
        {
          echo "$targetinfo[ship_name]'s shields are hit for $attackerbeams damage by your beams<BR>";
          $targetshields=$targetshields-$attackerbeams;
          $attackerbeams=0;
        }
      }
      else echo "You have no beams left to attack $targetinfo[ship_name]'s shields<BR>";
      if($targetbeams > 0)
      {
        if($targetbeams > $attackershields)
        {
          $targetbeams=$targetbeams-$attackershields;
          $attackershields=0;
          echo "--> $targetinfo[ship_name]'s beams have breached your shields<BR>";
        }
        else
        {
          echo "<-- $targetinfo[ship_name]'s beams have hit your shields for $targetbeams damage.<BR>";
          $attackershields=$attackershields-$targetbeams;
          $targetbeams=0;
        }
      }
      else echo "$targetinfo[ship_name] has no beams left to attack your shields<BR>";
      if($attackerbeams > 0)
      {
        if($attackerbeams > $targetarmor)
        {
          $targetarmor=0;
          echo "--> Your beams have breached $targetinfo[ship_name]'s armor<BR>";
        }
        else
        {
          $targetarmor=$targetarmor-$attackerbeams;
          echo "Your beams have done $attackerbeams damage to $targetinfo[ship_name]'s armor<BR>";
        } 
      }
      else echo "You have no beams left to attack $targetinfo[ship_name]'s armor<BR>";
      if($targetbeams > 0)
      {
        if($targetbeams > $attackerarmor)
        {
          $attackerarmor=0;
          echo "--> Your armor has been breached by $targetinfo[ship_name]'s beams<BR>";
        }
        else
        {
          $attackerarmor=$attackerarmor-$targetbeams;
          echo "<-- Your armor is hit for $targetbeams damage by $targetinfo[ship_name]'s beams<BR>";
        } 
      }
      else echo "$targetinfo[ship_name] has no beams left to attack your armor<BR>";
      echo "<BR>Torpedo exchange<BR>";
      if($targetfighters > 0 && $attackertorpdamage > 0)
      {
        if($attackertorpdamage > round($targetfighters / 2))
        {
          $temp=round($targetfighters/2);
          $lost=$targetfighters-$temp;
          $targetfighters=$temp;
          $attackertorpdamage=$attackertorpdamage-$lost;
          echo "--> Your torpedos destroy $lost of $targetinfo[ship_name]'s fighters<BR>";
        }
        else
        {
          $targetfighters=$targetfighters-$attackertorpdamage;
          echo "<-- Your torpedos destroy $attackertorpdamage of $targetinfo[ship_name]'s fighters<BR>";
          $attackertorpdamage=0;
        }
      }
      elseif ($targetfighters > 0 && $attackertorpdamage < 1) echo "You have no torpedo's left to attack $targetinfo[ship_name]'s fighters.<BR>";
      else echo "$targetinfo[ship_name] has no fighters left for you to destroy with torpedos<BR>";
      if($attackerfighters > 0 && $targettorpdmg > 0)
      {
        if($targettorpdmg > round($attackerfighters / 2))
        {
          $temp=round($attackerfighters/2);
          $lost=$attackerfighters-$temp;
          $attackerfighters=$temp;
          $targettorpdmg=$targettorpdmg-$lost;
          echo "--> $targetinfo[ship_name]'s torpedo's destroy $lost of your fighters<BR>";
        }
        else
        {
          $attackerfighters=$attackerfighters-$targettorpdmg;
          echo "<-- $targetinfo[ship_name] destroyed $targettorpdmg of your fighters with torpedos<BR>";
          $targettorpdmg=0;
        }
      }
      elseif ($attackerfighters > 0 && $targettorpdmg < 1) echo "$targetinfo[ship_name] has no torpedo's left to attack your fighters<BR>";
      else echo "You have no fighters left to be destroyed by $targetinfo[ship_name]'s torpedo's<BR>";
      if($attackertorpdamage > 0)
      {
        if($attackertorpdamage > $targetarmor)
        {
          $targetarmor=0;
          echo "--> You have breached $targetinfo[ship_name]'s armor with your torpedos<BR>";
        }
        else
        {
          $targetarmor=$targetarmor-$attackertorpdamage;
          echo "<-- $targetinfo[ship_name]'s armor is hit for $attackertorpdamage damage by your torpedos<BR>";
        } 
      }
      else echo "You have no torpedo's left to attack $targetinfo[ship_name]'s armor<BR>";
      if($targettorpdmg > 0)
      {
        if($targettorpdmg > $attackerarmor)
        {
          $attackerarmor=0;
          echo "<-- Your armor has been breached by $targetinfo[ship_name]'s torpedos<BR>";
        }
        else
        {
          $attackerarmor=$attackerarmor-$targettorpdmg;
          echo "<-- Your armor is hit for $targettorpdmg damage by $targetinfo[ship_name]'s torpedos<BR>";
        } 
      }
      else echo "$targetinfo[ship_name] has no torpedo's left to attack your armor<BR>";
      echo "<BR>Fighters Attack exchange<BR>";
      if($attackerfighters > 0 && $targetfighters > 0)
      {
        if($attackerfighters > $targetfighters)
        {
          echo "--> $targetinfo[ship_name] lost all fighters.<BR>";
          $temptargfighters=0;
        }
        else
        {
          echo "$targetinfo[ship_name] lost $attackerfighters fighters.<BR>";
          $temptargfighters=$targetfighters-$attackerfighters;
        }
        if($targetfighters > $attackerfighters)
        {
          echo "<-- You lost all fighters.<BR>";
          $tempplayfighters=0;
        }
        else
        {
          echo "<--You lost $targetfighters fighters.<BR>";
          $tempplayfighters=$attackerfighters-$targetfighters;
        }     
        $attackerfighters=$tempplayfighters;
        $targetfighters=$temptargfighters;
      }
      elseif ($attackerfighters > 0 && $targetfighters < 1) echo "$targetinfo[ship_name] has no fighters left for your fighters to attack<BR>";
      else echo "You have no fighters left to attack $targetinfo[ship_name]'s fighters<BR>";
      if($attackerfighters > 0)
      {
        if($attackerfighters > $targetarmor)
        {
          $targetarmor=0;
          echo "--> You have breached $targetinfo[ship_name]'s armor with your fighters<BR>";
        }
        else
        {
          $targetarmor=$targetarmor-$attackerfighters;
          echo "<-- You hit $targetinfo[ship_name]'s armor for $attackerfighters damage with your fighters<BR>";
        }
      }
      else echo "You have no fighters left to attack $targetinfo[ship_name]'s armor<BR>";
      if($targetfighters > 0)
      {
        if($targetfighters > $attackerarmor)
        {
          $attackerarmor=0;
          echo "<-- $targetinfo[ship_name] has breached your armor with fighters<BR>";
        }
        else
        {
          $attackerarmor=$attackerarmor-$targetfighters;
          echo "--> Your armor is hit for $targetfighters damage by $targetinfo[ship_name]'s fighters<BR>";
        }
      }
      else echo "$targetinfo[ship_name] has no fighters left to attack your armor<BR>";
      if($targetarmor < 1)
      {
        echo "<BR>$targetinfo[ship_name] has been destroyed<BR>";
        if($targetinfo[dev_escapepod] == "Y")
        {
          $rating=round($targetinfo[rating]/2);
          echo "An escape pod was launched!<BR><BR>";
          echo "<BR><BR>ship_id=$targetinfo[ship_id]<BR><BR>";
          $test = mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating' WHERE ship_id=$targetinfo[ship_id]"); 
          playerlog($targetinfo[ship_id],LOG_ATTACK_LOSE, "$playerinfo[character_name] Y"); 
        }
        else
        {
          playerlog($targetinfo[ship_id], LOG_ATTACK_LOSE, "$playerinfo[character_name] N"); 
          db_kill_player($targetinfo['ship_id']);
        }   
      
        if($attackerarmor > 0)
        {
          $rating_change=round($targetinfo[rating]*$rating_combat_factor);
          $free_ore = round($targetinfo[ship_ore]/2);
          $free_organics = round($targetinfo[ship_organics]/2);
          $free_goods = round($targetinfo[ship_goods]/2);
          $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
          if($free_holds > $free_goods)
          {
            $salv_goods=$free_goods;
            $free_holds=$free_holds-$free_goods;
          }
          elseif($free_holds > 0)
          {
            $salv_goods=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_goods=0;
          }
          if($free_holds > $free_ore)
          {
            $salv_ore=$free_ore;
            $free_holds=$free_holds-$free_ore;
          }
          elseif($free_holds > 0)
          {
            $salv_ore=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_ore=0;
          }
          if($free_holds > $free_organics)
          {
            $salv_organics=$free_organics;
            $free_holds=$free_holds-$free_organics;
          }
          elseif($free_holds > 0)
          {
            $salv_organics=$free_holds;
            $free_holds=0;
          }
          else
          {
            $salv_organics=0;
          }
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $targetinfo[hull]))+round(pow($upgrade_factor, $targetinfo[engines]))+round(pow($upgrade_factor, $targetinfo[power]))+round(pow($upgrade_factor, $targetinfo[computer]))+round(pow($upgrade_factor, $targetinfo[sensors]))+round(pow($upgrade_factor, $targetinfo[beams]))+round(pow($upgrade_factor, $targetinfo[torp_launchers]))+round(pow($upgrade_factor, $targetinfo[shields]))+round(pow($upgrade_factor, $targetinfo[armor]))+round(pow($upgrade_factor, $targetinfo[cloak])));
          $ship_salvage_rate=rand(10,20);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;
          echo "You salvaged $salv_ore units of ore, $salv_organics units of organics, $salv_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.<BR>Your rating changed by " . NUMBER(abs($rating_change)) . " points.";
          $update3 = mysql_query ("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
        }
      }
      else
      {
        echo "You did not destory $targetinfo[ship_name]<BR>";
        $target_rating_change=round($targetinfo[rating]*.1);
        $target_armor_lost=$targetinfo[armour_pts]-$targetarmor;
        $target_fighters_lost=$targetinfo[ship_fighters]-$targetfighters;
        $target_energy=$targetinfo[ship_energy];
        playerlog($targetinfo[ship_id], LOG_ATTACKED_WIN, "$playerinfo[character_name] $armor_lost $fighters_lost");
        $update4 = mysql_query ("UPDATE ships SET ship_energy=$target_energy,ship_fighters=ship_fighters-$target_fighters_lost, armour_pts=armour_pts-$target_armor_lost, torps=torps-$targettorpnum WHERE ship_id=$targetinfo[ship_id]");
      }
      echo "<BR>_+_+_+_+_+_+_<BR>";
      echo "Ship to Ship combat stats<BR>";
      echo "Attacker beams: $attackerbeams<BR>";
      echo "Attacker fighters: $attackerfighters<BR>";
      echo "Attacker shields: $attackershields<BR>";
      echo "Attacker torps: $attackertorps<BR>";
      echo "Attacker armor: $attackerarmor<BR>";
      echo "Attackertorpdamage: $attackertorpdamage<BR>";
      echo "_+_+_+_+_+_+<BR>";
mysql_query("UNLOCK TABLES");
}
?>
