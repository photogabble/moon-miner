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

$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo=mysql_fetch_array($result2);

bigtitle();

if($playerinfo[turns] < 1)
{
  echo "You need at least one turn to use a genesis device.<BR><BR>";
  echo "Click <a href=main.php3>here</a> to return to Main Menu.";
  include("footer.php3");   
  die();
}
if($sectorinfo[planet] == "Y")
{
  echo "There is already a planet in this sector. -  $sector_info[planet]<BR><BR>";
}
elseif($playerinfo[dev_genesis] < 1)
{
  echo "You do not have any genesis devices.<BR><BR>";
}
else
{
  $res = mysql_query("SELECT allow_planet FROM zones WHERE zone_id='$sectorinfo[zone_id]'");
  $zoneinfo = mysql_fetch_array($res);
  if($zoneinfo[allow_planet] == 'N')
  {
    echo "Creating a planet in this sector is not permitted.<BR><BR>";
  }
  else
  {
    $query1= "UPDATE universe SET planet='Y', planet_owner=$playerinfo[ship_id] WHERE sector_id=$playerinfo[sector]";
    $update1 = mysql_query ($query1);
    $query2= "UPDATE ships SET turns_used=turns_used+1, turns=turns-1, dev_genesis=dev_genesis-1 WHERE ship_id=$playerinfo[ship_id]";
    $update2 = mysql_query ($query2);
    echo "Planet created.<BR><BR>";
  }
}

echo "Click <a href=main.php3>here</a> to return to the main menu.";

include("footer.php3");

?> 
