<?
    $result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$sector'");
    //Put the sector information into the array "sectorinfo"
    $sectorinfo=mysql_fetch_array($result2);
    mysql_free_result($result2);
    $result3 = mysql_query ("SELECT * FROM sector_defence WHERE sector_id='$sector' and defence_type ='F' ORDER BY quantity DESC");
    //Put the defence information into the array "defenceinfo"
    $i = 0;
    $total_sector_fighters = 0;
    $owner = true;
    if($result3 > 0)
    {
       while($row = mysql_fetch_array($result3))
       {
          $defences[$i] = $row;
           $total_sector_fighters += $defences[$i]['quantity'];
          if($defences[$i][ship_id] != $playerinfo[ship_id])
          {
             $owner = false; 
          }
          $i++;
       }
       mysql_free_result($result3);
    }
    $num_defences = $i;
    if ($num_defences > 0 && $total_sector_fighters > 0 && !$owner)
    {
        // find out if the fighter owner and player are on the same team
        // All sector defences must be owned by members of the same team
        $fm_owner = $defences[0]['ship_id'];
	$result2 = mysql_query("SELECT * from ships where ship_id=$fm_owner");
        $fighters_owner = mysql_fetch_array($result2);
        mysql_free_result($result2);
        if ($fighters_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
        {
           switch($response) {
              case "fight":
                 mysql_query("UPDATE ships SET cleared_defences = ' ' WHERE ship_id = $playerinfo[ship_id]");
                 bigtitle();
                 include("sector_fighters.$phpext");    
                   
                 break;
              case "retreat":
                 mysql_query("UPDATE ships SET cleared_defences = ' ' WHERE ship_id = $playerinfo[ship_id]");
                 $stamp = date("Y-m-d H-i-s");
                 mysql_query("UPDATE ships SET last_login='$stamp',turns=turns-2, turns_used=turns_used+2, sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
                 bigtitle();
                 echo "You retreat back to your previous location<BR>";
                 TEXT_GOTOMAIN();
                 die();
                 break;
              case "pay":      
                 mysql_query("UPDATE ships SET cleared_defences = ' ' WHERE ship_id = $playerinfo[ship_id]");
                 $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
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
                    distribute_toll($sector,$fighterstoll,$total_sector_fighters);
                    playerlog($playerinfo[ship_id],"You paid $tollstring credits for entry to sector $sector.");
                    $ok=1;
                 }
                 break;
              case "sneak":
                 {
                    mysql_query("UPDATE ships SET cleared_defences = ' ' WHERE ship_id = $playerinfo[ship_id]");
                    $success = SCAN_SUCCESS($playerinfo[sensors], $fighters_owner[cloak]);
                    if($success < 5)
                    {
                       $success = 5;
                    }
                    if($success > 95)
                    {
                       $success = 95;
                    }
                    $roll = rand(1, 100);
                    if($roll < $success)
                    {
                        // sector defences detect incoming ship
                        bigtitle(); 
                        echo "The fighters detect you!<BR>";
                        include("sector_fighters.$phpext");         
                        break;
                    }
                    else
                    {
                       // sector defences don't detect incoming ship
                       $ok=1;                       
                    }
                 }
                 break;
              default:
                 $interface_string = $calledfrom . '?sector='.$sector.'&destination='.$destination.'&engage='.$engage;
                 mysql_query("UPDATE ships SET cleared_defences = '$interface_string' WHERE ship_id = $playerinfo[ship_id]");
                 $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
                 bigtitle();
                 echo "<FORM ACTION=$calledfrom METHOD=POST>";
                 echo "There are $total_sector_fighters fighters in your destination sector.<br>";
                 if($defences[0]['fm_setting'] == "toll")
                 {
                    echo "They demand " . NUMBER($fighterstoll) . " credits to enter this sector.<BR>";    
                 }
                 echo "You can <BR><INPUT TYPE=RADIO NAME=response VALUE=retreat><B>Retreat</B> - Will cost an extra turn.<BR></INPUT>"; 
                 if($defences[0]['fm_setting'] == "toll")
                 {
                    echo "<INPUT TYPE=RADIO NAME=response CHECKED VALUE=pay><B>Pay</B> the toll and enter without harm from the fighters.<BR></INPUT>";
                 } 
                 echo "<INPUT TYPE=RADIO NAME=response CHECKED VALUE=fight><B>Fight</B> - you must defeat all the fighters to enter the sector.<BR></INPUT>";
                 echo "<INPUT TYPE=RADIO NAME=response CHECKED VALUE=sneak><B>Cloak</B> - Use your cloaking device and try to avoid the fighters.<BR></INPUT><BR>";
                 echo "<INPUT TYPE=SUBMIT VALUE=Go><BR><BR>";
                 echo "<input type=hidden name=sector value=$sector>";
                 echo "<input type=hidden name=engage value=1>";
                 echo "<input type=hidden name=destination value=$destination>";
                 echo "</FORM>";
                 die();
                 break;
            }

           
           // clean up any sectors that have used up all mines or fighters
           mysql_query("delete from sector_defence where quantity <= 0 ");
        }   

    }

?>
