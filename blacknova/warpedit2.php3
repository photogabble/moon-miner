<?

include("config.php3");
updatecookie();

$title="Use Warp Editor";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$target_sector=round($target_sector);
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

bigtitle();

$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id=$target_sector");
$row = mysql_fetch_array($result2);
if(!$row)
{
  echo "Sector does not exist.  Click <a href=$interface>here</a> to return to the main menu.";
  die();
}

$res = mysql_query("SELECT allow_warpedit,universe.zone_id FROM zones,universe WHERE sector_id=$target_sector AND universe.zone_id=zones.zone_id");
$zoneinfo = mysql_fetch_array($res);
if($zoneinfo[allow_warpedit] == 'N' && !$oneway)
{
  echo "Using a Warp Editor to create a two-way link to sector $target_sector is not permitted.<BR><BR>";
  echo "Click <a href=$interface>here</a> to return to Main Menu.";
  include("footer.php3");
  die();
}

$result3 = mysql_query("SELECT * FROM links WHERE link_start=$playerinfo[sector]");
if($result3 > 0)
{
  while($row = mysql_fetch_array($result3))
  {
    if($target_sector == $row[link_dest])
    {
      $flag = 1;
    }
  }
  if($flag == 1)
  {
    echo "Target sector ($target_sector) already has a link from this sector.<BR><BR>";
  }
  else
  {
    $insert1 = mysql_query ("INSERT INTO links SET link_start=$playerinfo[sector], link_dest=$target_sector");
    $update1 = mysql_query ("UPDATE ships SET dev_warpedit=dev_warpedit - 1, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    if($oneway)
    {
      echo "Link created one-way to $target_sector.<BR><BR>";
    }
    else
    {
      $result4 = mysql_query ("SELECT * FROM links WHERE link_start=$target_sector");
      if($result4 > 1)
      {
        while($row = mysql_fetch_array($result4))
        {
          if($playerinfo[sector] == $row[link_dest])
          {
            $flag2 = 1;
          }
        }
      }
      if($flag2 != 1)
      {
        $insert2 = mysql_query ("INSERT INTO links SET link_start=$target_sector, link_dest=$playerinfo[sector]");
      }
      
      echo "Link created to and from $target_sector.<BR><BR>";  
    }
  }
}

echo "Click <a href=$interface>here</a> to return to the main menu.";

include("footer.php3");

?> 
