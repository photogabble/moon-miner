<?

include("config.php3");
updatecookie();

$title="Use Device";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);

bigtitle();

echo "You have the following devices you can use:<BR><BR>";
if($playerinfo[dev_warpedit] > 0)
{
  echo "$playerinfo[dev_warpedit] <A HREF=warpedit.php3>Warp Editor(s)</A><BR>";
}
if($playerinfo[dev_genesis] > 0)
{
  echo "$playerinfo[dev_genesis] <A HREF=genesis.php3>Genesis Device(s)</A><BR>";
}
if($playerinfo[dev_emerwarp] > 0)
{
  echo "$playerinfo[dev_emerwarp] <A HREF=emerwarp.php3>Emergency Warp Device(s)</A><BR>";
}
if($playerinfo[dev_beacon] > 0)
{
  echo "$playerinfo[dev_beacon] <A HREF=beacon.php3>Space Beacon(s)</A><BR>";
}

echo "<BR>You also have these devices that are used automatically:<BR><BR>";

if($playerinfo[dev_escapepod] == "Y")
{
  echo "Escape Pods<BR>";
}
if($playerinfo[dev_fuelscoop] == "Y")
{
  echo "Fuelscoop<BR>";
}
if($playerinfo[dev_minedeflector] > 0)
{
  echo "$playerinfo[dev_minedeflector] Mine Deflector(s)<BR>";
}

echo "<BR>Click <A HREF=main.php3>here</A> to return to the main menu.";

include("footer.php3");

?> 
