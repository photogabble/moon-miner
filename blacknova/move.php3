<?
include("config.php3");
updatecookie();

$title="Move";

include("header.php3");

//Connect to the database
connectdb();

//Check to see if the user is logged in
if (checklogin())
{
    die();
}

//Retrieve the user and ship information
$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
//Put the player information into the array: "playerinfo"
$playerinfo=mysql_fetch_array($result);

//Check to see if the player has less than one turn available
//and if so return to the main menu
if ($playerinfo[turns]<1)
{
	echo "You need at least one turn to move.<BR><BR>";
	TEXT_GOTOMAIN();
	include("footer.php3");
	die();
}

//Retrieve all the sector information about the current sector
$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
//Put the sector information into the array "sectorinfo"
$sectorinfo=mysql_fetch_array($result2);

//Retrive all the warp links out of the current sector
$result3 = mysql_query ("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");
$i=0;
$flag=0;
if ($result3>0)
{
    //loop through the available warp links to make sure it's a valid move
    while ($row = mysql_fetch_array($result3))
    {
        if ($row[link_dest]==$sector && $row[link_start]==$playerinfo[sector])
        {
            $flag=1;
        }
        $i++;
    }
}

//Check if there was a valid warp link to move to
if ($flag==1)
{
    $ok=1;
$stamp = date("Y-m-d H-i-s");
	$query="UPDATE ships SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$sector where ship_id=$playerinfo[ship_id]";
    $move_result = mysql_query ("$query");
	if (!$move_result)
	{
		$error = mysql_error($move_result);
		mail ("harwoodr@cgocable.net","Move Error", "Start Sector: $sectorinfo[sector_id]\nEnd Sector: $sector\nPlayer: $playerinfo[character_name] - $playerinfo[ship_id]\n\nQuery:  $query\n\nMySQL error: $error");
	}

    /* enter code for checking dangers in new sector */
    /* Check for mines */
    //Retrieve all the sector information about the current sector
    $result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$sector'");
    //Put the sector information into the array "sectorinfo"
    $sectorinfo=mysql_fetch_array($result2);
    mysql_free_result($result2);
    if ($sectorinfo[mines] > 0 && $sectorinfo[fm_owner] != $playerinfo[ship_id])
    {
        // find out if the mine owner and player are on the same team
	$result2 = mysql_query("SELECT * from ships where ship_id=$sectorinfo[fm_owner]");
        $mine_owner = mysql_fetch_array($result2);
        mysql_free_result($result2);
        if ($mine_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
        {
	   // Lets blow up some mines!
           bigtitle();
           $ok=0;
           $totalmines = $sectorinfo[mines];
           $roll = rand(1,$sectorinfo[mines]);
           $totalmines = $totalmines - $roll;
           echo "You hit $roll mines!<BR>";
           playerlog($playerinfo[ship_id],"You hit $roll mines in sector $sector.");
           playerlog($sectorinfo[fm_owner],"$playerinfo[character_name] hit $roll of your mines in sector $sector.");
           if($playerinfo[dev_minedeflector] >= $roll)
           {
              echo "You lost $roll mine deflectors.<BR>";
              $result2 = mysql_query("UPDATE ships set dev_minedeflector=dev_minedeflector-$roll where ship_id=$playerinfo[ship_id]");
              $result2 = mysql_query("UPDATE universe set mines=$totalmines where sector_id=$sector");

           }
           else
           {
              echo "You lost all your mine deflectors.<BR>";
              $mines_left = $roll - $playerinfo[dev_minedeflector];
              if($playerinfo[shields] >= $mines_left)
              {
                 echo "Your shields are hit for $mines_left damage.<BR>";
                 $result2 = mysql_query("UPDATE ships set shields=shields-$mines_left,dev_minedeflector=0 where ship_id=$playerinfo[ship_id]");
                 $result2 = mysql_query("UPDATE universe set mines=$totalmines where sector_id=$sector");
                 if($playerinfo[shields] == $mines_left) echo "Your shields are down!<BR>";
              }
              else
              {
                 echo "You lost all your shields!<BR>";
                 $mines_left = $mines_left - $playerinfo[shields];
                 if($playerinfo[armour_pts] >= $mines_left)
                 {
                    echo "Your armour is hit for $mines_left damage.<BR>";
                    $result2 = mysql_query("UPDATE ships set armour_pts=armour_pts-$mines_left,shields=0,dev_minedeflector=0 where ship_id=$playerinfo[ship_id]");
                    $result2 = mysql_query("UPDATE universe set mines=$totalmines where sector_id=$sector");
                    if($playerinfo[armour_pts] == $mines_left) echo "Your hull is breached!<BR>";
                 }
                 else
                 {
                    $result2 = mysql_query("UPDATE universe set mines=$totalmines where sector_id=$sector");
                    playerlog($playerinfo[ship_id],"Your ship was destroyed by mines in sector $sector.");
                    playerlog($sectorinfo[fm_owner],"$playerinfo[character_name] was destroyed by your mines in sector $sector.");
                    echo "Your ship has been destroyed!<BR><BR>";
                    if($playerinfo[dev_escapepod] == "Y")
                    {
                       $rating=round($playerinfo[rating]/2);
                       echo "Luckily you have an escape pod!<BR><BR>";
                       mysql_query("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating' WHERE ship_id=$targetinfo[ship_id]");
                    }
                    else
                    {
                       db_kill_player($playerinfo['ship_id']);
                    }
                 }

              }
   
           }                   


        }   

    }
    if ($ok==1) {echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface\">";} else
    {
        TEXT_GOTOMAIN();
    }
}
else
{
    echo "Move failed!<BR><BR>";
    TEXT_GOTOMAIN();
}

echo "</body></html>";

?>
