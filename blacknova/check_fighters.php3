<?
    $result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$sector'");
    //Put the sector information into the array "sectorinfo"
    $sectorinfo=mysql_fetch_array($result2);
    mysql_free_result($result2);
    if ($sectorinfo[fighters] > 0 && $sectorinfo[fm_owner] != $playerinfo[ship_id] && $playerinfo[hull] > $mine_hullsize)
    {
        // find out if the fighter owner and player are on the same team
	$result2 = mysql_query("SELECT * from ships where ship_id=$sectorinfo[fm_owner]");
        $fighters_owner = mysql_fetch_array($result2);
        mysql_free_result($result2);
        if ($fighters_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
        {
           if($sectorinfo[fm_setting] == "toll")
           {
              switch($response) {
                 case "fight":
                    bigtitle();
                    include("sector_fighters.php3");                    
                    break;
                 case "retreat":
                    echo "You retreated back to sector $sector.<BR>";
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
                       playerlog($playerinfo[ship_id],"You paid $tollstring credits for entry to sector $sector.");
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
             include("sector_fighters.php3");
           }
           // clean up any sectors that have used up all mines or fighters
           mysql_query("update universe set fm_owner=0 where fm_owner <> 0 and mines=0 and fighters=0");
        }   

    }

?>
