<?

include("config.php3");
updatecookie();

$title="Use Genesis Device";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships WRITE, universe WRITE, zones READ, planets WRITE");

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = mysql_fetch_array($result2);

$result3 = mysql_query("SELECT planet_id FROM planets WHERE sector_id='$playerinfo[sector]'");
$num_planets = mysql_num_rows($result3);
bigtitle();

if($playerinfo[turns] < 1)
{
  echo "You need at least one turn to use a genesis device.";
}
elseif($playerinfo[on_planet]=='Y')
{
  echo "You can't use this device while on the planet surface.";
} 
elseif($num_planets == $max_planets_sector)
{
  echo "This solar system is already full.";
}

/* -------------------------------------------------------------- *
 * I'm (SharpBlue) lazy. With many planets by sector that code    *
 * becomes pretty hard to manage, and besides... Isn't it a       *
 * little too powerful?                                           *
 * -------------------------------------------------------------- *
elseif($sectorinfo[planet] == "Y")
{
  echo "There is already a planet in this sector.";
  if($playerinfo[ship_id]==$sectorinfo[planet_owner])
  {
    if($destroy==1 && $allow_genesis_destroy)
    {
      echo "<BR>Are you sure???<BR><A HREF=genesis.php3?destroy=2>YES, Let them die!</A><BR>";
      echo "<A HREF=device.php3>No! That would be Evil!</A><BR>";
    }
    elseif($destroy==2 && $allow_genesis_destroy)
    {
      if($playerinfo[dev_genesis] > 0)
      {
        $deltarating=$sectorinfo[planet_colonists];
        $update = mysql_query("UPDATE universe SET planet_name='', planet_organics=0, planet_energy=0, planet_ore=0, planet_goods=0, planet_colonists=0, planet_credits=0, planet_fighters=0, planet_owner=null, planet_corp=null, base='N',base_sells='N', base_torp=0, planet_defeated='N', planet='N' WHERE sector_id=$playerinfo[sector]");
        $update2=mysql_query("UPDATE ships SET turns_used=turns_used+1, turns=turns-1, dev_genesis=dev_genesis-1, rating=rating-$deltarating WHERE ship_id=$playerinfo[ship_id]");
        echo "<BR>Errr, there was one with $deltarating colonists here....<BR>";
      }
      else
      {
        echo "You do not have any genesis devices.";
      }
    }
    elseif($allow_genesis_destroy)
    {
      echo "<BR>Do you want to destroy <A HREF=genesis.php3?destroy=1>";
      if($sectorinfo[planet_name]=="")
      {
        echo "Unnamed</A>?";
      }
      else
      {
        echo $sectorinfo[planet_name] . "</A>?";
      }
    }
  }  
}
* --------------------------------------------------------------- *
* If anyone who's coded this thing is willing to update it to     *
* support multiple planets, go ahead. I suggest removing this     *
* code completely from here and putting it in the planet menu     *
* instead. Easier to manage, makes more sense too.                *
* End of comments section.                                        *
* -------------------------------------------------------------- */

elseif($playerinfo[dev_genesis] < 1)
{
  echo "You do not have any genesis devices.";
}
else
{
  $res = mysql_query("SELECT allow_planet FROM zones WHERE zone_id='$sectorinfo[zone_id]'");
  $zoneinfo = mysql_fetch_array($res);
  if($zoneinfo[allow_planet] == 'N')
  {
    echo "Creating a planet in this sector is not permitted.";
  }
  else
  {
    $query1 = "INSERT INTO planets VALUES('', $playerinfo[sector], NULL, 0, 0, 0, 0, 0, 0, 0, 0, $playerinfo[ship_id], 0, 'N', 'N', $default_prod_organics, $default_prod_ore, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp, 'N')";
    $update1 = mysql_query($query1);
    $query2 = "UPDATE ships SET turns_used=turns_used+1, turns=turns-1, dev_genesis=dev_genesis-1 WHERE ship_id=$playerinfo[ship_id]";
    $update2 = mysql_query($query2);
    echo "Planet created.";
  }
}

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

echo "<BR><BR>";
TEXT_GOTOMAIN();

include("footer.php3");

?> 
