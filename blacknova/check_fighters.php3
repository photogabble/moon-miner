<?
    $result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$sector'");
    //Put the sector information into the array "sectorinfo"
    $sectorinfo=mysql_fetch_array($result2);
    mysql_free_result($result2);
    if ($sectorinfo[fighters] > 0 && $sectorinfo[fm_owner] != $playerinfo[ship_id])
    {
        // find out if the fighter owner and player are on the same team
	$result2 = mysql_query("SELECT * from ships where ship_id=$sectorinfo[fm_owner]");
        $fighters_owner = mysql_fetch_array($result2);
        mysql_free_result($result2);
        if ($fighter_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
        {
           if($sectorinfo[fm_setting] == "toll")
           {
              switch($response) {
                 case "fight":
                    bigtitle();
                    echo "Sector defence fighters are attacking you!<BR>";
                    $targetfighters = $sectorinfo[fighters];
     	            $playerbeams = NUM_BEAMS($playerinfo[beams]);
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
                    $playerinfo[ship_energy]=$playerinfo[ship_energy]-$playershields;
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
                 if($playerarmour < 1)
                 {
                    echo "Your ship has been destroyed!<BR><BR>";
                    if($playerinfo[dev_escapepod] == "Y")
                    {
                       $rating=round($playerinfo[rating]/2);
                       echo "Luckily you have an escape pod!<BR><BR>";
                       mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating' WHERE ship_id=$playerinfo[ship_id]"); 
                       $ok=0;
                    }
                    else
                    { 
                       db_kill_player($playerinfo['ship_id']);
                       $ok=0;
                    }         
                 }
                 $ok=1;
                    break;
                 case "retreat":
                    echo "Move failed.<BR>";
                    // undo the move
                    mysql_query("UPDATE ships SET sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
                    $ok=0;
                    break;
                 case "pay":      
                    $fighterstoll = $sectorinfo[fighters] * $fighter_price * 0.6;
                    if($playerinfo[credits] < $fighterstoll) 
                    {
                       echo "You do not have enough credits to pay the toll.<BR>";
                       echo "Move failed.<BR>";
                       // undo the move
                       mysql_query("UPDATE ships SET sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
                       $ok=0;
                    }
                    else
                    {
                       $tollstring = NUMBER($fighterstoll);
                       echo "You paid $tollstring credits for the toll.<BR>";
                       mysql_query("UPDATE ships SET credits=credits-$fighterstoll where ship_id=$playerinfo[ship_id]");
                       mysql_query("UPDATE ships SET credits=credits+$fighterstoll where ship_id=$sectorinfo[fm_owner]");
                       playerlog($sectorinfo[fm_owner],"$playerinfo[character_name] paid you $tollstring for entry to sector $sector.");
                       playerlog($playerinfo[ship_id],"You paid $tollstring for entry to sector $sector.");
                       $ok=1;
                    }
                    break;
                 default:
                    $fighterstoll = $sectorinfo[fighters] * $fighter_price * 0.6;
                    bigtitle();
                    echo "<FORM ACTION=$calledfrom METHOD=POST>";
                    echo "There are $sectorinfo[fighters] fighters in this sector.<br>";
                    echo "They demand " . NUMBER($fighterstoll) . " credits to enter this sector.<BR>";    
                    echo "You can <INPUT TYPE=RADIO NAME=response VALUE=retreat>Retreat</INPUT>"; 
                    echo "<INPUT TYPE=RADIO NAME=response CHECKED VALUE=pay>Pay</INPUT>";
                    echo "<INPUT TYPE=RADIO NAME=response CHECKED VALUE=fight>Fight</INPUT><BR>";
                    echo "<INPUT TYPE=SUBMIT VALUE=Go><BR><BR>";
                    echo "<input type=hidden name=sector value=$sector>";
                    echo "</FORM>";
                    die();
                    break;
              }

           }
           else
           {
             // Fighters are in attack mode
             $ok=0;
             bigtitle();
             echo "Sector defence fighters are attacking you!<BR>";
             $targetfighters = $sectorinfo[fighters];
	     $playerbeams = NUM_BEAMS($playerinfo[beams]);
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
             $playerinfo[ship_energy]=$playerinfo[ship_energy]-$playershields;
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
             if($playerarmour < 1)
             {
                echo "Your ship has been destroyed!<BR><BR>";
                if($playerinfo[dev_escapepod] == "Y")
                {
                   $rating=round($playerinfo[rating]/2);
                   echo "Luckily you have an escape pod!<BR><BR>";
                   mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating' WHERE ship_id=$playerinfo[ship_id]"); 
                   $ok=0;
                }
                else
                {
                   db_kill_player($playerinfo['ship_id']);
                   $ok=0;
                }        
             }
             $ok=1;

           }
           // clean up any sectors that have used up all mines or fighters
           mysql_query("update universe set fm_owner=0 where fm_owner <> 0 and mines=0 and fighters=0");
        }   

    }

?>
