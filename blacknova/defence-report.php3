<?

include("config.php3");
updatecookie();

$title="Sector Defence Report";
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);



$query = "SELECT * FROM universe WHERE fm_owner=$playerinfo[ship_id]";
if(!empty($sort))
{
  $query .= " ORDER BY";
  if($sort == "fighters")
  {
    $query .= " fighters ASC";
  }
  elseif($sort == "mines")
  {
    $query .= " mines ASC";
  }
  elseif($sort == "mode")
  {
    $query .= " fm_setting ASC";
  }
  else
  {
    $query .= " sector_id ASC";
  }
}

$res = mysql_query($query);

bigtitle(); 



$i = 0;
if($res)
{
  while($row = mysql_fetch_array($res))
  {
    $sector[$i] = $row;
    $i++;
  }
}
mysql_free_result($res);

$num_sectors = $i;
if($num_sectors < 1)
{
  echo "<BR>You have no sector defences deployed.";
}
else
{
  echo "Click on column header to sort.<BR><BR>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=2>";
  echo "<TR BGCOLOR=\"$color_header\">";
  echo "<TD><B><A HREF=defence-report.php3>Sector</A></B></TD>";
  echo "<TD><B><A HREF=defence-report.php3?sort=mines>Mines</A></B></TD>";
  echo "<TD><B><A HREF=defence-report.php3?sort=fighters>Fighters</A></B></TD>";
  echo "<TD><B><A HREF=defence-report.php3?sort=mode>Mode</A></B></TD>";
  echo "</TR>";
  $color = $color_line1;
  for($i=0; $i<$num_planets; $i++) 
    
    echo "<TR BGCOLOR=\"$color\">";
    echo "<TD><A HREF=rsmove.php3?engage=1&destination=". $sector[$i][sector_id] . ">". $sector[$i][sector_id] ."</A></TD>";
    echo "<TD>" . NUMBER($sector[$i][mines]) . "</TD>";
    echo "<TD>" . NUMBER($sector[$i][fighters]) . "</TD>";
    echo "<TD>" . $sector[$i][fm_setting] . "</TD>";
    echo "</TR>";

    if($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
  }
  echo "</TABLE>";

echo "<BR><BR>";

TEXT_GOTOMAIN();

include("footer.php3");

?> 
