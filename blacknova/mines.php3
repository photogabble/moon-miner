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
mysql_query("LOCK TABLES ships WRITE, universe WRITE, zones READ");

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);
$res = mysql_query("SELECT * from universe WHERE sector_id=$playerinfo[sector]");
$sectorinfo = mysql_fetch_array($res);
mysql_free_result($res);
bigtitle();
$res = mysql_query("SELECT allow_attack,universe.zone_id FROM zones,universe WHERE sector_id=$playerinfo[sector] AND zones.zone_id=universe.zone_id");
$zoneinfo = mysql_fetch_array($res);
mysql_free_result($res);
if($zoneinfo[allow_attack] == 'N')
{
 echo "Deploying Mines and Fighters in this sector is not permitted.<BR><BR>";
}
else
{
   if($sectorinfo[fm_owner] != 0 && $sectorinfo[fm_owner] != $playerinfo[ship_id]) 
   {
     echo "Can not deploy here. Someone else has mines or fighters in this sector.<BR>";
   }
   else
   {

      if(!isset($nummines) or !isset($numfighters) or !isset($mode))
      {
        echo "<FORM ACTION=mines.php3 METHOD=POST>";
        echo "You are presently in sector $playerinfo[sector]. There are " . NUMBER($sectorinfo[mines]) . " mines and " . NUMBER($sectorinfo[fighters]) . " fighters here.<BR><BR>";
        echo "Deploy <INPUT TYPE=TEXT NAME=nummines SIZE=10 MAXLENGTH=10> mines.<BR>";
        echo "Deploy <INPUT TYPE=TEXT NAME=numfighters SIZE=10 MAXLENGTH=10> fighters.<BR>";
        echo "Fighter mode <INPUT TYPE=RADIO NAME=mode VALUE=attack>Attack</INPUT>";
        echo "<INPUT TYPE=RADIO NAME=mode CHECKED VALUE=toll>Toll</INPUT><BR>";
        echo "<INPUT TYPE=SUBMIT VALUE=Deploy><BR><BR>";
        echo "<input type=hidden name=op value=$op>";
        echo "</FORM>";
      }
      else 
      {
         if ($nummines < 0) $nummines = 0;
         if ($numfighters < 0) $numfighters = 0;
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
         $numfighters = NUMBER($numfighters);
         $nummines = NUMBER($nummines);
      
         $update = mysql_query("UPDATE ships SET last_login='$stamp',turns=turns-1,turns_used=turns_used+1,ship_fighters=ship_fighters-$numfighters,torps=torps-$nummines WHERE ship_id=$playerinfo[ship_id]");
         $update = mysql_query("UPDATE universe SET fm_owner = $playerinfo[ship_id], fm_setting ='$mode', mines=mines+$nummines, fighters=fighters+$numfighters WHERE sector_id=$playerinfo[sector]");
      }
   }
}
mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php3");

?>
