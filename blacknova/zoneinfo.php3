<?

include("config.php3");
updatecookie();

$title="Zone Information";
include("header.php3");

connectdb();
if(checklogin())
{
  die();
}

bigtitle();

// get user info
$res = mysql_query("SELECT * FROM zones WHERE zone_id='$zone'");
if(!mysql_num_rows($res))
{
  echo "This section of space does not exist!";
}
else
{
  $row = mysql_fetch_array($res);
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>$row[zone_name]</B><TD><TD></TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Beacons</TD><TD>";
  if($row[allow_beacon] == 'Y')
  {
    echo "Allowed";
  }
  else
  {
    echo "Not allowed";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Attacking</TD><TD>";
  if($row[allow_attack] == 'Y')
  {
    echo "Allowed";
  }
  else
  {
    echo "Not allowed";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Warp edits</TD><TD>";
  if($row[allow_warpedit] == 'Y')
  {
    echo "Allowed";
  }
  else
  {
    echo "Not allowed";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>Planets</TD><TD>";
  if($row[allow_planet] == 'Y')
  {
    echo "Allowed";
  }
  else
  {
    echo "Not allowed";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>Maximum hull level allowed</TD><TD>";
  if(!$row[max_hull])
  {
    echo "Unlimited";
  }
  else
  {
    echo "$row[max_hull]";
  }
  echo "</TD></TR>";
  echo "</TABLE>";
}

echo "<BR><BR>";
echo "Click <a href=$interface>here</a> to return to main menu.";

include("footer.php3");

?>
