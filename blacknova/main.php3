<?

include("config.php3");
updatecookie();

$title="Main Menu";

$basefontsize = 0;
$stylefontsize = "8Pt";
$picsperrow = 6;

if($res == 640)
  $picsperrow = 4;

if($res >= 1024)
{
  $basefontsize = 1;
  $stylefontsize = "12Pt";
  $picsperrow = 7;
}

include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships READ, universe READ, links READ, zones READ, messages WRITE, planets READ");

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);

$res = mysql_query("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = mysql_fetch_array($res);
mysql_free_result($res);

$res = mysql_query("SELECT * FROM links WHERE link_start='$playerinfo[sector]' ORDER BY link_dest ASC");

srand((double)microtime() * 1000000);

if($playerinfo[on_planet] == "Y")
{
  $res2 = mysql_query("SELECT planet_id FROM planets WHERE planet_id=$playerinfo[planet_id]");
  if(mysql_num_rows($res2) != 0)
  {
    echo "Click <A HREF=planet.php3?planet_id=$playerinfo[planet_id]>here</A> to go to the planet menu.<BR>"; 
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php3?planet_id=$playerinfo[planet_id]&id=".$playerinfo[ship_id]."\">";
    mysql_query("UNLOCK TABLES");
    //-------------------------------------------------------------------------------------------------
    die();
  }
  else
  {
    mysql_query("UPDATE ships SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
    echo "<BR>On a non-existent planet???<BR><BR>";
  }
}

$i = 0;
if($res > 0)
{
  while($row = mysql_fetch_array($res))
  {
    $links[$i] = $row[link_dest];
    $i++;
  }
  mysql_free_result($res);
}
$num_links = $i;

$res = mysql_query("SELECT * FROM planets WHERE sector_id='$playerinfo[sector]'");

$i = 0;
if($res > 0)
{
  while($row = mysql_fetch_array($res))
  {
    $planets[$i] = $row;
    $i++;
  }
  mysql_free_result($res);
}
$num_planets = $i;


$res = mysql_query("SELECT zone_id,zone_name FROM zones WHERE zone_id=$sectorinfo[zone_id]");
$zoneinfo = mysql_fetch_array($res);
mysql_free_result($res);

$shiptypes[0]= "tinyship.gif";
$shiptypes[1]= "smallship.gif";
$shiptypes[2]= "mediumship.gif";
$shiptypes[3]= "largeship.gif";
$shiptypes[4]= "hugeship.gif";

$planettypes[0]= "tinyplanet.gif";
$planettypes[1]= "smallplanet.gif";
$planettypes[2]= "mediumplanet.gif";
$planettypes[3]= "largeplanet.gif";
$planettypes[4]= "hugeplanet.gif";

?>

<table border=2 cellspacing=2 cellpadding=2 bgcolor="#400040" width="75%" align=center>
<tr><td align="center" colspan=3><font color=silver size=<? echo $basefontsize + 2; ?> face="arial">Player <b><font color=white><? echo $playerinfo[character_name];?></font></b>, aboard the <b><font color=white><a href="report.php3"><? echo $playerinfo[ship_name] ?></a></font></b>
</td></tr>
</table>
<?
# New Message Check start -- blindcoder
 $result = mysql_query("SELECT * FROM messages WHERE recp_id='".$playerinfo[ship_id]."' AND notified='N'");
 if (mysql_num_rows($result)>0)
 {
?>
<script language="javascript">{ alert('You have <? echo mysql_num_rows($result);
 ?> Messages waiting for you.'); }</script>
<?
  mysql_query("UPDATE messages SET notified='Y' WHERE recp_id='".$playerinfo[ship_id]."'");
 }
# New Message Check stop -- blindcoder
?>
<table width=75% cellpadding=0 cellspacing=1 border=0 align=center>
<tr><td>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">&nbsp;Turns available: </font><font color=white><b><? echo NUMBER($playerinfo[turns]) ?></b></font>
</td>
<td align=center>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">Turns used: </font><font color=white><b><? echo NUMBER($playerinfo[turns_used]); ?></b></font>
</td>
<td align=right>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">Score: </font><font color=white><b><? echo NUMBER($playerinfo[score])?>&nbsp;</b></font>
</td>
<tr><td>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">&nbsp;Sector: </font><font color=white><b><? echo $playerinfo[sector]; ?></b></font>
</td><td align=center>

<?
if(!empty($sectorinfo[beacon]))
{
  echo "<font color=white size=", $basefontsize + 2," face=\"arial\"><b>", $sectorinfo[beacon], "</b></font>";
}
?>
</td><td align=right>
<a href="<? echo "zoneinfo.php3?zone=$zoneinfo[zone_id]"; ?>"><b><? echo $zoneinfo[zone_name]; ?></b></font></a>&nbsp;
</td></tr>
</table>

<table width=100% border=0 align=center cellpadding=0 cellspacing=0">

<tr>

<td valign=top>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Commands
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href="device.php3">Devices</a>&nbsp;<br>
&nbsp;<a class=mnu href="planet-report.php3">Planets</a>&nbsp;<br>
&nbsp;<a class=mnu href="log.php3">Log</a>&nbsp;<br>
&nbsp;<a class=mnu href="defence-report.php3">Sector Defences</a>&nbsp;<br>
&nbsp;<a class=mnu href="readmail.php3">Read Messages</A>&nbsp;<br> <? # Link to read the messages -- blindcoder ?>
&nbsp;<a class=mnu href="mailto2.php3">Send Message</a>&nbsp;<br>
&nbsp;<a class=mnu href="ranking.php3">Rankings</a>&nbsp;<br>
&nbsp;<a class=mnu href="lastusers.php3">Last Users</a>&nbsp;<br>
&nbsp;<a class=mnu href="teams.php">Alliances</a>&nbsp;<br> 
&nbsp;<a class=mnu href="self-destruct.php3">Self-Destruct</a>&nbsp;<br>
&nbsp;<a class=mnu href="options.php3">Options</a>&nbsp;<br>
&nbsp;<a class=mnu href="navcomp.php3">Nav Computer</a>&nbsp;<br>
</div>
</td></tr>
<tr><td nowrap>
<div class=mnu>
&nbsp;<a class=mnu href="help.php3">Help</a>&nbsp;<br>
&nbsp;<a class=mnu href="http://copland.udel.edu/~wallkk/bnfaq/">FAQ</a>&nbsp;<br>
&nbsp;<a class=mnu href="feedback.php3">Feedback</a>&nbsp;<br>
<?
if(!empty($link_forums))
{
    echo "&nbsp;<a class=mnu href=$link_forums TARGET=\'_blank\'>Forums</a>&nbsp;<br>";
}
?>
</div>
</td></tr>
<tr><td nowrap>
&nbsp;<a class=mnu href="logout.php3">Logout</a>&nbsp;<br>
</td></tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Warp to
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>

<?

if(!$num_links)
{
  echo "&nbsp;<a class=dis>No warp links</a>&nbsp;<br>";
  $link_bnthelper_string="<!--links:N";
}
else
{
  $link_bnthelper_string="<!--links:Y";
  for($i=0; $i<$num_links;$i++)
  {
     echo "&nbsp;<a class=mnu href=move.php3?sector=$links[$i]>=&gt;&nbsp;$links[$i]</a>&nbsp;<a class=dis href=lrscan.php3?sector=$links[$i]>[scan]</a>&nbsp;<br>";
     $link_bnthelper_string=$link_bnthelper_string . ":" . $links[$i];
  }
}
$link_bnthelper_string=$link_bnthelper_string . ":-->";
echo "</div>";
echo "</td></tr>";
echo "<tr><td nowrap align=center>";
echo "<div class=mnu>";
echo "&nbsp;<a class=dis href=lrscan.php3?sector=*>[Full scan]</a>&nbsp;<br>";
?>

</div>
</td></tr>
</table>

</td>

<td valign=top>
&nbsp;<br>

<center><font size=<? echo $basefontsize+2; ?> face="arial" color=white><b>Trading port:&nbsp;

<?
if($sectorinfo[port_type] != "none")
{
  echo "<a href=port.php3>", ucfirst($sectorinfo[port_type]), "</a>";
  $port_bnthelper_string="<!--port:" . $sectorinfo[port_type] . ":" . $sectorinfo[port_ore] . ":" . $sectorinfo[port_organics] . ":" . $sectorinfo[port_goods] . ":" . $sectorinfo[port_energy] . ":-->";
}
else
{
  echo "</b><font size=", $basefontsize+2,">None</font><b>";
  $port_bnthelper_string="<!--port:none:0:0:0:0:-->";
}
?>

</b></font></center>
<br>

<center><b><font size=2 face="arial" color=white>Planets in sector <? echo $sectorinfo[sector_id];?>:</font></b></center>
<table border=0 width=100%>
<tr>

<?

if($num_planets > 0)
{
  $totalcount=0;
  $curcount=0;
  $i=0;
  while($i < $num_planets)
  {
    if($planets[$i][owner] != 0)
    {
      $result5 = mysql_query("SELECT * FROM ships WHERE ship_id=" . $planets[$i][owner]);
      $planet_owner = mysql_fetch_array($result5);

      $planetavg = $planet_owner[hull] + $planet_owner[engines] + $planet_owner[computer] + $planet_owner[beams] + $planet_owner[torp_launchers] + $planet_owner[shields] + $planet_owner[armour];
      $planetavg /= 7;
  
      if($planetavg < 8)
        $planetlevel = 0;
      else if ($planetavg < 12)
        $planetlevel = 1;
      else if ($planetavg < 16)
        $planetlevel = 2;
      else if ($planetavg < 20)
        $planetlevel = 3;
      else
        $planetlevel = 4;
    }
    else
      $planetlevel=0;

    echo "<td align=center valign=top>";
    echo "<A HREF=planet.php3?planet_id=" . $planets[$i][planet_id] . ">";
    echo "<img src=\"images/$planettypes[$planetlevel]\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
    if(empty($planets[$i][name]))
    {
      echo "Unnamed";
      $planet_bnthelper_string="<!--planet:Y:Unnamed:";
    }
    else
    {
      echo $planets[$i][name];
      $planet_bnthelper_string="<!--planet:Y:" . $planets[$i][name] . ":";
    }

    if($planets[$i][owner] == 0)
    {
      echo "<br>(Unowned)";
      $planet_bnthelper_string=$planet_bnthelper_string . "Unowned:-->";
    }
    else
    {
      echo "<br>($planet_owner[character_name])";
      $planet_bnthelper_string=$planet_bnthelper_string . $planet_owner[character_name] . ":-->";
    }
    echo "</font></td>";

    $totalcount++;
    if($curcount == $picsperrow - 1)
    {
      echo "</tr><tr>";
      $curcount=0;
    }
    else
      $curcount++;
    $i++;
  }
}
else
{
  echo "<td align=center valign=top>";
  echo "<br><font color=white size=", $basefontsize +2, ">None</font><br><br>";
  $planet_bnthelper_string="<!--planet:N:::-->";
}
?>

</td>
</tr>
</table>

<b><center><font size=2 face="arial" color=white>Other ships in sector <? echo $sectorinfo[sector_id];?>:</font><br></center></b>
<table border=0 width=100%>
<tr>

<?

if($playerinfo[sector] != 0)
{
  $result4 = mysql_query("SELECT * FROM ships WHERE ship_id<>$playerinfo[ship_id] AND sector=$playerinfo[sector] AND on_planet='N' ORDER BY ship_name ASC");
  $totalcount=0;
  
  if($result4 > 0)
  {
    $curcount=0;
    while($row = mysql_fetch_array($result4))
    {
      $success = SCAN_SUCCESS($playerinfo[sensors], $row[cloak]);
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
        $shipavg = $row[hull] + $row[engines] + $row[computer] + $row[beams] + $row[torp_launchers] + $row[shields] + $row[armour];
        $shipavg /= 7;

        if($shipavg < 8)
          $shiplevel = 0;
        else if ($shipavg < 12)
          $shiplevel = 1;
        else if ($shipavg < 16)
          $shiplevel = 2;
        else if ($shipavg < 20)
          $shiplevel = 3;
        else
          $shiplevel = 4;

        echo "<td align=center valign=top>";
        echo "<a href=ship.php3?ship_id=$row[ship_id]><img src=\"images/", $shiptypes[$shiplevel],"\" border=0></a><BR><font size=", $basefontsize +1, " color=#ffffff face=\"arial\">$row[ship_name]<br>($row[character_name])</font></td>";
        echo "</td>";
        $totalcount++;
        if($curcount == $picsperrow - 1)
        {
          echo "</tr><tr>";
          $curcount=0;
        }
        else
          $curcount++;
      }
    }
  }
  if($result4 == 0 || $totalcount == 0)
  {
    echo "<td align=center valign=top>";
    echo "<br><font color=white>None</font><br><br>";
    echo "</td>";
  }
}
else
{
    echo "<td align=center valign=top>";
    echo "<br><font color=white>There is so much traffic in Sol (Sector 0) that you cannot even isolate other ships!</font><br><br>";
    echo "</td>";
}
if($sectorinfo[fm_owner] == $playerinfo[ship_id] ) 
{
 $mines_owner = 'You have'; 

} 
else
{
   $resultX = mysql_query("SELECT * FROM ships WHERE ship_id=$sectorinfo[fm_owner] ");
  $planet_owner_arry = mysql_fetch_array($resultX);
  $mines_owner = $planet_owner_arry[character_name] . ' has';
}
if($sectorinfo[mines] > 0 || $sectorinfo[fighters] > 0)
{
   $minedesc = 'mines';
   $fighterdesc = 'fighters';
   if($sectorinfo[mines] == 1) $minedesc = 'mine';
   if($sectorinfo[fighters] == 1) $fighterdesc = 'fighter';
   echo "</tr><tr><td align=center valign=top>";
   echo "<br><font color=white>$mines_owner $sectorinfo[mines] $minedesc and $sectorinfo[fighters] $fighterdesc ($sectorinfo[fm_setting]) in this sector.</font><br>";
   echo "</td>";
}
 
?>

</tr>
</table>

<td valign=top>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Cargo
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<a class=dis>
<img align=absmiddle height=12 width=12 alt="Ore" src="images/ore.gif">&nbsp;Ore&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_ore]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Organics" src="images/oragnics.gif">&nbsp;Organics&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_organics]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Goods" src="images/goods.gif">&nbsp;Goods&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_goods]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Energy" src="images/Energy.gif">&nbsp;Energy&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_energy]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Colonists" src="images/Colonists.gif">&nbsp;Colonists&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_colonists]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Credits" src="images/Credits.gif">&nbsp;Credits&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[credits]); ?>&nbsp</div>
</a>
</td></tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
<?
if($playerinfo[traderoutetype] == 'R') echo "Realspace trading";
else echo "Warp trading";
?>

</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href=traderoute.php3?phase=2&destination=<? echo $playerinfo[preset1] ?>>=&gt;&nbsp;<? echo $playerinfo[preset1] ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=traderoute.php3?phase=2&destination=<? echo $playerinfo[preset2] ?>>=&gt;&nbsp;<? echo $playerinfo[preset2] ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=traderoute.php3?phase=2&destination=<? echo $playerinfo[preset3] ?>>=&gt;&nbsp;<? echo $playerinfo[preset3] ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=traderoute.php3>=&gt;&nbsp;Other</a>&nbsp;<br>
</div>
</a>
</td></tr>
<tr><td nowrap>
<div class=mnu>
&nbsp;<a class=mnu href=switchtrade.php3?type=<? if($playerinfo[traderoutetype] == 'W') echo "R"; else echo "W"; ?>><? if($playerinfo[traderoutetype] == 'W') echo "Switch to Real"; else echo "Switch to Warp"; ?></a>&nbsp;<br>
</div>
</a>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
Realspace
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href=rsmove.php3?engage=1&destination=<? echo $playerinfo[preset1]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset1]; ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.php3?engage=1&destination=<? echo $playerinfo[preset2]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset2]; ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.php3?engage=1&destination=<? echo $playerinfo[preset3]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset3]; ?></a>&nbsp;<a class=dis href=preset.php3>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.php3>=&gt;&nbsp;Other</a>&nbsp;<br>
</div>
</a>
</td></tr>
</table>

</td>
</tr>

</table>

<?

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

$player_bnthelper_string="<!--player info:" . $playerinfo[hull] . ":" .  $playerinfo[engines] . ":"  .  $playerinfo[power] . ":" .  $playerinfo[computer] . ":" . $playerinfo[sensors] . ":" .  $playerinfo[beams] . ":" . $playerinfo[torp_launchers] . ":" .  $playerinfo[torps] . ":" . $playerinfo[shields] . ":" .  $playerinfo[armour] . ":" . $playerinfo[armour_pts] . ":" .  $playerinfo[cloak] . ":" . $playerinfo[credits] . ":" .  $playerinfo[sector] . ":" . $playerinfo[ship_ore] . ":" .  $playerinfo[ship_organics] . ":" . $playerinfo[ship_goods] . ":" .  $playerinfo[ship_energy] . ":" . $playerinfo[ship_colonists] . ":" .  $playerinfo[ship_fighters] . ":" . $playerinfo[turns] . ":" .  $playerinfo[on_planet] . ":" . $playerinfo[dev_warpedit] . ":" .  $playerinfo[dev_genesis] . ":" . $playerinfo[dev_beacon] . ":" .  $playerinfo[dev_emerwarp] . ":" . $playerinfo[dev_escapepod] . ":" .  $playerinfo[dev_fuelscoop] . ":" . $playerinfo[dev_minedeflector] . ":-->";
$rspace_bnthelper_string="<!--rspace:" . $sectorinfo[distance] . ":" . $sectorinfo[angle1] . ":" . $sectorinfo[angle2] . ":-->";
echo $player_bnthelper_string;
echo $link_bnthelper_string;
echo $port_bnthelper_string;
echo $planet_bnthelper_string;
echo $rspace_bnthelper_string;

include("footer.php3");

?> 
