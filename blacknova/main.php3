<?

include("config.php3");
updatecookie();

$title="Main Menu";
include("header.php3");
connectdb();

if(checklogin())
{
  die();
}

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo=mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo=mysql_fetch_array($result2);

$result3 = mysql_query("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");

bigtitle();

if($playerinfo[on_planet] == "Y")
{
  if($sectorinfo[planet] == "Y")
  {
    echo "Click <a href=planet.php3>here</a> to go to the planet menu.<BR>"; 
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php3?id=".$playerinfo[ship_id]."\">";
    die();
  }
  else
  {
    $update = mysql_query("UPDATE ships SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
    echo "<BR>On a non-existent planet???<BR><BR>";
  }
}
if(!empty($sectorinfo[beacon]))
{
  echo "$sectorinfo[beacon]<BR><BR>";
}

$i=0;
if($result3 > 0)
{
  while($row = mysql_fetch_array($result3))
  {
    $links[$i]=$row[link_dest];
    $i++;
  }
}
$num_links=$i;

$result4 = mysql_query("SELECT zone_id,zone_name FROM zones WHERE zone_id=$sectorinfo[zone_id]");
$zoneinfo = mysql_fetch_array($result4);

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Sector $playerinfo[sector]";
if($sectorinfo[sector_name] != "")
{
  echo " ($sectorinfo[sector_name])";
}
echo "</TD><TD></TD><TD ALIGN=RIGHT><B><A HREF=\"zoneinfo.php3?zone=$zoneinfo[zone_id]\">$zoneinfo[zone_name]</A></B></TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Player: $playerinfo[character_name]</TD><TD>Ship: $playerinfo[ship_name]</TD><TD ALIGN=RIGHT>Score: " . NUMBER($playerinfo[score]) . "</TD></TR>";
echo "</TABLE><BR>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Turns available: " . NUMBER($playerinfo[turns]) . "</TD><TD ALIGN=RIGHT>Turns used: " . NUMBER($playerinfo[turns_used]) . "</TD></TR>";
echo "</TABLE>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_line2\"><TD WIDTH=\"15%\">Ore: " . NUMBER($playerinfo[ship_ore]) . "</TD><TD WIDTH=\"15%\">Organics: " . NUMBER($playerinfo[ship_organics]) . "</TD><TD WIDTH=\"15%\">Goods: " . NUMBER($playerinfo[ship_goods]) . "</TD><TD WIDTH=\"15%\">Energy: " . NUMBER($playerinfo[ship_energy]) . "</TD><TD WIDTH=\"15%\">Colonists: " . NUMBER($playerinfo[ship_colonists]) . "</TD><TD ALIGN=RIGHT>Credits: " . NUMBER($playerinfo[credits]) . "</TD></TR>";
echo "</TABLE><BR>";

if($num_links == 0)
{
  echo "There are no links out of this sector.<BR><BR>";
}
else
{
  echo "Links lead to the following sectors (click to move): ";
  for  ($i=0; $i<$num_links;$i++)
  {
    echo "<a href=move.php3?sector=$links[$i]>$links[$i]</a>";
    if ($i+1!=$num_links) { echo ", ";}
  }
  echo "<BR><BR>";
  echo "Long-range scan: ";
  if($allow_fullscan)
  {
    echo "<A HREF=lrscan.php3?sector=*>full scan</A> - By sector: ";
  }
  for($i=0; $i<$num_links;$i++)
  {
    echo "<a href=lrscan.php3?sector=$links[$i]>$links[$i]</a>";
    if($i + 1 != $num_links)
    {
      echo ", ";
    }
  }
  echo "<BR><BR>";                }
/* Get a list of the ships in this sector */
if($playerinfo[sector] != 0)
{
  $result4 = mysql_query("SELECT * FROM ships WHERE sector=$playerinfo[sector] AND on_planet='N'");
  $i = 0;
  if($result4 > 0)
  {
    while ($row = mysql_fetch_array($result4))
    {
      $ships[$i]=$row[ship_name];
      $ship_id[$i]=$row[ship_id];
      $i++;
    }
  }
  $num_ships=$i;
  if($num_ships<2)
  {
    echo "There are no other ships in this sector.<BR><BR>";
  }
  else
  {
    echo "The are other ships in this sector (click to scan - if blank, there may be cloaked ships): ";
    for($i=0; $i<$num_ships; $i++)
    {
      /* display other ships in sector - unless they are successfully cloaked, or they are this player. */
      $success=(10 - $targetinfo[cloak] + $playerinfo[sensors])*5;
      if($success < 5)
      {
        $success = 5;
      }
      if($success > 95)
      {
        $success = 95;
      }
      $roll = rand(1, 100);
      
      if($ships[$i] != $playerinfo[ship_name] && $roll < $success)
      {
        echo "<a href=scan.php3?ship_id=$ship_id[$i]>$ships[$i]</a> (<a href=attack.php3?ship_id=$ship_id[$i]>attack</a>/<a href=mailto.php3?to=$ship_id[$i]>mail</a>)";
        if($i + 1 != $num_ships)
        {
          echo "  ";
        }
      }
    }
    echo "<BR><BR>";
  }
}
else
{
  echo "There is so much traffic in Sol (Sector 0) that you cannot even isolate other ships!<BR><BR>";
}
if($sectorinfo[port_type] != "none")
{
  echo "There is a <a href=port.php3>$sectorinfo[port_type] port</a> here.<BR><BR>";
}
if($sectorinfo[planet] == "Y" && $sectorinfo[sector_id] != 0)
{
  echo "There is a <a href=planet.php3>planet</a> here ";
  if(empty($sectorinfo[planet_name]))
  {
    echo "with no name ";
  }
  else
  {
    echo "named $sectorinfo[planet_name] ";
  }
  
  if($sectorinfo[planet_owner] == "")
  {
    echo "and it is unowned.<BR><BR>";
  }
  else
  {
    $result5 = mysql_query("SELECT character_name FROM ships WHERE ship_id=$sectorinfo[planet_owner]");
    $planet_owner_name=mysql_fetch_array($result5);
    echo "owned by <a href=mailto.php3?to=$sectorinfo[planet_owner]>$planet_owner_name[character_name]</a><BR><BR>";
  }
}

if($allow_navcomp)
{
  echo "<FORM ACTION=navcomp.php3 METHOD=POST>";
  echo "Navigation Computer: ";
  $maxlen = strlen(number_format($sector_max, 0, "", ""));
  echo "<INPUT NAME=\"stop_sector\" SIZE=" . $maxlen * 2 . " MAXLENGTH=$maxlen><INPUT TYPE=\"HIDDEN\" NAME=\"state\" VALUE=1>";
  echo "<INPUT TYPE=SUBMIT VALUE=\"Find Route\">";
  echo "</FORM>";
}

echo "Real Space Presets:  <a href=rsmove.php3?engage=1&destination=$playerinfo[preset1]>$playerinfo[preset1]</a> & <a href=rsmove.php3?engage=1&destination=$playerinfo[preset2]>$playerinfo[preset2]</a> & <a href=rsmove.php3?engage=1&destination=$playerinfo[preset3]>$playerinfo[preset3]</a> - <a href=preset.php3>Change Presets</a><BR><BR>";
echo "Trade Route Presets:  <a href=traderoute.php3?phase=2&destination=$playerinfo[preset1]>$playerinfo[preset1]</a> & <a href=traderoute.php3?phase=2&destination=$playerinfo[preset2]>$playerinfo[preset2]</a> & <a href=traderoute.php3?phase=2&destination=$playerinfo[preset3]>$playerinfo[preset3]</a><BR><BR>";

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\"><TD><A HREF=device.php3>Devices</A> - <A HREF=report.php3>Report</A> - <A HREF=planet-report.php3>Planet Report</A> - <A HREF=log.php3>Log</A> - <A HREF=rsmove.php3>RealSpace Move</A> - <A HREF=traderoute.php3>Trade Route</A> - <A HREF=mailto2.php3>Send Message</A> -"; 
echo "<A HREF=ranking.php3>Rankings</A> - <A HREF=options.php3>Options</A> - <A HREF=feedback.php3>Feedback</A> - <A HREF=help.php3>Help</A>";
if(!empty($link_forums))
{
  echo " - <A HREF=$link_forums TARGET=\"_blank\">Forums</A>";
}
echo "</TD><TD><A HREF=logout.php3>Logout</A></TD></TR>";
echo "</TABLE><BR>";
echo "System Time:  ";
print(date("l dS of F Y h:i:s A"));
echo "<BR>Last System Update:  ";
$lastupdate = filemtime($gameroot . "/cron.txt");
print(date("l dS of F Y h:i:s A",$lastupdate)) ; 
echo "<BR>Updates happen every 6 minutes.";
gen_score($playerinfo[ship_id]);
include("footer.php3");

?> 
