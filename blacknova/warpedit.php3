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

bigtitle();

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

$res = mysql_query("SELECT allow_warpedit,universe.zone_id FROM zones,universe WHERE sector_id=$playerinfo[sector] AND universe.zone_id=zones.zone_id");
$zoneinfo = mysql_fetch_array($res);
if($zoneinfo[allow_warpedit] == 'N')
{
  echo "Using a Warp Editor in this sector is not permitted.<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php3");
  die();
}

$result2 = mysql_query("SELECT * FROM links WHERE link_start=$playerinfo[sector] ORDER BY link_dest ASC");
if($result2 < 1)
{
  echo "There are no links out of this sector.<BR><BR>";
}
else
{
  echo "Links lead from this sector to ";
  while($row = mysql_fetch_array($result2))
  {
    echo "$row[link_dest] ";
  }
  echo "<BR><BR>";
}

echo "<form action=\"warpedit2.php3\" method=\"post\">";
echo "<table>";
echo "<tr><td>What sector would you like to create a link to?</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>One-way?</td><td><input type=\"checkbox\" name=\"oneway\" value=\"oneway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"Submit\"><input type=\"reset\" value=\"Reset\">";
echo "</form>";
echo "<BR><BR>Alternately, you may destroy a link to sector.<BR><BR>";
echo "<form action=\"warpedit3.php3\" method=\"post\">";
echo "<table>";
echo "<tr><td>What sector would you like to remove a link to?</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>Both-ways?</td><td><input type=\"checkbox\" name=\"bothway\" value=\"bothway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"Submit\"><input type=\"reset\" value=\"Reset\">";
echo "</form>";

TEXT_GOTOMAIN();

include("footer.php3");

?> 
