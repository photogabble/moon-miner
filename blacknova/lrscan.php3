<?

include("config.php3");
updatecookie();

$title="Long Range Scan";
include("header.php3");

connectdb();
if(checklogin())
{
  die();
}

bigtitle();

srand((double)microtime() * 1000000);

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships WRITE, universe READ, links READ");

// get user info
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

if($sector == "*")
{
  if(!$allow_fullscan)
  {
    echo "Your scanners do not possess full long range scan capabilities.<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php3");   
    die();
  }
  if($playerinfo[turns] < $fullscan_cost)
  {
    echo "You need at least $fullscan_cost turn(s) to run a full long range scan.<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php3");   
    die();
  }

  echo "Used " . NUMBER($fullscan_cost) . " turn(s). " . NUMBER($playerinfo[turns] - $fullscan_cost) . " left.<BR><BR>";

  // deduct the appropriate number of turns
  mysql_query("UPDATE ships SET turns=turns-$fullscan_cost, turns_used=turns_used+$fullscan_cost where ship_id='$playerinfo[ship_id]'");

  // user requested a full long range scan
  echo "The following locations can be reached from sector $playerinfo[sector]:<BR><BR>";

  // get sectors which can be reached from the player's current sector
  $result = mysql_query("SELECT * FROM links WHERE link_start='$playerinfo[sector]' ORDER BY link_dest");
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>Sector</B><TD></TD></TD><TD><B>Links</B></TD><TD><B>Ships</B></TD><TD><B>Port</B></TD><TD><B>Planet</B></TD><TD><B>Mines</B></TD><TD><B>Fighters</B></TD></TR>";
  $color = $color_line1;
  while($row = mysql_fetch_array($result))
  {
    // get number of sectors which can be reached from scanned sector
    $result2 = mysql_query("SELECT COUNT(*) AS count FROM links WHERE link_start='$row[link_dest]'");
    $row2 = mysql_fetch_array($result2);
    $num_links = $row2[count];

    // get number of ships in scanned sector
    $result2 = mysql_query("SELECT COUNT(*) AS count FROM ships WHERE sector='$row[link_dest]' AND on_planet='N'");
    $row2 = mysql_fetch_array($result2);
    $num_ships = $row2[count];

    // get port type and discover the presence of a planet in scanned sector
    $result2 = mysql_query("SELECT port_type,planet,mines,fighters FROM universe WHERE sector_id='$row[link_dest]'");
    $sectorinfo = mysql_fetch_array($result2);
    $port_type = $sectorinfo[port_type];
    $has_planet = ($sectorinfo[planet] == "Y") ? "Yes" : "No";
    $has_mines = NUMBER($sectorinfo[mines] ) ;
    $has_fighters = NUMBER($sectorinfo[fighters] ) ;

    echo "<TR BGCOLOR=\"$color\"><TD><A HREF=move.php3?sector=$row[link_dest]>$row[link_dest]</A></TD><TD><A HREF=lrscan.php3?sector=$row[link_dest]>Scan</A></TD><TD>$num_links</TD><TD>$num_ships</TD><TD>$port_type</TD><TD>$has_planet</TD><TD>$has_mines</TD><TD>$has_fighters</TD></TR>";
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

  if($num_links == 0)
  {
    echo "None.";
  }
  else
  {
    echo "<BR>Click one of the links to move to that sector.";
  }

}
else
{
  // user requested a single sector (standard) long range scan

  // get scanned sector information
  $result2 = mysql_query("SELECT * FROM universe WHERE sector_id='$sector'");
  $sectorinfo = mysql_fetch_array($result2);

  // get sectors which can be reached through scanned sector
  $result3 = mysql_query("SELECT link_dest FROM links WHERE link_start='$sector' ORDER BY link_dest ASC");

  $i=0;

  if($result3 > 0)
  {
    while($row = mysql_fetch_array($result3))
    {
      $links[$i] = $row[link_dest];
      $i++;
    }
  }
  $num_links=$i;

  // get sectors which can be reached from the player's current sector
  $result3a = mysql_query("SELECT link_dest FROM links WHERE link_start='$playerinfo[sector]'");

  $i=0;

  $flag=0;

  if($result3a > 0)
  {
    while($row = mysql_fetch_array($result3a))
    {
      if($row[link_dest] == $sector)
      {
        $flag=1;
      }
      $i++;
    }
  }

  if($flag == 0)
  {
    echo "Can't scan sector from current sector!<BR><BR>";
    TEXT_GOTOMAIN();
    die();
  }

  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>Sector $sector";
  if($sectorinfo[sector_name] != "")
  {
    echo " ($sectorinfo[sector_name])";
  }
  echo "</B></TR>";
  echo "</TABLE><BR>";

  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>Links</B></TD></TR>";
  echo "<TR><TD>";
  if($num_links == 0)
  {
    echo "None";
    $link_bnthelper_string="<!--links:N:-->";
  }
  else
  {
    $link_bnthelper_string="<!--links:Y";
    for($i = 0; $i < $num_links; $i++)
    {
      echo "$links[$i]";
      $link_bnthelper_string=$link_bnthelper_string . ":" . $links[$i];
      if($i + 1 != $num_links)
      {
        echo ", ";
      }
    }
    $link_bnthelper_string=$link_bnthelper_string . ":-->";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>Ships</B></TD></TR>";
  echo "<TR><TD>";
  if($sector != 0)
  {
    // get ships located in the scanned sector
    $result4 = mysql_query("SELECT ship_id,ship_name,character_name,cloak FROM ships WHERE sector='$sector' AND on_planet='N'");
    if(mysql_num_rows($result4) < 1)
    {
      echo "None";
    }
    else
    {
      $num_detected = 0;
      while($row = mysql_fetch_array($result4))
      {
        // display other ships in sector - unless they are successfully cloaked
        $success = SCAN_SUCCESS($playerinfo['sensors'], $row['cloak']);
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
          $num_detected++;
          echo $row['ship_name'] . "(" . $row['character_name'] . ")<BR>";
        }
      }
      if(!$num_detected)
      {
        echo "None";
      }
    }
  }
  else
  {
    echo "Sector 0 is too crowded to scan for ships!";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>Port</B></TD></TR>";
  echo "<TR><TD>";
  if($sectorinfo[port_type] == "none")
  {
    echo "None";
    $port_bnthelper_string="<!--port:none:0:0:0:0:-->";
  }
  else
  {
    echo "$sectorinfo[port_type]";
    $port_bnthelper_string="<!--port:" . $sectorinfo[port_type] . ":" . $sectorinfo[port_ore] . ":" . $sectorinfo[port_organics] . ":" . $sectorinfo[port_goods] . ":" . $sectorinfo[port_energy] . ":-->";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>Planet</B></TD></TR>";
  echo "<TR><TD>";
  if($sectorinfo[planet] == "N")
  {
    echo "None";
    $planet_bnthelper_string="<!--planet:N:::-->";
  }
  else
  {
    if(empty($sectorinfo[planet_name]))
    {
      echo "Unnamed";
      $planet_bnthelper_string="<!--planet:Y:Unnamed:";
    }
    else
    {
      echo "$sectorinfo[planet_name]";
      $planet_bnthelper_string="<!--planet:Y:" . $sectorinfo[planet_name] . ":";
    }
    if($sectorinfo[planet_owner] == 0)
    {
      echo " (unowned)";
      $planet_bnthelper_string=$planet_bnthelper_string . "Unowned:-->";
    }
    else
    {
      $result5 = mysql_query("SELECT character_name FROM ships WHERE ship_id=$sectorinfo[planet_owner]");
      $planet_owner_name = mysql_fetch_array($result5);
      echo " ($planet_owner_name[character_name])";
      $planet_bnthelper_string=$planet_bnthelper_string . $planet_owner_name[character_name] . ":-->";
    } 
  } 
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD><B>Mines</B></TD></TR>";
  $has_mines =  NUMBER($sectorinfo[mines] ) ;
  echo "<TR><TD>" . $has_mines;
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>Fighters</B></TD></TR>";
  $has_fighters =  NUMBER($sectorinfo[fighters] ) ;
  echo "<TR><TD>" . $has_fighters;
  echo "</TD></TR>";
  echo "</TABLE><BR>";
  echo "Click <a href=move.php3?sector=$sector>here</a> to move to sector $sector.";
}

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------
$rspace_bnthelper_string="<!--rspace:" . $sectorinfo[distance] . ":" . $sectorinfo[angle1] . ":" . $sectorinfo[angle2] . ":-->";
echo $link_bnthelper_string;
echo $port_bnthelper_string;
echo $planet_bnthelper_string;
echo $rspace_bnthelper_string;
echo "<BR><BR>";
TEXT_GOTOMAIN();

include("footer.php3");

?>
