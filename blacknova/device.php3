<?

include("config.php3");
updatecookie();

$title="Devices";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);

bigtitle();

echo "Your ship is equipped with the following devices (click on a device to use it):<BR><BR>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Device</B></TD><TD><B>Quantity</B></TD><TD><B>Usage</B></TD></TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD><A HREF=beacon.php3>Beacons</A></TD><TD>" . NUMBER($playerinfo[dev_beacon]) . "</TD><TD>Manual</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD><A HREF=warpedit.php3>Warp Editors</A></TD><TD>" . NUMBER($playerinfo[dev_warpedit]) . "</TD><TD>Manual</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD><A HREF=genesis.php3>Genesis Torpedoes</A></TD><TD>" . NUMBER($playerinfo[dev_genesis]) . "</TD><TD>Manual</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD>Mine Deflectors</TD><TD>" . NUMBER($playerinfo[dev_minedeflector]) . "</TD><TD>Automatic</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD><A HREF=emerwarp.php3>Emergency Warp</A></TD><TD>" . NUMBER($playerinfo[dev_emerwarp]) . "</TD><TD>Manual/Automatic</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line2\">";
echo "<TD>Escape Pod</TD><TD>" . (($playerinfo[dev_escapepod] == 'Y') ? "Yes" : "No") . "</TD><TD>Automatic</TD>";
echo "</TR>";
echo "<TR BGCOLOR=\"$color_line1\">";
echo "<TD>Fuel Scoop</TD><TD>" . (($playerinfo[dev_fuelscoop] == 'Y') ? "Yes" : "No") . "</TD><TD>Automatic</TD>";
echo "</TR>";
echo "</TABLE>";

echo "<BR>Click <A HREF=$interface>here</A> to return to the main menu.";

include("footer.php3");

?> 
