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

echo "<FONT FACE=\"Arial\">";
bigtitle();

// get user info
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

if($sector == "*")
{
  if($playerinfo[turns] < 1)
  {
    echo "You need at least one turn to run a full long range scan.<BR><BR>";
    echo "Click <a href=main.php3>here</a> to return to Main Menu.";
    include("footer.php3");   
    die();
  }

  echo "Used $fullscan_cost turn(s). $playerinfo[turns] left.<BR><BR>";

  // deduct the appropriate number of turns
  mysql_query("UPDATE ships SET turns=turns-$fullscan_cost, turns_used=turns_used+$fullscan_cost where ship_id='$playerinfo[ship_id]'");

  // user requested a full long range scan
  echo "The following locations can be reached from sector $playerinfo[sector]:<BR><BR>";

  // get sectors which can be reached from the player's current sector
  $result = mysql_query("SELECT * FROM links WHERE link_start='$playerinfo[sector]' ORDER BY link_dest");
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
  echo "<FONT COLOR=\"WHITE\">";
  echo "<TR BGCOLOR=\"SILVER\"><TD><B>Sector</B><TD></TD></TD><TD><B>Links</B></TD><TD><B>Ships</B></TD><TD><B>Port</B></TD><TD><B>Planet</B></TD></TR>";
  echo "</FONT>";
  $color = "WHITE";
  while($row = mysql_fetch_array($result))
  {
    // get number of sectors which can be reached from scanned sector
    $result2 = mysql_query("SELECT COUNT(*) AS count FROM links WHERE link_start='$row[link_dest]'");
    $row2 = mysql_fetch_array($result2);
    $num_links = $row2[count];

    // get number of ships in scanned sector
    $result2 = mysql_query("SELECT COUNT(*) AS count FROM ships WHERE sector='$row[link_dest]'");
    $row2 = mysql_fetch_array($result2);
    $num_ships = $row2[count];

    // get port type and discover the presence of a planet in scanned sector
    $result2 = mysql_query("SELECT port_type,planet FROM universe WHERE sector_id='$row[link_dest]'");
    $sectorinfo = mysql_fetch_array($result2);
    $port_type = $sectorinfo[port_type];
    $has_planet = ($sectorinfo[planet] == "Y") ? "Yes" : "No";

    echo "<TR BGCOLOR=\"$color\"><TD><A HREF=move.php3?sector=$row[link_dest]>$row[link_dest]</A></TD><TD><A HREF=lrscan.php3?sector=$row[link_dest]>Scan</A></TD><TD>$num_links</TD><TD>$num_ships</TD><TD>$port_type</TD><TD>$has_planet</TD></TR>";
    if($color == "WHITE")
    {
      $color = "LIGHTGREY";
    }
    else
    {
      $color = "WHITE";
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
  $result3 = mysql_query("SELECT link_dest FROM links WHERE link_start='$sector'");

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
    echo "Can't scan sector from current sector! Click <a href=main.php3>here</a> to go back.";
    die();
  }

  echo "Long Range Scan of Sector #$sector";
  if($sectorinfo[sector_name] != "")
  {
    echo " ($sectorinfo[sector_name]).<BR><BR>";
  }
  else
  {
    echo ".<BR><BR>";
  }

  if($num_links == 0)
  {
    echo "There are no links out of this sector.<BR><BR>";
  }
  else
  {
    echo "Links lead to the following sectors: ";
    for($i = 0; $i < $num_links; $i++)
    {
      echo "$links[$i]";
      if($i + 1 != $num_links)
      {
        echo ", ";
      }
    }
    echo "<BR><BR>";
  }
  if($sector != 0)
  {
    // get ships located in the scanned sector
    $result4 = mysql_query("SELECT ship_id,ship_name FROM ships WHERE sector='$sector'");
    $i=0;
    if($result4 > 0)
    {
      while($row = mysql_fetch_array($result4))
      {
        $ships[$i] = $row[ship_name];
        $ship_id[$i] = $row[ship_id];
        $i++;
      }
    }
    $num_ships = $i;
    if($num_ships < 1)
    {
      echo "There are no ships in this sector.<BR><BR>";
    }
    else
    {
      echo "The following other ships are here: ";
      for($i = 0; $i < $num_ships; $i++)
      {
        if($ships[$i] != $playerinfo[ship_name])
        {
          echo "$ships[$i]";
          if($i + 1 != $num_ships)
          {
            echo " ";
          }
        }
      }
      echo "<BR><BR>";
    }
  }
  else
  {
    echo "Sector 0 is too congested to scan for ships!<BR><BR>";
  }

  if($sectorinfo[port_type] != "none")
  {
    echo "There is a $sectorinfo[port_type] port here.<BR><BR>";
  }
  if($sectorinfo[planet] == "Y" && $sectorinfo[sector_id] != 0)
  {
    echo "There is a planet here ";
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
      $planet_owner_name = mysql_fetch_array($result5);
      echo "owned by <a href=mailto.php3?to=$sectorinfo[planet_owner]>$planet_owner_name[character_name]</a> (#$sectorinfo[planet_owner])<BR><BR>";
    } 
  } 
  echo "Click <a href=move.php3?sector=$sector>here</a> to move to sector $sector.";
}

echo "<BR><BR>";
echo "Click <a href=main.php3>here</a> to return to main menu.";

echo "</FONT>";

include("footer.php3");

?>
