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

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

if($playerinfo[turns] < 1)
{
  echo "You need at least one turn to use a warp editor.<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}

if($playerinfo[dev_warpedit] < 1)
{
  echo "You do not have any warp editors.<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}

$res = mysql_query("SELECT allow_warpedit,universe.zone_id FROM zones,universe W
HERE sector_id=$playerinfo[sector] AND universe.zone_id=zones.zone_id");
$zoneinfo = mysql_fetch_array($res);
if($zoneinfo[allow_warpedit] == 'N')
{
  echo "Using a Warp Editor in this sector is not permitted.<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}

$target_sector = round($target_sector);
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

bigtitle();

if($playerinfo[turns] < 1)
{
  echo "You need at least one turn to use a warp editor.<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php3");   
  die();
}

$res = mysql_query("SELECT allow_warpedit,universe.zone_id FROM zones,universe WHERE sector_id=$target_sector AND universe.zone_id=zones.zone_id");
$zoneinfo = mysql_fetch_array($res);
if($zoneinfo[allow_warpedit] == 'N' && $bothway)
{
  echo "Using a Warp Editor to remove a two-way link to sector $target_sector is not permitted.<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$target_sector");
$row = mysql_fetch_array($result2);
if(!$row)
{
  echo "Sector does not exist.<BR><BR>";
  TEXT_GOTOMAIN();
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
  if($flag != 1)
  {
    echo "Target sector ($target_sector) does not have a link from this sector.<BR><BR>";
  }
  else
  {
    $delete1 = mysql_query("DELETE FROM links WHERE link_start=$playerinfo[sector] AND link_dest=$target_sector");
    $update1 = mysql_query("UPDATE ships SET dev_warpedit=dev_warpedit - 1, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    if(!$bothway)
    {
      echo "Link removed to $target_sector.<BR><BR>";
    }
    else
    {
      $delete2 = mysql_query("DELETE FROM links WHERE link_start=$target_sector AND link_dest=$playerinfo[sector]");
      echo "Link removed to and from $target_sector.<BR><BR>";  
    }
  }
}

TEXT_GOTOMAIN();

include("footer.php3");

?> 
