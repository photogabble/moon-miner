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

$result3 = mysql_query("SELECT * FROM links WHERE link_start='$playerinfo[sector]' ORDER BY link_dest ASC");

//bigtitle();

srand((double)microtime() * 1000000);

if($playerinfo[on_planet] == "Y")
{
  if($sectorinfo[planet] == "Y")
  {
    echo "Click <A HREF=planet.php3>here</A> to go to the planet menu.<BR>"; 
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php3?id=".$playerinfo[ship_id]."\">";
    die();
  }
  else
  {
    mysql_query("UPDATE ships SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
    echo "<BR>On a non-existent planet???<BR><BR>";
  }
}
if(!empty($sectorinfo[beacon]))
{
  echo "$sectorinfo[beacon]<BR><BR>";
}

$i = 0;
if($result3 > 0)
{
  while($row = mysql_fetch_array($result3))
  {
    $links[$i] = $row[link_dest];
    $i++;
  }
}
$num_links = $i;

$result4 = mysql_query("SELECT zone_id,zone_name FROM zones WHERE zone_id=$sectorinfo[zone_id]");
$zoneinfo = mysql_fetch_array($result4);

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\"><TD><B>Sector $playerinfo[sector]";
if($sectorinfo[sector_name] != "")
{
  echo " ($sectorinfo[sector_name])";
}
echo "</TD><TD></TD><TD ALIGN=RIGHT><B><A HREF=\"zoneinfo.php3?zone=$zoneinfo[zone_id]\">$zoneinfo[zone_name]</A></B></TD></TR>";
echo "<TR BGCOLOR=\"$color_line2\"><TD>Player: $playerinfo[character_name]</TD><TD>Ship: <A HREF=report.php3>$playerinfo[ship_name]</A></TD><TD ALIGN=RIGHT>Score: " . NUMBER($playerinfo[score]) . "</TD></TR>";
echo "</TABLE><BR>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_line1\"><TD>Turns available: " . NUMBER($playerinfo[turns]) . "</TD><TD ALIGN=RIGHT>Turns used: " . NUMBER($playerinfo[turns_used]) . "</TD></TR>";
echo "</TABLE>";
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_line2\"><TD WIDTH=\"15%\">Ore: " . NUMBER($playerinfo[ship_ore]) . "</TD><TD WIDTH=\"15%\">Organics: " . NUMBER($playerinfo[ship_organics]) . "</TD><TD WIDTH=\"15%\">Goods: " . NUMBER($playerinfo[ship_goods]) . "</TD><TD WIDTH=\"15%\">Energy: " . NUMBER($playerinfo[ship_energy]) . "</TD><TD WIDTH=\"15%\">Colonists: " . NUMBER($playerinfo[ship_colonists]) . "</TD><TD ALIGN=RIGHT>Credits: " . NUMBER($playerinfo[credits]) . "</TD></TR>";
echo "</TABLE><BR>";

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>";
echo "<TR>";
echo "<TD>Warp links:</TD>";
echo "<TD>";
if(!$num_links)
{
  echo "There are no links out of this sector.";
}
else
{
  echo "&nbsp;&nbsp;";
  for($i=0; $i<$num_links;$i++)
  {
    echo "[<A HREF=move.php3?sector=$links[$i]>$links[$i]</A>]";
    if($i + 1 != $num_links)
    {
      echo ", ";
    }
  }
  echo "</TR>";
  echo "<TR>";
  echo "<TD>Long-range scan:";
  if($allow_fullscan)
  {
    echo " [<A HREF=lrscan.php3?sector=*>full scan</A>]";
  }
  echo "</TD><TD>";
  echo "&nbsp;&nbsp;";
  for($i=0; $i<$num_links;$i++)
  {
    echo "<A HREF=lrscan.php3?sector=$links[$i]>$links[$i]</A>";
    if($i + 1 != $num_links)
    {
      echo ", ";
    }
  }
  echo "</TD></TR>";
  echo "</TABLE>";
  echo "<BR>";
}
/* Get a list of the ships in this sector */
if($playerinfo[sector] != 0)
{
  $result4 = mysql_query("SELECT ship_id,ship_name,character_name,cloak FROM ships WHERE ship_id<>$playerinfo[ship_id] AND sector=$playerinfo[sector] AND on_planet='N' ORDER BY ship_name ASC");
  $i = 0;
  if($result4 > 0)
  {
    while($row = mysql_fetch_array($result4))
    {
      $ship_id[$i] = $row[ship_id];
      $ships[$i] = $row[ship_name];
      $character_name[$i] = $row[character_name];
      $ship_cloak[$i] = $row[cloak];
      $i++;
    }
  }
  $num_ships=$i;
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR>";
  echo "<TD>Ships detected:</TD>";
  echo "<TD>&nbsp;&nbsp;";
  if($num_ships<1)
  {
    echo "None";
  }
  else
  {
    $num_detected = 0;
    for($i=0; $i<$num_ships; $i++)
    {
      // display other ships in sector - unless they are successfully cloaked
      $success = SCAN_SUCCESS($playerinfo[sensors], $ship_cloak[$i]);
      if($success < 5)
      {
        $success = 5;
      }
      if($success > 95)
      {
        $success = 95;
      }
      $roll = rand(1, 100);
      if($roll < $success)
      {
        if($num_detected)
        {
          echo ", ";
        }
        $num_detected++;
        echo "$ships[$i] ($character_name[$i]) [<A HREF=scan.php3?ship_id=$ship_id[$i]>scan</A>/<A HREF=attack.php3?ship_id=$ship_id[$i]>attack</A>]";
      }
    }
    if(!$num_detected)
    {
      echo "None";
    }
  }
  echo "</TD>";
  echo "</TR>";
  echo "</TABLE><BR>";
}
else
{
  echo "There is so much traffic in Sol (Sector 0) that you cannot even isolate other ships!<BR><BR>";
}
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>";
echo "<TR>";
echo "<TD>Trading port:</TD>";
echo "<TD>&nbsp;&nbsp;";
if($sectorinfo[port_type] != "none")
{
  echo "<A HREF=port.php3>" . ucfirst($sectorinfo[port_type]) . "</A>";
}
else
{
  echo "None";
}
echo "</TD>";
echo "</TR>";
echo "<TR><TD>&nbsp;</TD><TD></TD></TR>";
echo "<TR>";
echo "<TD>Planet:</TD>";
echo "<TD>&nbsp;&nbsp;";
if($sectorinfo[planet] == "Y" && $sectorinfo[sector_id] != 0)
{
  echo "<A HREF=planet.php3>";
  if(empty($sectorinfo[planet_name]))
  {
    echo "Unnamed";
  }
  else
  {
    echo "$sectorinfo[planet_name]";
  }
  echo "</A> (";
  if($sectorinfo[planet_owner] == "")
  {
    echo "Unowned";
  }
  else
  {
    $result5 = mysql_query("SELECT character_name FROM ships WHERE ship_id=$sectorinfo[planet_owner]");
    $planet_owner_name = mysql_fetch_array($result5);
    echo "$planet_owner_name[character_name]";
  }
  echo ")";
}
else
{
  echo "None";
}
echo "</TD>";
echo "</TR>";
echo "</TABLE><BR>";

if($allow_navcomp)
{
  echo "<FORM ACTION=navcomp.php3 METHOD=POST>";
  echo "Navigation Computer: ";
  $maxlen = strlen(number_format($sector_max, 0, "", ""));
  echo "<INPUT NAME=\"stop_sector\" SIZE=" . $maxlen * 2 . " MAXLENGTH=$maxlen><INPUT TYPE=\"HIDDEN\" NAME=\"state\" VALUE=1>";
  echo "<INPUT TYPE=SUBMIT VALUE=\"Find Route\">";
  echo "</FORM>";
}

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0>";
echo "<TR>";
echo "<TD>RealSpace <A HREF=preset.php3>Presets</A>:</TD>";
echo "<TD>&nbsp;&nbsp;[<A HREF=rsmove.php3?engage=1&destination=$playerinfo[preset1]>$playerinfo[preset1]</A>], [<A HREF=rsmove.php3?engage=1&destination=$playerinfo[preset2]>$playerinfo[preset2]</A>], [<A HREF=rsmove.php3?engage=1&destination=$playerinfo[preset3]>$playerinfo[preset3]</A>], <A HREF=rsmove.php3>Other</A></TD>";
echo "</TR>";
echo "<TR>";
echo "<TD>Trade Routes:</TD>";
echo "<TD>&nbsp;&nbsp;[<A HREF=traderoute.php3?phase=2&destination=$playerinfo[preset1]>$playerinfo[preset1]</A>], [<A HREF=traderoute.php3?phase=2&destination=$playerinfo[preset2]>$playerinfo[preset2]</A>], [<A HREF=traderoute.php3?phase=2&destination=$playerinfo[preset3]>$playerinfo[preset3]</A>], <A HREF=traderoute.php3>Other</A></TD>";
echo "</TR>";
echo "</TABLE><BR>";

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
echo "<TR BGCOLOR=\"$color_header\">";
echo "<TD>";
echo "<A HREF=device.php3>Devices</A> - ";
echo "<A HREF=planet-report.php3>Planets</A> - ";
echo "<A HREF=log.php3>Log</A> - ";
echo "<A HREF=mailto2.php3>Send Message</A> - "; 
echo "<A HREF=ranking.php3>Rankings</A> - ";
echo "<A HREF=lastusers.php3>Last Users</A> - ";
echo "<A HREF=options.php3>Options</A> - ";
echo "<A HREF=feedback.php3>Feedback</A> - ";
echo "<A HREF=self-destruct.php3>Self-Destruct</A> - ";
echo "<A HREF=help.php3>Help</A>";
if(!empty($link_forums))
{
  echo " - <A HREF=$link_forums TARGET=\"_blank\">Forums</A>";
}
echo "</TD>";
echo "<TD><A HREF=logout.php3>Logout</A></TD>";
echo "</TR>";
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
