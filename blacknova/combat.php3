<?

function calcplanetbeams()
{
	global $playerinfo;
	global $ownerinfo;
	global $sectorinfo;
	
	$energy_available = $sectorinfo[planet_energy];
	$planetbeams = NUM_BEAMS($ownerinfo[beams] + $basedefense);
	$res = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] AND on_planet='Y'");
	while($row = mysql_fetch_array($res))
	{
		$planetbeams = $planetbeams + NUM_BEAMS($row[beams]);
	}
		
	if ($planetbeams > $energy_available) $planetbeams = $energy_available;
	
	return $planetbeams;
}

function calcplanetfighters()
{
	global $sectorinfo;
	
	$planetfighters = $sectorinfo[planet_fighters];
	return $planetfighters;
}
		

function calcplanettorps()
{
	global $playerinfo;
	global $ownerinfo;
	global $sectorinfo;
	global $level_factor;
	
	$res = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] AND on_planet='Y'");
	$torp_launchers = round(pow($level_factor, ($ownerinfo[torp_launchers])+ $basedefense)) * 2;
	$torps = $sectorinfo[base_torp];
	while($row = mysql_fetch_array($res))
	{
		$torp_launchers = $torp_launchers + $row[torp_launchers];
	}
	
	if ($torp_launchers > $torps) {$planettorps = $torps;}
	else $planettorps = $torp_launchers;
	$sectorinfo[base_torp] = $sectorinfo[base_torp] - $planettorps;
	
		
	return $planettorps;
}

function calcplanetshields()
{
	global $playerinfo;
	global $ownerinfo;
	global $sectorinfo;
	
	$res = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] AND on_planet='Y'");
	$planetshields = NUM_SHIELDS($ownerinfo[shields]) + $basedefense;
	$energy_available = $sectorinfo[planet_energy];
	while($row = mysql_fetch_array($res))
	{
		$planetshields = $planetshields + NUM_SHIELDS($row[shields]);
	}
	
	if ($planetshields > $energy_available) {$planetshields = $energy_available;}
	$sectorinfo[planet_energy] = $sectorinfo[planet_energy] - $planetshields;
	return $planetshields;
}

function planetcombat()
{
global $playerinfo;
global $ownerinfo;
global $sectorinfo;
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
		echo "Ship energy before beams: $playerinfo[ship_energy]<BR>\n";
		if ($attackerbeams   > $playerinfo[ship_energy]) $attackerbeams   = $playerinfo[ship_energy];
		$playerinfo[ship_energy] = $playerinfo[ship_energy] - $attackerbeams;
		echo "Ship energy after beams (before shields): $playerinfo[ship_energy]<BR>\n";
		
		// Shields
		if ($attackershields > $playerinfo[ship_energy]) $attackershields = $playerinfo[ship_energy];
		$playerinfo[ship_energy] = $playerinfo[ship_energy] - $attackershields;
		echo "Ship energy after shields: $playerinfo[ship_energy]<BR>\n";
		
		// Torpedos
		echo "Ship torpedos before torp launch: $attackertorps ($playerinfo[torps] / $playerinfo[torp_launchers])<BR>\n";
		if ($attackertorps > $playerinfo[torps]) $attackertorps = $playerinfo[torps];
		$playerinfo[torps] = $playerinfo[torps] - $attackertorps;
		echo "Ship torpedos after torp launch: $attackertorps ($playerinfo[torps] / $playerinfo[torp_launchers])<BR>\n";

		// Setup torp damage rate for both Planet and Ship
		$planettorpdamage	= $torp_dmg_rate * $planettorps;
		$attackertorpdamage	= $torp_dmg_rate * $attackertorps;
		echo "Planet torp damage: $planettorpdamage<BR>\n";
		echo "Attacker torp damage: $attackertorpdamage<BR>\n";


echo "
<BR>--------------<BR>
planetbeams: $planetbeams<BR>\n
planetfighters: $planetfighters<BR>\n
planetshields: $planetshields<BR>\n
planettorps: $planettorps<BR>\n
--<BR>
attackerbeams: $attackerbeams<BR>\n
attackerfighters: $attackerfighters<BR>\n
attackershields: $attackershields<BR>\n
attackertorps: $attackertorps<BR>\n
attackertorpdamage: $attackertorpdamage<BR>\n
attackerarmor: $attackerarmor<BR>\n
";

		
// Begin actual combat calculations
		
		$planetdestroyed   = 0;
		$attackerdestroyed = 0;
		
        echo "<--Attacking planet in sector $playerinfo[sector]<BR><BR>";
        echo "<--You fire your beams<BR>";
        if($planetfighters > 0 && $attackerbeams > 0)
        {
          if($attackerbeams > $planetfighters)
          {
            echo "-->Planetary defense lost $planetfighters fighters to your beams<BR>";
            $planetfighters = 0;
            $attackerbeams = $attackerbeams - $planetfighters;
          }
          else
          {
            $planetfighters = $planetfighters - $attackerbeams;
            echo "-->Planetary Defense lost $attackerbeams fighters, but there are more coming!<BR>";
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
            echo "<--Planetary beams destroy $temp of your fighters<BR>";
          }
          else
          {
            $attackerfighters = $attackerfighters - $planetbeams;
            echo "<--Planetary beams destroy $planetbeams of your fighters<BR>";
            $planetbeams = 0;
          }
        }
        if($attackerbeams > 0)
        {
          if($attackerbeams > $planetshields)
          {
            $attackerbeams = $attackerbeams - $planetshields;
            $planetshields = 0;
            echo "-->Your beams have destroyed the planetary shields<BR>";
          }
          else
          {
            echo "-->You destroy $attackerbeams planetary shields before your beams are exhausted<BR>";
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
            echo "<--Planetary beams have breached your shields<BR>";
          }
          else
          {
            $attackershields = $attackershields - $planetbeams;
            echo "<--Planetary beams have destroyed $planetbeams of your shields<BR>";
            $planetbeams = 0;
          }
        }
        if($planetbeams > 0)
        {
          if($planetbeams > $attackerarmor)
          {
            $attackerarmor = 0;
            echo "<--Planetary beams have breached your armor<BR>";
          }
          else
          {
            $attackerarmor = $attackerarmor - $planetbeams;
            echo "Planetary beams have destroyed $planetbeams points of armor<BR>";
          } 
        } 
        echo "<BR>Torpedo Exchange<BR>";
        if($planetfighters > 0 && $attackertorpdamage > 0)
        {
          if($attackertorpdamage > $planetfighters)
          {
            echo "-->Your torpedos destroy $planetfighters planetary fighters, no fighters are left<BR>";
            $planetfighters = 0;
            $attackertorpdamage = $attackertorpdamage - $planetfighters;
          }
          else
          {
            $planetfighters = $planetfighters - $attackertorpdamage;
            echo "-->Your torpedos destroy $attackertorpdamage planetary fighters<BR>";
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
            echo "<--Planetary torpedos destroy $temp of your fighters<BR>";
          }
          else
          {
            $attackerfighters = $attackerfighters - $planettorpdamage;
            echo "<--Planetary torpedos destroy $planettorpdamage of your fighters<BR>";
            $planettorpdamage = 0;
          }
        }
        if($planettorpdamage > 0)
        {
          if($planettorpdamage > $attackerarmor)
          {
            $attackerarmor = 0;
            echo "-->Planetary torpedos have breached your armor<BR>";
          }
          else
          {
            $attackerarmor = $attackerarmor - $planettorpdamage;
            echo "-->Planetary torpedos have destroyed $planettorpdamage points of armor<BR>";
          } 
        }
        if($attackertorpdamage > 0 && $planetfighters > 0)
        {
          $planetfighters = $planetfighters - $attackertorpdamage;
          if ($planetfighters < 0) 
          {
          	$planetfighters = 0;
          	echo "<--Your torpedos have destroyed all the planetary fighters<BR>";
          }
          else { echo "<--Your torpeods destroy $attackertorpdamage planetary fighters<BR>"; }
        }
        echo "<BR>Fighter combat<BR>";
        if($attackerfighters > 0 && $planetfighters > 0)
        {
          if($attackerfighters > $planetfighters)
          {
            echo "<--Your fighters have destroyed all the planetary fighters.<BR>";
            $tempplanetfighters = 0;
          }
          else
          {
            echo "<--Your fighters have destroyed $attackerfighters planetary fighters<BR>";
            $tempplanetfighters = $planetfighters - $attackerfighters;
          }
          if($planetfighters > $attackerfighters)
          {
            echo "-->All your fighters were destroyed<BR>";
            $tempplayfighters = 0;
          }
          else
          {
            $tempplayfighters = $attackerfighters - $planetfighters;
            echo "<--You lost $planetfighters fighters in fighter to fighter combat<BR>";
          }     
          $attackerfighters = $tempplayfighters;
          $planetfighters = $tempplanetfighters;
        }
        if($attackerfighters > 0 && $planetshields > 0)
        {
          if($attackerfighters > $planetshields)
          {
            $attackerfighters = $attackerfighters - round($planetshields / 2);
            echo "<--Your fighters have breached the planetary shields<BR>";
            $planetshields = 0;
          }
          else
          {
            echo "-->Your fighters destroyed $attackerfighters planetary shields, but they remain up<BR>";
            $planetshields = $planetshields - $attackerfighters;
          }
        }
        if($planetfighters > 0)
        {
          if($planetfighters > $attackerarmor)
          {
            $attackerarmor = 0;
            echo "-->Planetary fighters swarm your ship, your armor has been breached<BR>";
          }
          else
          {
            $attackerarmor = $attackerarmor - $planetfighters;
            echo "<--Planetary fighters swarm your ship, but your armor repels them<BR>";
          }
        }
        
        // Send each docked ship in sequence to attack agressor
 		$result4 = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] AND on_planet='Y'");
		$shipsonplanet = mysql_num_rows($result4);
		
		echo "<BR>-/-/-/-/-/-<BR>$attackertorpdamage<BR>-/-/-/-/-/-/-<BR>";
      
		if ($shipsonplanet > 0)
		{
			echo "<BR>There are $shipsonplanet ships docked at Spacedock!<BR><BR>\n";
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
    	else echo "<BR>There are NO ships docked at Spacedock!<BR><BR>\n";    
        
        if($attackerarmor < 1)
        {
          $free_ore = round($playerinfo[ship_ore]/2);
          $free_organics = round($playerinfo[ship_organics]/2);
          $free_goods = round($playerinfo[ship_goods]/2);
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $playerinfo[hull]))+round(pow($upgrade_factor, $playerinfo[engines]))+round(pow($upgrade_factor, $playerinfo[power]))+round(pow($upgrade_factor, $playerinfo[computer]))+round(pow($upgrade_factor, $playerinfo[sensors]))+round(pow($upgrade_factor, $playerinfo[beams]))+round(pow($upgrade_factor, $playerinfo[torp_launchers]))+round(pow($upgrade_factor, $playerinfo[shields]))+round(pow($upgrade_factor, $playerinfo[armor]))+round(pow($upgrade_factor, $playerinfo[cloak])));
          $ship_salvage_rate=rand(0,10);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;
          echo "--->Your ship has been destroyed!<BR><BR>";
          if($playerinfo[dev_escapepod] == "Y")
          {
            echo "Luckily you have an escape pod!<BR><BR>";
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
echo "<BR>-0-0-0- <B>Final Stats</B> -0-0-0-<BR><BR>";
          $fighters_lost = $playerinfo[ship_fighters] - $attackerfighters;
echo "Fighters Lost: $fighters_lost out of $playerinfo[ship_fighters] total ($attackerfighters alive)<BR>";
          $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
echo "Armor Lost: $armor_lost out of $playerinfo[armour_pts] total ($attackerarmor points remain)<BR>";
          $energy=$playerinfo[ship_energy];
echo "Energy used: $energy from a total of $playerinfo[ship_energy]<BR>";
          mysql_query("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps,armour_pts=armour_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
        } 
		
		$result4 = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] AND on_planet='Y'");
		$shipsonplanet = mysql_num_rows($result4);
echo "Ships on planet = -$shipsonplanet-";
		
		if($planetshields < 1 && $planetfighters < 1 && $attackerarmor > 0 && $shipsonplanet == 0)
        {
          echo "<BR>Planet defeated.<BR><BR>";
          echo "You may <a href=planet.php3?command=capture>capture</a> the planet or just leave it undefended.<BR><BR>";
          playerlog($ownerinfo[ship_id], "Your planet in sector $playerinfo[sector] was defeated in battle by $playerinfo[character_name].");
          gen_score($ownerinfo[ship_id]);
          $update7a = mysql_query("UPDATE universe SET planet_fighters=0, base_torp=base_torp-$planettorps, planet_defeated='Y' WHERE sector_id=$sectorinfo[sector_id]");
        }
        else
        {
          echo "<BR>Planet not defeated.<BR><BR>";
echo "<BR><BR>Planet statistics<BR><BR>";
          $fighters_lost = $sectorinfo[planet_fighters] - $planetfighters;
echo "Fighters lost: $fighters_lost out of $sectorinfo[planet_fighters] ($planetfighters alive)<BR>";
          $energy=$sectorinfo[planet_energy];
echo "Energy left: $sectorinfo[planet_energy]<BR>";
          playerlog($ownerinfo[ship_id], "Your planet in sector $playerinfo[sector] was attacked by $playerinfo[character_name], but was not defeated.  You salvaged $free_ore units of ore, $free_organics units of organics, $free_goods unitsof goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.");
          gen_score($ownerinfo[ship_id]);
          $update7b = mysql_query("UPDATE universe SET planet_energy=$energy,planet_fighters=planet_fighters-$fighters_lost, base_torp=base_torp-$planettorps, planet_ore=planet_ore+$free_ore, planet_goods=planet_goods+$free_goods, planet_organics=planet_organics+$free_organics, planet_credits=planet_credits+$ship_salvage WHERE sector_id=$sectorinfo[sector_id]");
echo "<BR>Set: energy=$energy, fighters lost=$fighters_lost, base_torp=$sectorinfo[base_torp], sectorid=$sectorinfo[sector_id]<BR>";
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
          playerlog($targetinfo[ship_id],"$playerinfo[character_name] attacked you, and destroyed your ship!  Luckily you had an escape pod!<BR><BR>"); 
        }
        else
        {
          playerlog($targetinfo[ship_id],"$playerinfo[character_name] attacked you, and destroyed your ship!<BR><BR>"); 
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
        playerlog($targetinfo[ship_id],"$playerinfo[character_name] attacked you.  You lost $armor_lost points of armor and $fighters_lost fighters.<BR><BR>");
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
