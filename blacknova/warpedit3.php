<?


include("config.php");
updatecookie();

include_once($gameroot . "/languages/$lang");
$title=$l_warp_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

if($playerinfo[turns] < 1)
{
  echo "$l_warp_turn<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

if($playerinfo[dev_warpedit] < 1)
{
  echo "$l_warp_none<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

$res = mysql_query("SELECT allow_warpedit,universe.zone_id FROM zones,universe WHERE sector_id=$playerinfo[sector] AND universe.zone_id=zones.zone_id");
$zoneinfo = mysql_fetch_array($res);
if($zoneinfo[allow_warpedit] == 'N')
{
  echo "$l_warp_forbid<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

$target_sector = round($target_sector);
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

bigtitle();


$res = mysql_query("SELECT allow_warpedit,universe.zone_id FROM zones,universe WHERE sector_id=$target_sector AND universe.zone_id=zones.zone_id");
$zoneinfo = mysql_fetch_array($res);
if($zoneinfo[allow_warpedit] == 'N' && $bothway)
{
  $l_warp_forbidtwo = str_replace("[target_sector]", $target_sector, $l_warp_forbidtwo);
  echo "$l_warp_forbidtwo<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id=$target_sector");
$row = mysql_fetch_array($result2);
if(!$row)
{
  echo "$l_warp_nosector<BR><BR>";
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
    $l_warp_unlinked = str_replace("[target_sector]", $target_sector, $l_warp_unlinked);
    echo "$l_warp_unlinked<BR><BR>";
  }
  else
  {
    $delete1 = mysql_query("DELETE FROM links WHERE link_start=$playerinfo[sector] AND link_dest=$target_sector");
    $update1 = mysql_query("UPDATE ships SET dev_warpedit=dev_warpedit - 1, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
    if(!$bothway)
    {
      echo "$l_warp_removed $target_sector.<BR><BR>";
    }
    else
    {
      $delete2 = mysql_query("DELETE FROM links WHERE link_start=$target_sector AND link_dest=$playerinfo[sector]");
      echo "$l_warp_removedtwo $target_sector.<BR><BR>";
    }
  }
}

TEXT_GOTOMAIN();

include("footer.php");

?>
