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
mysql_query("LOCK TABLES ships WRITE, universe WRITE, zones READ");

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = mysql_fetch_array($result2);

bigtitle();

if($playerinfo[turns] < 1)
{
  echo "You need at least one turn to use a genesis device.";
}
elseif($sectorinfo[planet] == "Y")
{
  echo "There is already a planet in this sector.";
}
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
    $query1= "UPDATE universe SET planet='Y', planet_owner=$playerinfo[ship_id] WHERE sector_id=$playerinfo[sector]";
    $update1 = mysql_query($query1);
    $query2= "UPDATE ships SET turns_used=turns_used+1, turns=turns-1, dev_genesis=dev_genesis-1 WHERE ship_id=$playerinfo[ship_id]";
    $update2 = mysql_query($query2);
    echo "Planet created.";
  }
}

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

echo "<BR><BR>";
echo "Click <A HREF=$interface>here</A> to return to the main menu.";

include("footer.php3");

?> 
