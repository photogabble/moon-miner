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

bigtitle();

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

$result2 = mysql_query("SELECT * FROM links WHERE link_start=$playerinfo[sector] ORDER BY link_dest ASC");
if($result2 < 1)
{
  echo "$l_warp_nolink<BR><BR>";
}
else
{
  echo "$l_warp_linkto ";
  while($row = mysql_fetch_array($result2))
  {
    echo "$row[link_dest] ";
  }
  echo "<BR><BR>";
}

echo "<form action=\"warpedit2.php\" method=\"post\">";
echo "<table>";
echo "<tr><td>$l_warp_query</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>$l_warp_oneway?</td><td><input type=\"checkbox\" name=\"oneway\" value=\"oneway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"$l_submit\"><input type=\"reset\" value=\"$l_reset\">";
echo "</form>";
echo "<BR><BR>$l_warp_dest<BR><BR>";
echo "<form action=\"warpedit3.php\" method=\"post\">";
echo "<table>";
echo "<tr><td>$l_warp_destquery</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>$l_warp_bothway?</td><td><input type=\"checkbox\" name=\"bothway\" value=\"bothway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"$l_submit\"><input type=\"reset\" value=\"$l_reset\">";
echo "</form>";

TEXT_GOTOMAIN();

include("footer.php");

?>
