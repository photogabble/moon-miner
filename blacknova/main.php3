<?

include("extension.inc");
include("config.$phpext");
updatecookie();

$title="Main Menu";

$basefontsize = 0;
$stylefontsize = "8Pt";
$picsperrow = 5;

if($res == 640)
  $picsperrow = 3;

if($res >= 1024)
{
  $basefontsize = 1;
  $stylefontsize = "12Pt";
  $picsperrow = 7;
}

include("header.$phpext");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);
if($playerinfo['cleared_defences'] > ' ')
{
   echo "You haven't completed your previous move.<BR>";
   echo "<a href=$playerinfo[cleared_defences]>Click Here to continue</a>";
   die();
}


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
    echo "Click <A HREF=planet.$phpext?planet_id=$playerinfo[planet_id]>here</A> to go to the planet menu.<BR>";
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.$phpext?planet_id=$playerinfo[planet_id]&id=".$playerinfo[ship_id]."\">";

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

$res = mysql_query("SELECT * FROM sector_defence,ships WHERE sector_defence.sector_id='$playerinfo[sector]'
                                                       AND ships.ship_id = sector_defence.ship_id ");


$i = 0;
if($res > 0)
{
  while($row = mysql_fetch_array($res))
  {
    $defences[$i] = $row;
    $i++;
  }
  mysql_free_result($res);
}
$num_defences = $i;


$res = mysql_query("SELECT zone_id,zone_name FROM zones WHERE zone_id='$sectorinfo[zone_id]'");
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
<tr><td align="center" colspan=3><font color=silver size=<? echo $basefontsize + 2; ?> face="arial">Player <b><font color=white><? echo $playerinfo[character_name];?></font></b>, aboard the <b><font color=white><a href="report.$phpext"><? echo $playerinfo[ship_name] ?></a></font></b>
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
<a href="<? echo "zoneinfo.$phpext?zone=$zoneinfo[zone_id]"; ?>"><b><? echo "<font size=", $basefontsize + 2," face=\"arial\">$zoneinfo[zone_name]</font>"; ?></b></font></a>&nbsp;
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
&nbsp;<a class=mnu href="device.$phpext">Devices</a>&nbsp;<br>
&nbsp;<a class=mnu href="planet-report.$phpext">Planets</a>&nbsp;<br>
&nbsp;<a class=mnu href="log.$phpext">Log</a>&nbsp;<br>
&nbsp;<a class=mnu href="defence-report.$phpext">Sector Defences</a>&nbsp;<br>
&nbsp;<a class=mnu href="readmail.$phpext">Read Messages</A>&nbsp;<br> <? # Link to read the messages -- blindcoder ?>
&nbsp;<a class=mnu href="mailto2.$phpext">Send Message</a>&nbsp;<br>
&nbsp;<a class=mnu href="ranking.$phpext">Rankings</a>&nbsp;<br>
&nbsp;<a class=mnu href="teams.php">Alliances</a>&nbsp;<br>
&nbsp;<a class=mnu href="self-destruct.$phpext">Self-Destruct</a>&nbsp;<br>
&nbsp;<a class=mnu href="options.$phpext">Options</a>&nbsp;<br>
&nbsp;<a class=mnu href="navcomp.$phpext">Nav Computer</a>&nbsp;<br>
</div>
</td></tr>
<tr><td nowrap>
<div class=mnu>
<? //&nbsp;<a class=mnu href="help.$phpext">Help</a>&nbsp;<br> ?>
&nbsp;<a class=mnu href="http://copland.udel.edu/~wallkk/bnfaq/">FAQ</a>&nbsp;<br>
&nbsp;<a class=mnu href="feedback.$phpext">Feedback</a>&nbsp;<br>
<?
if(!empty($link_forums))
{
    echo "&nbsp;<a class=mnu href=$link_forums TARGET=\'_blank\'>Forums</a>&nbsp;<br>";
}
?>
</div>
</td></tr>
<tr><td nowrap>
&nbsp;<a class=mnu href="logout.$phpext">Logout</a>&nbsp;<br>
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
     echo "&nbsp;<a class=mnu href=move.$phpext?sector=$links[$i]>=&gt;&nbsp;$links[$i]</a>&nbsp;<a class=dis href=lrscan.$phpext?sector=$links[$i]>[scan]</a>&nbsp;<br>";
     $link_bnthelper_string=$link_bnthelper_string . ":" . $links[$i];
  }
}
$link_bnthelper_string=$link_bnthelper_string . ":-->";
echo "</div>";
echo "</td></tr>";
echo "<tr><td nowrap align=center>";
echo "<div class=mnu>";
echo "&nbsp;<a class=dis href=lrscan.$phpext?sector=*>[Full scan]</a>&nbsp;<br>";
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
  echo "<a href=port.$phpext>", ucfirst($sectorinfo[port_type]), "</a>";
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
    echo "<A HREF=planet.$phpext?planet_id=" . $planets[$i][planet_id] . ">";
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
      $planet_bnthelper_string=$planet_bnthelper_string . $planet_owner[character_name] . ":N:-->";
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
  $result4 = mysql_query(" SELECT
                              ships.*,
                              teams.team_name,
                              teams.id
                           FROM ships
                              LEFT OUTER JOIN teams
                              ON ships.team = teams.id
                           WHERE ships.ship_id<>$playerinfo[ship_id]
                           AND ships.sector=$playerinfo[sector]
                           AND ships.on_planet='N'");
   $totalcount=0;

   if($result4 > 0)
   {
      $curcount=0;
      echo "<td align=center colspan=99 valign=top>
      <table width=100% border=0>
         <tr>";
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

            if ($row[team_name]) {
               echo "<a href=ship.$phpext?ship_id=$row[ship_id]><img src=\"images/", $shiptypes[$shiplevel],"\" border=0></a><BR><font size=", $basefontsize +1, " color=#ffffff face=\"arial\">$row[ship_name]<br>($row[character_name])&nbsp;(<font color=#33ff00>$row[team_name]</font>)</font>";
            }
            else
            {
               echo "<a href=ship.$phpext?ship_id=$row[ship_id]><img src=\"images/", $shiptypes[$shiplevel],"\" border=0></a><BR><font size=", $basefontsize +1, " color=#ffffff face=\"arial\">$row[ship_name]<br>($row[character_name])</font>";
            }

            echo "</td>";

            $totalcount++;

            if($curcount == $picsperrow - 1)
            {
               echo "</tr><tr>";
               $curcount=0;
            }
            else
            {
               $curcount++;
            }
         }
         if($result4 == 0 || $totalcount == 0)
         {
            echo "<td align=center>";
            echo "<br><font color=white>None</font><br><br>";
            echo "</td>";
            $displayed=true;
            break;
         }
      }
   echo "    </tr>
           </table>
         </td>";
}
   if($result4 == 0 || $totalcount == 0 && $displayed != true)
   {
      echo "<tr><td align=center>";
      echo "<br><font color=white size=2>None</font><br><br>";
      echo "</td></tr>";
   }
}
else
{
    echo "<td align=center valign=top>";
    echo "<br><font color=white>There is so much traffic in Sol (Sector 0) that you cannot even isolate other ships!</font><br><br>";
    echo "</td>";

}
?>
</td>
</tr>
</table>
<?
if($num_defences>0) echo "<b><center><font size=2 face=\"arial\" color=white>Sector Defences</font><br></center></b>";
?>
<table border=0 width=100%>
<tr>
<?
if($num_defences > 0)
{
  $totalcount=0;
  $curcount=0;
  $i=0;
  while($i < $num_defences)
  {

    $defence_id = $defences[$i]['defence_id'];
    echo "<td align=center valign=top>";
    if($defences[$i]['defence_type'] == 'F')
    {
       echo "<a href=modify-defences.php?defence_id=$defence_id><img src=\"images/fighters.gif\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
       $def_type = 'Fighters ';
       $def_type .= $defences[$i]['fm_setting'];
    }
    elseif($defences[$i]['defence_type'] == 'M')
    {
       echo "<a href=modify-defences.php?defence_id=$defence_id><img src=\"images/mines.gif\" border=0></a><BR><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
       $def_type = 'Mines ';
    }
    $char_name = $defences[$i]['character_name'];
    $qty = $defences[$i]['quantity'];
    echo "$char_name ( $qty $def_type )";
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
  echo "</td></tr></table>";
}
else
{
  echo "<td align=center valign=top>";
//  echo "<br><font color=white size=", $basefontsize +2, ">None</font><br><br>";
  echo "</td></tr></table>";
}
?>


<br>
<?

?>



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
<img align=absmiddle height=12 width=12 alt="Organics" src="images/organics.gif">&nbsp;Organics&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_organics]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Goods" src="images/goods.gif">&nbsp;Goods&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_goods]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Energy" src="images/energy.gif">&nbsp;Energy&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_energy]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Colonists" src="images/colonists.gif">&nbsp;Colonists&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[ship_colonists]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="Credits" src="images/credits.gif">&nbsp;Credits&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($playerinfo[credits]); ?>&nbsp</div>
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
Trade Routes
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

  $query = mysql_query("SELECT * FROM traderoutes WHERE source_type='P' AND source_id=$playerinfo[sector] AND owner=$playerinfo[ship_id] ORDER BY dest_id ASC");
  $i=0;
  $num_traderoutes = 0;
  while($row = mysql_fetch_array($query))
  {
    $traderoutes[$i]=$row;
    $i++;
    $num_traderoutes++;
  }
  $query = mysql_query("SELECT traderoute_id, dest_type, dest_id, source_type, name, sector_id FROM planets, traderoutes WHERE source_type='L' AND source_id=planets.planet_id AND planets.sector_id=$playerinfo[sector] AND traderoutes.owner=$playerinfo[ship_id]");
  while($row = mysql_fetch_array($query))
  {
    $traderoutes[$i]=$row;
    $i++;
    $num_traderoutes++;
  }

  if($num_traderoutes == 0)
    echo "<a class=dis><center>&nbsp;None&nbsp;</center></a>";
  else
  {
    $i=0;
    while($i<$num_traderoutes)
    {
      echo "&nbsp;<a class=mnu href=traderoute.php?engage=" . $traderoutes[$i][traderoute_id] . ">";
      if($traderoutes[$i][source_type] == 'P')
        echo "Port&nbsp;";
      else
      {
        if($traderoutes[$i][name] == "")
          $traderoutes[$i][name] == "Unnamed";
        echo $traderoutes[$i][name] . "&nbsp;";
      }

      echo "=&gt;&nbsp;";

      if($traderoutes[$i][dest_type] == 'P')
        echo $traderoutes[$i][dest_id];
      else
      {
        $query = mysql_query("SELECT name FROM planets WHERE planet_id=" . $traderoutes[$i][dest_id]);
        if(empty($query) || mysql_num_rows($query) == 0)
          echo "Unknown";
        else
        {
          $planet = mysql_fetch_array($query);
          if($planet[name] == "")
            echo "Unnamed";
          else
            echo $planet[name];
        }
      }
      echo "</a>&nbsp;<br>";
      $i++;
    }
  }

?>

</div>
</a>
</td></tr>
<tr><td nowrap>
<div class=mnu>
&nbsp;<a class=mnu href=traderoute.php>Trade Control</a>&nbsp;<br>
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
&nbsp;<a class=mnu href=rsmove.$phpext?engage=1&destination=<? echo $playerinfo[preset1]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset1]; ?></a>&nbsp;<a class=dis href=preset.$phpext>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.$phpext?engage=1&destination=<? echo $playerinfo[preset2]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset2]; ?></a>&nbsp;<a class=dis href=preset.$phpext>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.$phpext?engage=1&destination=<? echo $playerinfo[preset3]; ?>>=&gt;&nbsp;<? echo $playerinfo[preset3]; ?></a>&nbsp;<a class=dis href=preset.$phpext>[set]</a>&nbsp;<br>
&nbsp;<a class=mnu href=rsmove.$phpext>=&gt;&nbsp;Other</a>&nbsp;<br>
</div>
</a>
</td></tr>
</table>

</td>
</tr>

</table>


<?


//-------------------------------------------------------------------------------------------------

$player_bnthelper_string="<!--player info:" . $playerinfo[hull] . ":" .  $playerinfo[engines] . ":"  .  $playerinfo[power] . ":" .  $playerinfo[computer] . ":" . $playerinfo[sensors] . ":" .  $playerinfo[beams] . ":" . $playerinfo[torp_launchers] . ":" .  $playerinfo[torps] . ":" . $playerinfo[shields] . ":" .  $playerinfo[armour] . ":" . $playerinfo[armour_pts] . ":" .  $playerinfo[cloak] . ":" . $playerinfo[credits] . ":" .  $playerinfo[sector] . ":" . $playerinfo[ship_ore] . ":" .  $playerinfo[ship_organics] . ":" . $playerinfo[ship_goods] . ":" .  $playerinfo[ship_energy] . ":" . $playerinfo[ship_colonists] . ":" .  $playerinfo[ship_fighters] . ":" . $playerinfo[turns] . ":" .  $playerinfo[on_planet] . ":" . $playerinfo[dev_warpedit] . ":" .  $playerinfo[dev_genesis] . ":" . $playerinfo[dev_beacon] . ":" .  $playerinfo[dev_emerwarp] . ":" . $playerinfo[dev_escapepod] . ":" .  $playerinfo[dev_fuelscoop] . ":" . $playerinfo[dev_minedeflector] . ":-->";
$rspace_bnthelper_string="<!--rspace:" . $sectorinfo[distance] . ":" . $sectorinfo[angle1] . ":" . $sectorinfo[angle2] . ":-->";
echo $player_bnthelper_string;
echo $link_bnthelper_string;
echo $port_bnthelper_string;
echo $planet_bnthelper_string;
echo $rspace_bnthelper_string;

include("footer.$phpext");

?>
