<?

function furangeetoship($ship_id)
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $attackerbeams;
  global $attackerfighters;
  global $attackershields;
  global $attackertorps;
  global $attackerarmor;
  global $attackertorpdamage;
  global $start_energy;
  global $playerinfo;

  // *********************************
  // *** LOOKUP TARGET DETAILS    ****
  // *********************************
  mysql_query("LOCK TABLES ships WRITE, universe WRITE, zones READ");
  $resultt = mysql_query ("SELECT * FROM ships WHERE ship_id='$ship_id'");
  $targetinfo=mysql_fetch_array($resultt);

  // *********************************
  // *** SETUP ATTACKER VARIABLES ****
  // *********************************
  $attackerbeams = NUM_BEAMS($playerinfo[beams]);
  if ($attackerbeams > $playerinfo[ship_energy]) $attackerbeams = $playerinfo[ship_energy];
  $playerinfo[ship_energy] = $playerinfo[ship_energy] - $attackerbeams;
  $attackershields = NUM_SHIELDS($playerinfo[shields]);
  if ($attackershields > $playerinfo[ship_energy]) $attackershields = $playerinfo[ship_energy];
  $playerinfo[ship_energy] = $playerinfo[ship_energy] - $attackershields;
  $attackertorps = round(pow($level_factor, $playerinfo[torp_launchers])) * 2;
  if ($attackertorps > $playerinfo[torps]) $attackertorps = $playerinfo[torps];
  $playerinfo[torps] = $playerinfo[torps] - $attackertorps;
  $attackertorpdamage = $torp_dmg_rate * $attackertorps;
  $attackerarmor = $playerinfo[armour_pts];
  $attackerfighters = $playerinfo[ship_fighters];
  $playerdestroyed = 0;

  // *********************************
  // **** SETUP TARGET VARIABLES *****
  // *********************************
  $targetbeams = NUM_BEAMS($targetinfo[beams]);
  if ($targetbeams>$targetinfo[ship_energy]) $targetbeams=$targetinfo[ship_energy];
  $targetinfo[ship_energy]=$targetinfo[ship_energy]-$targetbeams;
  $targetshields = NUM_SHIELDS($targetinfo[shields]);
  if ($targetshields>$targetinfo[ship_energy]) $targetshields=$targetinfo[ship_energy];
  $targetinfo[ship_energy]=$targetinfo[ship_energy]-$targetshields;
  $targettorpnum = round(pow($level_factor,$targetinfo[torp_launchers]))*2;
  if ($targettorpnum > $targetinfo[torps]) $targettorpnum = $targetinfo[torps];
  $targetinfo[torps] = $targetinfo[torps] - $targettorpnum;
  $targettorpdmg = $torp_dmg_rate*$targettorpnum;
  $targetarmor = $targetinfo[armour_pts];
  $targetfighters = $targetinfo[ship_fighters];
  $targetdestroyed = 0;

  // *********************************
  // **** BEGIN COMBAT PROCEDURES ****
  // *********************************
  if($attackerbeams > 0 && $targetfighters > 0)
  {                                                  //******** ATTACKER HAS BEAMS - TARGET HAS FIGHTERS - BEAMS VS FIGHTERS ********
    if($attackerbeams > round($targetfighters / 2))
    {                                                           //****** ATTACKER BEAMS GT HALF TARGET FIGHTERS ******
      $lost = $targetfighters-(round($targetfighters/2));
      $targetfighters = $targetfighters-$lost;                           //**** TARGET LOOSES HALF ALL FIGHTERS ****
      $attackerbeams = $attackerbeams-$lost;                             //**** ATTACKER LOOSES BEAMS EQ TO HALF TARGET FIGHTERS ****
    } else
    {                                                           //****** ATTACKER BEAMS LE HALF TARGET FIGHTERS ******
      $targetfighters = $targetfighters-$attackerbeams;                  //**** TARGET LOOSES FIGHTERS EQ TO ATTACKER BEAMS ****
      $attackerbeams = 0;                                                //**** ATTACKER LOOSES ALL BEAMS ****
    }   
  }
  if($attackerfighters > 0 && $targetbeams > 0)
  {                                                  //******** TARGET HAS BEAMS - ATTACKER HAS FIGHTERS - BEAMS VS FIGHTERS ********
    if($targetbeams > round($attackerfighters / 2))
    {                                                           //****** TARGET BEAMS GT HALF ATTACKER FIGHTERS ******
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;                         //**** ATTACKER LOOSES HALF ALL FIGHTERS ****
      $targetbeams=$targetbeams-$lost;                                   //**** TARGET LOOSES BEAMS EQ TO HALF ATTACKER FIGHTERS ****
    } else
    {                                                           //****** TARGET BEAMS LE HALF ATTACKER FIGHTERS ******
      $attackerfighters=$attackerfighters-$targetbeams;                  //**** ATTACKER LOOSES FIGHTERS EQ TO TARGET BEAMS **** 
      $targetbeams=0;                                                    //**** TARGET LOOSES ALL BEAMS ****
    }
  }
  if($attackerbeams > 0)
  {                                                  //******** ATTACKER HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS ********
    if($attackerbeams > $targetshields)
    {                                                           //****** ATTACKER BEAMS GT TARGET SHIELDS ******
      $attackerbeams=$attackerbeams-$targetshields;                      //**** ATTACKER LOOSES BEAMS EQ TO TARGET SHIELDS ****
      $targetshields=0;                                                  //**** TARGET LOOSES ALL SHIELDS ****
    } else
    {                                                           //****** ATTACKER BEAMS LE TARGET SHIELDS ******
      $targetshields=$targetshields-$attackerbeams;                      //**** TARGET LOOSES SHIELDS EQ TO ATTACKER BEAMS ****
      $attackerbeams=0;                                                  //**** ATTACKER LOOSES ALL BEAMS ****
    }
  }
  if($targetbeams > 0)
  {                                                  //******** TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS ********
    if($targetbeams > $attackershields)
    {                                                           //****** TARGET BEAMS GT ATTACKER SHIELDS ******
      $targetbeams=$targetbeams-$attackershields;                        //**** TARGET LOOSES BEAMS EQ TO ATTACKER SHIELDS ****
      $attackershields=0;                                                //**** ATTACKER LOOSES ALL SHIELDS ****
    } else
    {                                                           //****** TARGET BEAMS LE ATTACKER SHIELDS ****** 
      $attackershields=$attackershields-$targetbeams;                    //**** ATTACKER LOOSES SHIELDS EQ TO TARGET BEAMS ****
      $targetbeams=0;                                                    //**** TARGET LOOSES ALL BEAMS ****
    }
  }
  if($attackerbeams > 0)
  {                                                  //******** ATTACKER HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS ARMOR ********
    if($attackerbeams > $targetarmor)
    {                                                           //****** ATTACKER BEAMS GT TARGET ARMOR ******
      $attackerbeams=$attackerbeams-$targetarmor;                        //**** ATTACKER LOOSES BEAMS EQ TO TARGET ARMOR ****
      $targetarmor=0;                                                    //**** TARGET LOOSES ALL ARMOR (TARGET SHIP DESTROYED) ****
    } else
    {                                                           //****** ATTACKER BEAMS LE TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackerbeams;                          //**** TARGET LOOSES ARMORS EQ TO ATTACKER BEAMS ****
      $attackerbeams=0;                                                  //**** ATTACKER LOOSES ALL BEAMS ****
    } 
  }
  if($targetbeams > 0)
  {                                                 //******** TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS ARMOR ******** 
    if($targetbeams > $attackerarmor)
    {                                                          //****** TARGET BEAMS GT ATTACKER ARMOR ******
      $targetbeams=$targetbeams-$attackerarmor;                         //**** TARGET LOOSES BEAMS EQ TO ATTACKER ARMOR ****
      $attackerarmor=0;                                                 //**** ATTACKER LOOSES ALL ARMOR (ATTACKER SHIP DESTROYED) ****
    } else
    {                                                          //****** TARGET BEAMS LE ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targetbeams;                       //**** ATTACKER LOOSES ARMOR EQ TO TARGET BEAMS ****
      $targetbeams=0;                                                   //**** TARGET LOOSES ALL BEAMS ****
    } 
  }
  if($targetfighters > 0 && $attackertorpdamage > 0)
  {                                                 //******** ATTACKER FIRES TORPS - TARGET HAS FIGHTERS - TORPS VS FIGHTERS ********
    if($attackertorpdamage > round($targetfighters / 2))
    {                                                          //****** ATTACKER FIRED TORPS GT HALF TARGET FIGHTERS ******
      $lost=$targetfighters-(round($targetfighters/2));
      $targetfighters=$targetfighters-$lost;                            //**** TARGET LOOSES HALF ALL FIGHTERS ****
      $attackertorpdamage=$attackertorpdamage-$lost;                    //**** ATTACKER LOOSES FIRED TORPS EQ TO HALF TARGET FIGHTERS ****
    } else
    {                                                          //****** ATTACKER FIRED TORPS LE HALF TARGET FIGHTERS ******
      $targetfighters=$targetfighters-$attackertorpdamage;              //**** TARGET LOOSES FIGHTERS EQ TO ATTACKER TORPS FIRED ****
      $attackertorpdamage=0;                                            //**** ATTACKER LOOSES ALL TORPS FIRED ****
    }
  }
  if($attackerfighters > 0 && $targettorpdmg > 0)
  {                                                 //******** TARGET FIRES TORPS - ATTACKER HAS FIGHTERS - TORPS VS FIGHTERS ********
    if($targettorpdmg > round($attackerfighters / 2))
    {                                                          //****** TARGET FIRED TORPS GT HALF ATTACKER FIGHTERS ******
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;                        //**** ATTACKER LOOSES HALF ALL FIGHTERS ****
      $targettorpdmg=$targettorpdmg-$lost;                              //**** TARGET LOOSES FIRED TORPS EQ TO HALF ATTACKER FIGHTERS ****
    } else
    {                                                          //****** TARGET FIRED TORPS LE HALF ATTACKER FIGHTERS ******
      $attackerfighters=$attackerfighters-$targettorpdmg;               //**** ATTACKER LOOSES FIGHTERS EQ TO TARGET TORPS FIRED ****
      $targettorpdmg=0;                                                 //**** TARGET LOOSES ALL TORPS FIRED ****
    }
  }
  if($attackertorpdamage > 0)
  {                                                 //******** ATTACKER FIRES TORPS - CONTINUE COMBAT - TORPS VS ARMOR ********
    if($attackertorpdamage > $targetarmor)
    {                                                          //****** ATTACKER FIRED TORPS GT HALF TARGET ARMOR ******
      $attackertorpdamage=$attackertorpdamage-$targetarmor;             //**** ATTACKER LOOSES FIRED TORPS EQ TO TARGET ARMOR ****
      $targetarmor=0;                                                   //**** TARGET LOOSES ALL ARMOR (TARGET SHIP DESTROYED) ****
    } else
    {                                                          //****** ATTACKER FIRED TORPS LE HALF TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackertorpdamage;                    //**** TARGET LOOSES ARMOR EQ TO ATTACKER TORPS FIRED ****
      $attackertorpdamage=0;                                            //**** ATTACKER LOOSES ALL TORPS FIRED ****
    } 
  }
  if($targettorpdmg > 0)
  {                                                 //******** TARGET FIRES TORPS - CONTINUE COMBAT - TORPS VS ARMOR ********
    if($targettorpdmg > $attackerarmor)
    {                                                          //****** TARGET FIRED TORPS GT HALF ATTACKER ARMOR ******
      $targettorpdmg=$targettorpdmg-$attackerarmor;                     //**** TARGET LOOSES FIRED TORPS EQ TO ATTACKER ARMOR ****
      $attackerarmor=0;                                                 //**** ATTACKER LOOSES ALL ARMOR (ATTACKER SHIP DESTROYED) ****
    } else
    {                                                          //****** TARGET FIRED TORPS LE HALF ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targettorpdmg;                     //**** ATTACKER LOOSES ARMOR EQ TO TARGET TORPS FIRED ****
      $targettorpdmg=0;                                                 //**** TARGET LOOSES ALL TORPS FIRED ****
    } 
  }
  if($attackerfighters > 0 && $targetfighters > 0)
  {                                                 //******** ATTACKER HAS FIGHTERS - TARGET HAS FIGHTERS - FIGHTERS VS FIGHTERS ********
    if($attackerfighters > $targetfighters)
    {                                                          //****** ATTACKER FIGHTERS GT TARGET FIGHTERS ******
      $temptargfighters=0;                                              //**** TARGET WILL LOOSE ALL FIGHTERS ****
    } else
    {                                                          //****** ATTACKER FIGHTERS LE TARGET FIGHTERS ******
      $temptargfighters=$targetfighters-$attackerfighters;              //**** TARGET WILL LOOSE FIGHTERS EQ TO ATTACKER FIGHTERS ****
    }
    if($targetfighters > $attackerfighters)
    {                                                          //****** TARGET FIGHTERS GT ATTACKER FIGHTERS ******
      $tempplayfighters=0;                                              //**** ATTACKER WILL LOOSE ALL FIGHTERS ****
    } else
    {                                                          //****** TARGET FIGHTERS LE ATTACKER FIGHTERS ******
      $tempplayfighters=$attackerfighters-$targetfighters;              //**** ATTACKER WILL LOOSE FIGHTERS EQ TO TARGET FIGHTERS ****
    }     
    $attackerfighters=$tempplayfighters;
    $targetfighters=$temptargfighters;
  }
  if($attackerfighters > 0)
  {                                                 //******** ATTACKER HAS FIGHTERS - CONTINUE COMBAT - FIGHTERS VS ARMOR ********
    if($attackerfighters > $targetarmor)
    {                                                          //****** ATTACKER FIGHTERS GT TARGET ARMOR ******
      $targetarmor=0;                                                   //**** TARGET LOOSES ALL ARMOR (TARGET SHIP DESTROYED) ****
    } else
    {                                                          //****** ATTACKER FIGHTERS LE TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackerfighters;                      //**** TARGET LOOSES ARMOR EQ TO ATTACKER FIGHTERS **** 
    }
  }
  if($targetfighters > 0)
  {                                                 //******** TARGET HAS FIGHTERS - CONTINUE COMBAT - FIGHTERS VS ARMOR ********
    if($targetfighters > $attackerarmor)
    {                                                          //****** TARGET FIGHTERS GT ATTACKER ARMOR ******
      $attackerarmor=0;                                                 //**** ATTACKER LOOSES ALL ARMOR (ATTACKER SHIP DESTROYED) ****
    } else
    {                                                          //****** TARGET FIGHTERS LE ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targetfighters;                    //**** ATTACKER LOOSES ARMOR EQ TO TARGET FIGHTERS ****
    }
  }

  // *********************************
  // **** FIX NEGATIVE VALUE VARS ****
  // *********************************
  if ($attackerfighters < 0) $attackerfighters = 0;
  if ($attackertorps    < 0) $attackertorps = 0;
  if ($attackershields  < 0) $attackershields = 0;
  if ($attackerbeams    < 0) $attackerbeams = 0;
  if ($attackerarmor    < 0) $attackerarmor = 0;
  if ($targetfighters   < 0) $targetfighters = 0;
  if ($targettorpnum    < 0) $targettorpnum = 0;
  if ($targetshields    < 0) $targetshields = 0;
  if ($targetbeams      < 0) $targetbeams = 0;
  if ($targetarmor      < 0) $targetarmor = 0;

  // *********************************
  // *** DEAL WITH DESTROYED SHIPS ***
  // *********************************
  if($targetarmor < 1)
  {                                                 //******** TARGET SHIP WAS DESTROYED ********
    if($targetinfo[dev_escapepod] == "Y")
    {                                                          //****** TARGET HAD ESCAPE POD ******
      $rating=round($targetinfo[rating]/2);
      mysql_query("UPDATE ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage='', on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N', rating='$rating' where ship_id=$targetinfo[ship_id]");
      playerlog($targetinfo[ship_id],"$playerinfo[character_name] attacked you, and destroyed your ship!  Luckily you had an escape pod!<BR><BR>"); 
    } else
    {
      playerlog($targetinfo[ship_id],"$playerinfo[character_name] attacked you, and destroyed your ship!<BR><BR>"); 
      db_kill_player($targetinfo['ship_id']);
    }   
    if($attackerarmor > 0)
    {                                                          //****** ATTACKER STILL ALIVE TO SALVAGE TRAGET ******
      $rating_change=round($targetinfo[rating]*$rating_combat_factor);
      $free_ore = round($targetinfo[ship_ore]/2);
      $free_organics = round($targetinfo[ship_organics]/2);
      $free_goods = round($targetinfo[ship_goods]/2);
      $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      if($free_holds > $free_goods) 
      {                                                        //****** FIGURE OUT WHAT WE CAN CARRY ******
        $salv_goods=$free_goods;
        $free_holds=$free_holds-$free_goods;
      } elseif($free_holds > 0)
      {
        $salv_goods=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_goods=0;
      }
      if($free_holds > $free_ore)
      {
        $salv_ore=$free_ore;
        $free_holds=$free_holds-$free_ore;
      } elseif($free_holds > 0)
      {
        $salv_ore=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_ore=0;
      }
      if($free_holds > $free_organics)
      {
        $salv_organics=$free_organics;
        $free_holds=$free_holds-$free_organics;
      } elseif($free_holds > 0)
      {
        $salv_organics=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_organics=0;
      }
      $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $targetinfo[hull]))+round(pow($upgrade_factor, $targetinfo[engines]))+round(pow($upgrade_factor, $targetinfo[power]))+round(pow($upgrade_factor, $targetinfo[computer]))+round(pow($upgrade_factor, $targetinfo[sensors]))+round(pow($upgrade_factor, $targetinfo[beams]))+round(pow($upgrade_factor, $targetinfo[torp_launchers]))+round(pow($upgrade_factor, $targetinfo[shields]))+round(pow($upgrade_factor, $targetinfo[armor]))+round(pow($upgrade_factor, $targetinfo[cloak])));
      $ship_salvage_rate=rand(10,20);
      $ship_salvage=$ship_value*$ship_salvage_rate/100;
      playerlog($playerinfo[ship_id],"Attack successful, $targetinfo[character_name] was defeated and salvaged for $ship_salvage credits.<BR>"); 
      mysql_query ("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
      $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
      $fighters_lost = $playerinfo[ship_fighters] - $attackerfighters;
      $energy=$playerinfo[ship_energy];
      mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps,armour_pts=armour_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
    } else
    {
      playerlog($playerinfo[ship_id],"Attack failed, $targetinfo[character_name] was defeated but you died in the process.<BR>"); 
    }
  }
  elseif($targetarmor > 0 && $attackerarmor > 0)
  {                                                 //******** ATTACKER AND DEFENDER BOTH SURVIVED ********
    $rating_change=round($targetinfo[rating]*.1);
    $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
    $fighters_lost = $playerinfo[ship_fighters] - $attackerfighters;
    $energy=$playerinfo[ship_energy];
    $target_rating_change=round($targetinfo[rating]/2);
    $target_armor_lost=$targetinfo[armour_pts]-$targetarmor;
    $target_fighters_lost=$targetinfo[ship_fighters]-$targetfighters;
    $target_energy=$targetinfo[ship_energy];
    playerlog($playerinfo[ship_id],"Attack failed, $targetinfo[character_name] survived.<BR>"); 
    playerlog($targetinfo[ship_id],"The Furangee $playerinfo[character_name] attacked you.  You lost $target_armor_lost points of armor and $target_fighters_lost fighters.<BR><BR>");
    mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps,armour_pts=armour_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
    mysql_query ("UPDATE ships SET ship_energy=$target_energy,ship_fighters=ship_fighters-$target_fighters_lost, armour_pts=armour_pts-$target_armor_lost, torps=torps-$targettorpnum, rating=$target_rating WHERE ship_id=$targetinfo[ship_id]");
  }
  elseif($attackerarmor < 1)
  {                                                 //******** ATTACKER SHIP WAS DESTROYED ********
    playerlog($playerinfo[ship_id],"$targetinfo[character_name] destroyed your ship!<BR>"); 
    db_kill_player($playerinfo['ship_id']);
    $rating_change=round($playerinfo[rating]*$rating_combat_factor);
    $free_ore = round($playerinfo[ship_ore]/2);
    $free_organics = round($playerinfo[ship_organics]/2);
    $free_goods = round($playerinfo[ship_goods]/2);
    $free_holds = NUM_HOLDS($targetinfo[hull]) - $targetinfo[ship_ore] - $targetinfo[ship_organics] - $targetinfo[ship_goods] - $targetinfo[ship_colonists];
    if($free_holds > $free_goods) 
    {                                                          //****** FIGURE OUT WHAT WE CAN CARRY ******
      $salv_goods=$free_goods;
      $free_holds=$free_holds-$free_goods;
    } elseif($free_holds > 0)
    {
      $salv_goods=$free_holds;
      $free_holds=0;
    } else
    {
      $salv_goods=0;
    }
    if($free_holds > $free_ore)
    {
      $salv_ore=$free_ore;
      $free_holds=$free_holds-$free_ore;
    } elseif($free_holds > 0)
    {
      $salv_ore=$free_holds;
      $free_holds=0;
    } else
    {
      $salv_ore=0;
    }
    if($free_holds > $free_organics)
    {
      $salv_organics=$free_organics;
      $free_holds=$free_holds-$free_organics;
    } elseif($free_holds > 0)
    {
      $salv_organics=$free_holds;
      $free_holds=0;
    } else
    {
      $salv_organics=0;
    }
    $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $playerinfo[hull]))+round(pow($upgrade_factor, $playerinfo[engines]))+round(pow($upgrade_factor, $playerinfo[power]))+round(pow($upgrade_factor, $playerinfo[computer]))+round(pow($upgrade_factor, $playerinfo[sensors]))+round(pow($upgrade_factor, $playerinfo[beams]))+round(pow($upgrade_factor, $playerinfo[torp_launchers]))+round(pow($upgrade_factor, $playerinfo[shields]))+round(pow($upgrade_factor, $playerinfo[armor]))+round(pow($upgrade_factor, $playerinfo[cloak])));
    $ship_salvage_rate=rand(10,20);
    $ship_salvage=$ship_value*$ship_salvage_rate/100;
    playerlog($targetinfo[ship_id],"You were attacked by Furangee $playerinfo[character_name] and you destroyed thier ship.<BR>");
    playerlog($targetinfo[ship_id],"You salvaged $salv_ore units of ore, $salv_organics units of organics, $salv_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.<BR>");
    mysql_query ("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
    $target_armor_lost = $targetinfo[armour_pts] - $targetarmor;
    $target_fighters_lost = $targetinfo[ship_fighters] - $targetfighters;
    $target_energy=$targetinfo[ship_energy];
    mysql_query ("UPDATE ships SET ship_energy=$target_energy,ship_fighters=ship_fighters-$target_fighters_lost, armour_pts=armour_pts-$target_armor_lost, torps=torps-$targettorpnum, rating=rating-$rating_change WHERE ship_id=$targetinfo[ship_id]");
  }

  // *********************************
  // *** END OF FURANGEETOSHIP SUB ***
  // *********************************
  mysql_query("UNLOCK TABLES");
}


//Retrieve the user and ship information
//$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
//Put the player information into the array: "playerinfo"
//$playerinfo=mysql_fetch_array($result);
//Retrieve all the sector information about the current sector
//$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
//Put the sector information into the array "sectorinfo"
//$sectorinfo=mysql_fetch_array($result2);
//Retrive all the warp links out of the current sector
//$result3 = mysql_query ("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");
//$i=0;
//$flag=0;
//if ($result3>0)
//{
//    //loop through the available warp links to make sure it's a valid move
//    while ($row = mysql_fetch_array($result3))
//    {
//        if ($row[link_dest]==$sector && $row[link_start]==$playerinfo[sector])
//        {
//            $flag=1;
//        }
//        $i++;
//    }
//}
//Check if there was a valid warp link to move to
//if ($flag==1)
//{
//    $ok=1;
//    $calledfrom = "move.php3";
//    include("check_fighters.php3");
//    if($ok==1){
//      $stamp = date("Y-m-d H-i-s");
//      $query="UPDATE ships SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$sector where ship_id=$playerinfo[ship_id]";
//       $move_result = mysql_query ("$query");
//  	if (!$move_result)
//	{
//		$error = mysql_error($move_result);
//	mail ("harwoodr@cgocable.net","Move Error", "Start Sector: $sectorinfo[sector_id]\nEnd Sector: $sector\nPlayer: $playerinfo[character_name] - $playerinfo[ship_id]\n\nQuery:  $query\n\nMySQL error: $error");
//	}
//    }
//    include("check_mines.php3");
//    if ($ok==1) {echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface\">";} else
//    {
//        TEXT_GOTOMAIN();
//    }
//}
//else
//{
//    echo "Move failed!<BR><BR>";
//    TEXT_GOTOMAIN();
//}

?>

