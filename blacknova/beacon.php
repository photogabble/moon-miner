<?
include("config.php");
updatecookie();

include_once($gameroot . "/languages/$lang");
$title=$l_beacon_title;
include("header.php");

connectdb();

if (checklogin()) {die();}

$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo=mysql_fetch_array($result2);

bigtitle();

if($playerinfo[dev_beacon] > 0)
{
  $res = mysql_query("SELECT allow_beacon FROM zones WHERE zone_id='$sectorinfo[zone_id]'");
  $zoneinfo = mysql_fetch_array($res);
  if($zoneinfo[allow_beacon] == 'N')
  {
    echo "$l_beacon_notpermitted<BR><BR>";
  }
  else
  {
    if($beacon_text == "")
    {
      if($sectorinfo[beacon] != "")
      {
        echo "$l_beacon_reads: \"$sectorinfo[beacon]\"<BR><BR>";
      }
      else
      {
        echo "$l_beacon_none<BR><BR>";
      }
      echo"<form action=beacon.php method=post>";
      echo"<table>";
      echo"<tr><td>$l_beacon_enter:</td><td><input type=text name=beacon_text size=40 maxlength=80></td></tr>";
      echo"</table>";
      echo"<input type=submit value=$l_submit><input type=reset value=$l_reset>";
      echo"</form>";
    }
    else
    {
      $beacon_text = trim(strip_tags($beacon_text));
      echo "$l_beacon_nowreads: \"$beacon_text\".<BR><BR>";
      $update = mysql_query("UPDATE universe SET beacon='$beacon_text' WHERE sector_id=$sectorinfo[sector_id]");
      $update = mysql_query("UPDATE ships SET dev_beacon=dev_beacon-1 WHERE ship_id=$playerinfo[ship_id]");
    }
  }
}
else
{
  echo "$l_beacon_donthave<BR><BR>";
}

TEXT_GOTOMAIN();

include("footer.php");

?>
