<?
    include_once($gameroot . "/languages/$lang");

    $result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$sector'");
    //Put the sector information into the array "sectorinfo"
    $sectorinfo=mysql_fetch_array($result2);
    mysql_free_result($result2);
    $result3 = mysql_query ("SELECT * FROM sector_defence WHERE sector_id='$sector' and defence_type ='M'");
    //Put the defence information into the array "defenceinfo"
    $i = 0;
    $total_sector_mines = 0;
    $owner = true;
    if($result3 > 0)
    {
       while($row = mysql_fetch_array($result3))
       {
          $defences[$i] = $row;
           $total_sector_mines += $defences[$i]['quantity'];
          if($defences[$i][ship_id] != $playerinfo[ship_id])
          {
             $owner = false;
          }
          $i++;
       }
       mysql_free_result($result3);
    }
    $num_defences = $i;
    if ($num_defences > 0 && $total_sector_mines > 0 && !$owner && $playerinfo[hull] > $mine_hullsize)
    {
        $fm_owner = $defences[0][ship_id];
	$result2 = mysql_query("SELECT * from ships where ship_id=$fm_owner");
        $mine_owner = mysql_fetch_array($result2);
        mysql_free_result($result2);
        if ($mine_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
        // find out if the mine owner and player are on the same team
        {
	   // Lets blow up some mines!
           bigtitle();
           $ok=0;
           $totalmines = $total_sector_mines;
           if ($totalmines>1)
           {
              $roll = rand(1,$totalmines);
           }
           else
           {
              $roll = 1;
           }
           $totalmines = $totalmines - $roll;
           $l_chm_youhitsomemines = str_replace("[chm_roll]", $roll, $l_chm_youhitsomemines);
           echo "$l_chm_youhitsomemines<BR>";
           playerlog($playerinfo[ship_id], LOG_HIT_MINES, "$roll|$sector");
           $l_chm_hehitminesinsector = str_replace("[chm_playerinfo_character_name]", $playerinfo[character_name], $l_chm_hehitminesinsector);
           $l_chm_hehitminesinsector = str_replace("[chm_roll]", $roll, $l_chm_hehitminesinsector);
           $l_chm_hehitminesinsector = str_replace("[chm_sector]", $sector, $l_chm_hehitminesinsector);
           message_defence_owner($sector,"$l_chm_hehitminesinsector");
           if($playerinfo[dev_minedeflector] >= $roll)
           {
              $l_chm_youlostminedeflectors = str_replace("[chm_roll]", $roll, $l_chm_youlostminedeflectors);
              echo "$l_chm_youlostminedeflectors<BR>";
              $result2 = mysql_query("UPDATE ships set dev_minedeflector=dev_minedeflector-$roll where ship_id=$playerinfo[ship_id]");
           }
           else
           {
              if($playerinfo[dev_minedeflector] > 0)
              {
                 echo "$l_chm_youlostallminedeflectors<BR>";
              }
              else
              {
                 echo "$l_chm_youhadnominedeflectors<BR>";
              }

              $mines_left = $roll - $playerinfo[dev_minedeflector];
              $playershields = NUM_SHIELDS($playerinfo[shields]);
              if($playershields > $playerinfo[ship_energy])
              {
                 $playershields=$playerinfo[ship_energy];
              }


              if($playershields >= $mines_left)
              {
                 $l_chm_yourshieldshitforminesdmg = str_replace("[chm_mines_left]", $mines_left, $l_chm_yourshieldshitforminesdmg);
                 echo "$l_chm_yourshieldshitforminesdmg<BR>";
                 $result2 = mysql_query("UPDATE ships set ship_energy=ship_energy-$mines_left, dev_minedeflector=0 where ship_id=$playerinfo[ship_id]");
                 if($playershields == $mines_left) echo "$l_chm_yourshieldsaredown<BR>";
              }
              else
              {
                 echo "$l_chm_youlostallyourshields<BR>";
                 $mines_left = $mines_left - $playershields;
                 if($playerinfo[armour_pts] >= $mines_left)
                 {
                    $l_chm_yourarmorhitforminesdmg = str_replace("[chm_mines_left]", $mines_left, $l_chm_yourarmorhitforminesdmg);
                    echo "$l_chm_yourarmorhitforminesdmg<BR>";
                    $result2 = mysql_query("UPDATE ships set armour_pts=armour_pts-$mines_left,ship_energy=0,dev_minedeflector=0 where ship_id=$playerinfo[ship_id]");
                    if($playerinfo[armour_pts] == $mines_left) echo "$l_chm_yourhullisbreached<BR>";
                 }
                 else
                 {
                    $pod = $playerinfo[dev_escapepod];
                    playerlog($playerinfo[ship_id], LOG_SHIP_DESTROYED_MINES, "$sector|$pod");
                    $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]", $playerinfo[character_name], $l_chm_hewasdestroyedbyyourmines);
                    $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", $sector, $l_chm_hewasdestroyedbyyourmines);
                    message_defence_owner($sector,"$l_chm_hewasdestroyedbyyourmines");
                    echo "$l_chm_yourshiphasbeendestroyed<BR><BR>";
                    if($playerinfo[dev_escapepod] == "Y")
                    {
                       $rating=round($playerinfo[rating]/2);
                       echo "$l_chm_luckescapepod<BR><BR>";
                       mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating',cleared_defences=' ' WHERE ship_id=$playerinfo[ship_id]");
                    }
                    else
                    {
                       db_kill_player($playerinfo['ship_id']);
                    }
                 }

              }


           }

           explode_mines($sector,$roll);

        }

    }

?>
