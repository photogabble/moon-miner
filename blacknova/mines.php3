<?


include("config.php3");

updatecookie();

$title="Deploy Sector Mines & Fighters";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);
$res = mysql_query("SELECT * from universe WHERE sector_id=$playerinfo[sector]");
$sectorinfo = mysql_fetch_array($res);
mysql_free_result($res);
$result3 = mysql_query ("SELECT * FROM sector_defence WHERE sector_id=$playerinfo[sector] ");
//Put the defence information into the array "defenceinfo"
$i = 0;
$total_sector_fighters = 0;
$total_sector_miness = 0;
$owns_all = true;
$fighter_id = 0;
$mine_id = 0;
$set_attack = 'CHECKED';
$set_toll = '';
if($result3 > 0)
{
   while($row = mysql_fetch_array($result3))
   {
      $defences[$i] = $row;
      if($defences[$i]['defence_type'] == 'F')
         $total_sector_fighters += $defences[$i]['quantity'];
      else
         $total_sector_mines += $defences[$i]['quantity'];

      if($defences[$i][ship_id] != $playerinfo[ship_id])
      {
         $owns_all = false;
      }
      else
      {
         if($defences[$i]['defence_type'] == 'F')
         {
            $fighter_id = $defences[$i]['defence_id'];
            if($defences[$i]['fm_setting'] == 'attack')
            {
               $set_attack = 'CHECKED';
               $set_toll = '';
            } 
            else
            {
               $set_attack = '';
               $set_toll = 'CHECKED';
            }

         }
         else   
            $mine_id = $defences[$i]['defence_id'];

      }
      $i++;
   }
   mysql_free_result($result3);
}
$num_defences = $i;
bigtitle();
if ($playerinfo[turns]<1)
{
	echo "You need at least one turn to deploy sector defences.<BR><BR>";
	TEXT_GOTOMAIN();
	include("footer.php3");
	die();
}
$res = mysql_query("SELECT allow_defenses,universe.zone_id,owner FROM zones,universe WHERE sector_id=$playerinfo[sector] AND zones.zone_id=universe.zone_id");
$zoneinfo = mysql_fetch_array($res);
mysql_free_result($res);
if($zoneinfo[allow_defenses] == 'N')
{
 echo "Deploying Mines and Fighters in this sector is not permitted.<BR><BR>";
}
else
{
   if($num_defences > 0)
   {
      if(!$owns_all)
      {
         $defence_owner = $defences[0]['ship_id'];
         $result2 = mysql_query("SELECT * from ships where ship_id=$defence_owner");
         $fighters_owner = mysql_fetch_array($result2);
         mysql_free_result($result2);
     
         if($fighters_owner[team] != $playerinfo[team] || $playerinfo['team'] == 0) 
         {
            echo "Can not deploy here. Another Ship or Alliance has mines or fighters in this sector.<BR>";
            TEXT_GOTOMAIN();
            die();
            
         }
      }
   }
   if($zoneinfo[allow_defenses] == 'L')    
   {
         $zone_owner = $zoneinfo['owner'];
         $result2 = mysql_query("SELECT * from ships where ship_id=$zone_owner");
         $zoneowner_info = mysql_fetch_array($result2);
         mysql_free_result($result2);
     
         if($zoneowner_info['team'] != $playerinfo['team'] || $playerinfo['team'] == 0) 
         {
            echo "Deploying Mines and Fighters in this sector is not permitted.<BR><BR>";
            TEXT_GOTOMAIN();
            die();
            
         }
   }

   
   if(!isset($nummines) or !isset($numfighters) or !isset($mode))
   {
     $availmines = NUMBER($playerinfo[torps]);
     $availfighters = NUMBER($playerinfo[ship_fighters]);
     echo "<FORM ACTION=mines.php3 METHOD=POST>";
     echo "You are presently in sector $playerinfo[sector]. There are " . NUMBER($sectorinfo[mines]) . " mines and " . NUMBER($sectorinfo[fighters]) . " fighters here.<BR><BR>";
     echo "You have $availmines mines and $availfighters fighters available to deploy.<BR>";
     echo "Deploy <INPUT TYPE=TEXT NAME=nummines SIZE=10 MAXLENGTH=10 VALUE=0> mines.<BR>";
     echo "Deploy <INPUT TYPE=TEXT NAME=numfighters SIZE=10 MAXLENGTH=10 VALUE=0> fighters.<BR>";
     echo "Fighter mode <INPUT TYPE=RADIO NAME=mode $set_attack VALUE=attack>Attack</INPUT>";
     echo "<INPUT TYPE=RADIO NAME=mode $set_toll VALUE=toll>Toll</INPUT><BR>";
     echo "<INPUT TYPE=SUBMIT VALUE=Deploy><BR><BR>";
     echo "<input type=hidden name=op value=$op>";
     echo "</FORM>";
  }
  else 
  {
     if (empty($nummines)) $nummines = 0;
     if (empty($numfighters)) $numfighters = 0;
     if ($nummines < 0) $nummines = 0;
     if ($numfighters < 0) $numfighters =0;
     if ($nummines > $playerinfo[torps])
     {
        echo "You do not have enough torpedos.<BR>";
        $nummines = 0;
     }
     else 
     { 
        echo "Deployed $nummines mines.<BR>";
     }
     if ($numfighters > $playerinfo[ship_fighters])
     {
        echo "You do not have enough fighters.<BR>";
        $numfighters = 0;
     }
     else 
     { 
        echo "Deployed $numfighters fighters in $mode mode.<BR>";
     }
      
     $stamp = date("Y-m-d H-i-s");
     if($numfighters > 0)
     {
        if($fighter_id != 0)
        {
           $update = mysql_query("UPDATE sector_defence set quantity=quantity + $numfighters,fm_setting = '$mode' where defence_id = $fighter_id");
        }
        else
        {
           
           $update = mysql_query("INSERT INTO sector_defence (ship_id,sector_id,defence_type,quantity,fm_setting) values ($playerinfo[ship_id],$playerinfo[sector],'F',$numfighters,'$mode')");
           echo mysql_error();
        }
     }
     if($nummines > 0)
     {
        if($mine_id != 0)
        {
           $update = mysql_query("UPDATE sector_defence set quantity=quantity + $nummines,fm_setting = '$mode' where defence_id = $mine_id");
        }
        else
        {
           $update = mysql_query("INSERT INTO sector_defence (ship_id,sector_id,defence_type,quantity,fm_setting) values ($playerinfo[ship_id],$playerinfo[sector],'M',$nummines,'$mode')");
           
        }
     }
       
     $update = mysql_query("UPDATE ships SET last_login='$stamp',turns=turns-1,turns_used=turns_used+1,ship_fighters=ship_fighters-$numfighters,torps=torps-$nummines WHERE ship_id=$playerinfo[ship_id]");
 
  }
}

//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php3");

?>
