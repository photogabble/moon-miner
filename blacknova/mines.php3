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
mysql_query("LOCK TABLES ships WRITE, universe READ");

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);
$res = mysql_query("SELECT * from universe WHERE sector_id=$playerinfo[sector]");
$sectorinfo = mysql_fetch_array($res);
mysql_free_result($res);
bigtitle();

if($sectorinfo[fm_owner] != '' && $sectorinfo[fm_owner] != $playerinfo[ship_id]) 
{
  echo "Can not deploy here. Someone else has mines or fighters in this sector.";
}
else
{

   if(!isset($nummines) || !isset($numfighters) || !isset$($mode))
   {
     echo "<FORM ACTION=mines.php3 METHOD=POST>";
     echo "You are presently in sector $playerinfo[sector]. There are " . NUMBER($sectorinfo[mines]) . " and " . NUMBER($sectorinfo[fighters]) . " fighters here.<BR><BR>";
     echo "Deploy <INPUT TYPE=TEXT NAME=nummines SIZE=10 MAXLENGTH=10> mines.<BR>";
     echo "Deploy <INPUT TYPE=TEXT NAME=numfighters SIZE=10 MAXLENGTH=10> fighters.<BR>";
     echo "Fighter mode <INPUT TYPE="RADIO" NAME="mode" VALUE="attack">Attack</INPUT>");
     echo "<INPUT TYPE="RADIO" NAME="mode" CHECKED VALUE="toll">Toll</INPUT>");
     echo "<INPUT TYPE=SUBMIT VALUE=Compute><BR><BR>";
     echo "<input type="hidden" name="op" value="$op">;
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
      
      $update = mysql_query("UPDATE ships SET last_login='$stamp',turns=turns-1,turns_used=turns_used+1,ship_fighters=shipfighters-$numfighters,torps=torps-$nummines WHERE ship_id=$playerinfo[ship_id]");
      $update = mysql_query("UPDATE uninverse SET fm_owner = $player_info[ship_id], fm_setting ='$mode', mines=mines+$nummines, fighters=fighters+$numfighters WHERE sector_id=$playerinfo[sector]");
   }

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php3");

?>
