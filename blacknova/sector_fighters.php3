<?
                    echo "Sector defence fighters are attacking you!<BR>";
                    $targetfighters = $total_sector_fighters;
     	            $playerbeams = NUM_BEAMS($playerinfo[beams]);
                    if($calledfrom == 'rsmove.$phpext')
                    {
                       $playerinfo[ship_enery] += $energyscooped;
                    }
                    if($playerbeams>$playerinfo[ship_energy])
                    {
                       $playerbeams=$playerinfo[ship_energy];
                    }
                    $playerinfo[ship_energy]=$playerinfo[ship_energy]-$playerbeams;
                    $playershields = NUM_SHIELDS($playerinfo[shields]);
                    if($playershields>$playerinfo[ship_energy])
                    {  
                       $playershields=$playerinfo[ship_energy];
                    }
//                    $playerinfo[ship_energy]=$playerinfo[ship_energy]-$playershields;
                    $playertorpnum = round(pow($level_factor,$playerinfo[torp_launchers]))*2;
                    if($playertorpnum > $playerinfo[torps])
                    { 
                       $playertorpnum = $playerinfo[torps];
                    }
                    $playertorpdmg = $torp_dmg_rate*$playertorpnum;
                    $playerarmour = $playerinfo[armour_pts];
                    $playerfighters = $playerinfo[ship_fighters];
                    if($targetfighters > 0 && $playerbeams > 0)
                    {
                       if($playerbeams > round($targetfighters / 2))
                       {
                          $temp = round($targetfighters/2);
                          $lost = $targetfighters-$temp;
                          echo "Your beams destroyed $lost fighters<BR>";
                          $targetfighters = $temp;
                          $playerbeams = $playerbeams-$lost;
                       }
                       else
                       {
                          $targetfighters = $targetfighters-$playerbeams;
                          echo "Your beams destroyed $playerbeams fighters<BR>";
                          $playerbeams = 0;
                       }   
                   }
                   echo "<BR>Torpedoes hit:<BR>";
                   if($targetfighters > 0 && $playertorpdmg > 0)
                   {
                      if($playertorpdmg > round($targetfighters / 2))
                      {
                         $temp=round($targetfighters/2);
                         $lost=$targetfighters-$temp;
                         echo "Your torpedoes destroyed $lost fighters<BR>";
                         $targetfighters=$temp;
                         $playertorpdmg=$playertorpdmg-$lost;
                      }
                      else
                      {
                         $targetfighters=$targetfighters-$playertorpdmg;
                         echo "Your torpedoes destroyed $playertorpdmg fighters<BR>";
                         $playertorpdmg=0;
                      }
                  }
                  echo "<BR>Fighters Attack:<BR>";
                  if($playerfighters > 0 && $targetfighters > 0)
                  {
                     if($playerfighters > $targetfighters)
                     {
                        echo "You destroyed all the fighters.<BR>";
                        $temptargfighters=0;
                     }
                     else
                     {
                        echo "You destroyed $playerfighters fighters.<BR>";
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
                 $fighterslost = $total_sector_fighters - $targetfighters;
                 destroy_fighters($sector,$fighterslost);
                 message_defence_owner($sector,"$playerinfo[character_name] destroyed $fighterslost sector defence fighters in sector $sector.");
                 playerlog($playerinfo[ship_id],"You destroyed $fighterslost sector defence fighters in sector $sector.");
                 $armour_lost=$playerinfo[armour_pts]-$playerarmour;
                 $fighters_lost=$playerinfo[ship_fighters]-$playerfighters;
                 $energy=$playerinfo[ship_energy];
                 $update4b = mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armour_pts=armour_pts-$armour_lost, torps=torps-$playertorpnum WHERE ship_id=$playerinfo[ship_id]");
                 echo "You lost $armour_lost armour points, $fighters_lost fighters, and used $playertorpnum torpedoes.<BR><BR>";
                 if($playerarmour < 1)
                 {
                    echo "Your ship has been destroyed!<BR><BR>";
                    playerlog($playerinfo[ship_id],"Your ship was destroyed by sector defence fighters in sector $sector.");
                    message_defence_owner($sector,"Sector defence fighters destroyed $playerinfo[character_name] in sector $sector.");
                    if($playerinfo[dev_escapepod] == "Y")
                    {
                       $rating=round($playerinfo[rating]/2);
                       echo "Luckily you have an escape pod!<BR><BR>";
                       mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating' WHERE ship_id=$playerinfo[ship_id]"); 
                       $ok=0;
                       TEXT_GOTOMAIN();
                       die();

                    }
                    else
                    { 
                       db_kill_player($playerinfo['ship_id']);
                       $ok=0;
                       TEXT_GOTOMAIN();
                       die();
                    }         
                 }
                 if($targetfighters > 0)
                    $ok=0;
                 else
                    $ok=2;
?>
