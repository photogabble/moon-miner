<?

include("config.php3");
updatecookie();

$title="Attack Ship";
include("header.php3");

connectdb();
checklogin();

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships WRITE, universe WRITE, zones READ");

$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query ("SELECT * FROM ships WHERE ship_id='$ship_id'");
$targetinfo=mysql_fetch_array($result2);

bigtitle();

srand((double)microtime()*1000000);

/* check to ensure target is in the same sector as player */
if($targetinfo[sector] != $playerinfo[sector] || $targetinfo[on_planet] == "Y")
{
  echo "Target not in this sector.<BR><BR>";
}
elseif($playerinfo[turns] < 1)
{
  echo "You need at least one turn to attack.<BR><BR>";
}
else
{
  /* determine percent chance of success in detecting target ship - based on player's sensors and opponent's cloak */
  $success = (10 - $targetinfo[cloak] + $playerinfo[sensors]) * 5;
  if($success < 5)
  {
    $success = 5;
  }
  if($success > 95)
  {
    $sucess = 95;
  }
  $flee = (10 - $targetinfo[engines] + $playerinfo[engines]) * 5;
  $roll = rand(1, 100);
  $roll2 = rand(1, 100);

  $res = mysql_query("SELECT allow_attack,universe.zone_id FROM zones,universe WHERE sector_id='$targetinfo[sector]' AND zones.zone_id=universe.zone_id");
  $zoneinfo = mysql_fetch_array($res);
  if($zoneinfo[allow_attack] == 'N')
  {
    echo "Attacking someone in this sector is not permitted.<BR><BR>";
  }
  elseif($flee < $roll2)
  {
    echo "Target out maneuvered you!<BR><BR>";
    mysql_query("UPDATE ships SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    playerlog($targetinfo[ship_id],"$playerinfo[character_name] attempted to attack your ship, but your ship was faster.");
  }
  elseif($roll > $success)
  {
    /* if scan fails - inform both player and target. */
    echo "Unable to get a lock on target!<BR><BR>";
    mysql_query("UPDATE ships SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    playerlog($targetinfo[ship_id],"$playerinfo[character_name] attempted to attack your ship, but could not lock weapons.");
  }
  else
  {
    /* if scan succeeds, show results and inform target. */
    if($targetinfo[dev_emerwarp] > 0)
    {
      /* need to change warp destination to random sector in universe */
      $dest_sector=rand(1,$sector_max);
      mysql_query("UPDATE ships SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
      playerlog($targetinfo[ship_id],"$playerinfo[character_name] in sector $playerinfo[sector] attempted to attack your ship, Your Emergency Warp Engaged.");
      $result_warp = mysql_query ("UPDATE ships SET sector=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$targetinfo[ship_id]");
      echo "Target engaged an emergency warp device when attacked!<BR><BR>";
    }
    else
    {
      $targetbeams = round(pow($level_factor,$targetinfo[beams]))*10;
      $playerbeams = round(pow($level_factor,$playerinfo[beams]))*10;
      $playershields = round(pow($level_factor,$playerinfo[shields]))*10;
      $targetshields = round(pow($level_factor,$targetinfo[shields]))*10;
      
      $playertorpnum = round(pow($level_factor,$playerinfo[torp_launchers]))*2;
      if($playertorpnum > $playerinfo[torps])
      {
        $playertorpnum = $playerinfo[torps];
      }
      $targettorpnum = round(pow($level_factor,$targetinfo[torp_launchers]))*2;
      if($targettorpnum > $targetinfo[torps])
      {
        $targettorpnum = $targetinfo[torps];
      }
      $playertorpdmg = $torp_dmg_rate*$playertorpnum;
      $targettorpdmg = $torp_dmg_rate*$targettorpnum;
      $playerarmour = $playerinfo[armour_pts];
      $targetarmour = $targetinfo[armour_pts];
      $playerfighters = $playerinfo[ship_fighters];
      $targetfighters = $targetinfo[ship_fighters];
      $targetdestroyed = 0;
      $playerdestroyed = 0;
      echo "Attacking $targetinfo[character_name] on the $targetinfo[ship_name]:<BR><BR>";
      echo "Beams hit:<BR>";
      if($targetfighters > 0 && $playerbeams > 0)
      {
        if($playerbeams > round($targetfighters / 2))
        {
          $temp = round($targetfighters/2);
          $lost = $targetfighters-$temp;
          echo "$targetinfo[character_name] lost $lost fighters<BR>";
          $targetfighters = $temp;
          $playerbeams = $playerbeams-$lost;
        }
        else
        {
          $targetfighters = $targetfighters-$playerbeams;
          echo "$targetinfo[character_name] lost $playerbeams fighters<BR>";
          $playerbeams = 0;
        }   
      }
      if($playerfighters > 0 && $targetbeams > 0)
      {
        if($targetbeams > round($playerfighters / 2))
        {
          $temp=round($playerfighters/2);
          $lost=$playerfighters-$temp;
          echo "You lost $lost fighters<BR>";
          $playerfighters=$temp;
          $targetbeams=$targetbeams-$lost;
        }
        else
        {
          $playerfighters=$playerfighters-$targetbeams;
          echo "You lost $targetbeams fighters<BR>";
          $targetbeams=0;
        }
      }
      if($playerbeams > 0)
      {
        if($playerbeams > $targetshields)
        {
          $playerbeams=$playerbeams-$targetshields;
          $targetshields=0;
          echo "$targetinfo[character_name]'s shields are down!<BR>";
        }
        else
        {
          echo "$targetinfo[character_name]'s shields are hit for $playerbeams damage.<BR>";
          $targetshields=$targetshields-$playerbeams;
          $playerbeams=0;
        }
      }
      if($targetbeams > 0)
      {
        if($targetbeams > $playershields)
        {
          $targetbeams=$targetbeams-$playershields;
          $playershields=0;
          echo "Your shields are down!<BR>";
        }
        else
        {
          echo "Your shields are hit for $targetbeams damage.<BR>";
          $playershields=$playershields-$targetbeams;
          $targetbeams=0;
        }
      }
      if($playerbeams > 0)
      {
        if($playerbeams > $targetarmour)
        {
          $targetarmour=0;
          echo "$targetinfo[character_name]'s armour breached!<BR>";
        }
        else
        {
          $targetarmour=$targetarmour-$playerbeams;
          echo "$targetinfo[character_name]'s armour is hit for $playerbeams damage.<BR>";
        } 
      }
      if($targetbeams > 0)
      {
        if($targetbeams > $playerarmour)
        {
          $playerarmour=0;
          echo "Your armour has been breached!<BR>";
        }
        else
        {
          $playerarmour=$playerarmour-$targetbeams;
          echo "Your armour is hit for $targetbeams damage.<BR>";
        } 
      }
      echo "<BR>Torpedoes hit:<BR>";
      if($targetfighters > 0 && $playertorpdmg > 0)
      {
        if($playertorpdmg > round($targetfighters / 2))
        {
          $temp=round($targetfighters/2);
          $lost=$targetfighters-$temp;
          echo "$targetinfo[character_name] lost $lost fighters<BR>";
          $targetfighters=$temp;
          $playertorpdmg=$playertorpdmg-$lost;
        }
        else
        {
          $targetfighters=$targetfighters-$playertorpdmg;
          echo "$targetinfo[character_name] lost $playertorpdmg fighters<BR>";
          $playertorpdmg=0;
        }
      }
      if($playerfighters > 0 && $targettorpdmg > 0)
      {
        if($targettorpdmg > round($playerfighters / 2))
        {
          $temp=round($playerfighters/2);
          $lost=$playerfighters-$temp;
          echo "You lost $lost fighters<BR>";
          echo "$temp - $playerfighters - $targettorpdmg";
          $playerfighters=$temp;
          $targettorpdmg=$targettorpdmg-$lost;
        }
        else
        {
          $playerfighters=$playerfighters-$targettorpdmg;
          echo "You lost $targettorpdmg fighters<BR>";
          $targettorpdmg=0;
        }
      }
      if($playertorpdmg > 0)
      {
        if($playertorpdmg > $targetarmour)
        {
          $targetarmour=0;
          echo "$targetinfo[character_name]'s armour breached!<BR>";
        }
        else
        {
          $targetarmour=$targetarmour-$playertorpdmg;
          echo "$targetinfo[character_name]'s armour is hit for $playertorpdmg damage.<BR>";
        } 
      }
      if($targettorpdmg > 0)
      {
        if($targettorpdmg > $playerarmour)
        {
          $playerarmour=0;
          echo "Your armour has been breached!<BR>";
        }
        else
        {
          $playerarmour=$playerarmour-$targettorpdmg;
          echo "Your armour is hit for $targettorpdmg damage.<BR>";
        } 
      }
      echo "<BR>Fighters Attack:<BR>";
      if($playerfighters > 0 && $targetfighters > 0)
      {
        if($playerfighters > $targetfighters)
        {
          echo "$targetinfo[character_name] lost all fighters.<BR>";
          $temptargfighters=0;
        }
        else
        {
          echo "$targetinfo[character_name] lost $playerfighters fighters.<BR>";
          $temptargfighters=$targetfighters-$playerfighters;
        }
        if($targetfighters > $playerfighters)
        {
          echo "You lost all fighters.<BR>";
          $tempplayfighters=0;
        }
        else
        {
          echo "You lost $targetfighters fighters.<BR>";
          $tempplayfighters=$playerfighters-$targetfighters;
        }     
        $playerfighters=$tempplayfighters;
        $targetfighters=$temptargfighters;
      }
      if($playerfighters > 0)
      {
        if($playerfighters > $targetarmour)
        {
          $targetarmour=0;
          echo "$targetinfo[character_name]'s armour breached!<BR>";
        }
        else
        {
          $targetarmour=$targetarmour-$playerfighters;
          echo "$targetinfo[character_name]'s armour is hit for $playerfighters damage.<BR>";
        }
      }
      if($targetfighters > 0)
      {
        if($targetfighters > $playerarmour)
        {
          $playerarmour=0;
          echo "Your armour is breached!<BR>";
        }
        else
        {
          $playerarmour=$playerarmour-$targetfighters;
          echo "Your armour is hit for $targetfighters damage.<BR>";
        }
      }
      if($targetarmour < 1)
      {
        echo "<BR>$targetinfo[character_name]'s ship has been destroyed.<BR>";
        if($targetinfo[dev_escapepod] == "Y")
        {
          echo "An escape pod was launched!<BR><BR>";
          mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=1000,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=1,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0 WHERE ship_id=$targetinfo[ship_id]"); 
          playerlog($targetinfo[ship_id],"$playerinfo[character_name] attacked you, and destroyed your ship!  Luckily you had an escape pod!<BR><BR>"); 
        }
        else
        {
          mysql_query("UPDATE ships SET ship_destroyed='Y',sector=NULL WHERE ship_id=$targetinfo[ship_id]");
          playerlog($targetinfo[ship_id],"$playerinfo[character_name] attacked you, and destroyed your ship!<BR><BR>"); 
          mysql_query("UPDATE universe SET planet_owner=NULL,prod_ore=20.0,prod_organics=20.0,prod_goods=20.0,prod_energy=20.0,prod_fighters=10.0,prod_torp=10.0 where planet_owner=$target_info[ship_id]");
        }   
      
        if($playerarmour > 0)
        {
          $free_ore=round($targetinfo[ship_ore]/2);
          $free_organics=round($targetinfo[ship_organics]/2);
          $free_goods=round($targetinfo[ship_goods]/2);
          $free_holds=round(pow($level_factor,$playerinfo[hull]) * 100) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
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
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $targetinfo[hull]))+round(pow($upgrade_factor, $targetinfo[engines]))+round(pow($upgrade_factor, $targetinfo[power]))+round(pow($upgrade_factor, $targetinfo[computer]))+round(pow($upgrade_factor, $targetinfo[sensors]))+round(pow($upgrade_factor, $targetinfo[beams]))+round(pow($upgrade_factor, $targetinfo[torp_launchers]))+round(pow($upgrade_factor, $targetinfo[shields]))+round(pow($upgrade_factor, $targetinfo[armour]))+round(pow($upgrade_factor, $targetinfo[cloak])));
          $ship_salvage_rate=rand(0,10);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;
          echo "You salvaged $salv_ore units of ore, $salv_organics units of organics, $salv_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits<BR>";
          $update3 = mysql_query ("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
          $armour_lost=$playerinfo[armour_pts]-$playerarmour;
          $fighters_lost=$playerinfo[ship_fighters]-$playerfighters;  
          $update3b = mysql_query ("UPDATE ships SET ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$playertorpnum, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
          echo "You lost $armour_lost armour points, $fighters_lost fighters, and used $playertorpnum torpedoes.<BR><BR>";  
        }
      }
      else
      {
        echo "You did not destory $targetinfo[character_name]'s ship.<BR>";
        $armour_lost=$targetinfo[armour_pts]-$targetarmour;
        $fighters_lost=$targetinfo[ship_fighters]-$targetfighters;
        playerlog($targetinfo[ship_id],"$playerinfo[character_name] attacked you.  You lost $armour_lost points of armour and $fighters_lost fighters.<BR><BR>");
        $update4 = mysql_query ("UPDATE ships SET ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$targettorpnum WHERE ship_id=$targetinfo[ship_id]");
        $armour_lost=$playerinfo[armour_pts]-$playerarmour;
        $fighters_lost=$playerinfo[ship_fighters]-$playerfighters;  
        $update4b = mysql_query ("UPDATE ships SET ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$playertorpnum, turns=turns-1 WHERE ship_id=$playerinfo[ship_id]");
        echo "You lost $armour_lost armour points, $fighters_lost fighters, and used $playertorpnum torpedoes.<BR><BR>";
      }
      if($playerarmour < 1)
      {
        echo "Your ship has been destroyed!<BR><BR>";
        if($playerinfo[dev_escapepod] == "Y")
        {
          echo "Luckily you have an escape pod!<BR><BR>";
          mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=1000,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=1,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0 WHERE ship_id=$targetinfo[ship_id]"); 
        }
        else
        {
          mysql_query("UPDATE ships SET ship_destroyed='Y',sector=NULL WHERE ship_id=$playerinfo[ship_id]"); 
          mysql_query("UPDATE universe SET planet_owner=NULL,prod_ore=20.0,prod_organics=20.0,prod_goods=20.0,prod_energy=20.0,prod_fighters=10.0,prod_torp=10.0 where planet_owner=$target_info[ship_id]");
        }         
        if($targetarmour > 0)
        {
          $free_ore=round($playerinfo[ship_ore]/2);
          $free_organics=round($playerinfo[ship_organics]/2);
          $free_goods=round($playerinfo[ship_goods]/2);
          $free_holds=round(pow($level_factor,$targetinfo[hull]) * 100) - $targetinfo[ship_ore] - $targetinfo[ship_organics] - $targetinfo[ship_goods] - $targetinfo[ship_colonists];
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
          $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $playerinfo[hull]))+round(pow($upgrade_factor, $playerinfo[engines]))+round(pow($upgrade_factor, $playerinfo[power]))+round(pow($upgrade_factor, $playerinfo[computer]))+round(pow($upgrade_factor, $playerinfo[sensors]))+round(pow($upgrade_factor, $playerinfo[beams]))+round(pow($upgrade_factor, $playerinfo[torp_launchers]))+round(pow($upgrade_factor, $playerinfo[shields]))+round(pow($upgrade_factor, $playerinfo[armour]))+round(pow($upgrade_factor, $playerinfo[cloak])));
          $ship_salvage_rate=rand(0,10);
          $ship_salvage=$ship_value*$ship_salvage_rate/100;
          echo "$targetinfo[character_name] salvaged $salv_ore units of ore, $salv_organics units of organics, $salv_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits<BR>";
          $update6 = mysql_query ("UPDATE ships SET credits=credits+$ship_salvage, ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods WHERE ship_id=$targetinfo[ship_id]");
          $armour_lost=$targetinfo[armour_pts]-$targetarmour;
          $fighters_lost=$targetinfo[ship_fighters]-$targetfighters;
          $update6b = mysql_query ("UPDATE ships SET ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$targettorpnum WHERE ship_id=$targetinfo[ship_id]");
        } 
      }
    }
  }
}

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

echo "Click <a href=main.php3>here</a> to return to Main Menu.";

include("footer.php3");
  
?> 
