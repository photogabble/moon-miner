<?
include("config.php3");
updatecookie();

include($gameroot . $default_lang);
$title=$l_lrs_title;
include("header.php3");

connectdb();
if(checklogin())
{
  die();
}

bigtitle();

srand((double)microtime() * 1000000);

//-------------------------------------------------------------------------------------------------


// get user info
$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

if($sector == "*")
{
  if(!$allow_fullscan)
  {
    echo "$l_lrs_nofull<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php3");
    die();
  }
  if($playerinfo[turns] < $fullscan_cost)
  {
    echo "$l_lrs_noturns<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php3");
    die();
  }

  echo "$l_lrs_used " . NUMBER($fullscan_cost) . " $l_lrs_turn. " . NUMBER($playerinfo[turns] - $fullscan_cost) . " $l_lrs_left.<BR><BR>";

  // deduct the appropriate number of turns
  mysql_query("UPDATE ships SET turns=turns-$fullscan_cost, turns_used=turns_used+$fullscan_cost where ship_id='$playerinfo[ship_id]'");

  // user requested a full long range scan
  $l_lrs_reach=str_replace("[sector]",$playerinfo[sector],$l_lrs_reach);
  echo "$l_lrs_reach<BR><BR>";

  // get sectors which can be reached from the player's current sector
  $result = mysql_query("SELECT * FROM links WHERE link_start='$playerinfo[sector]' ORDER BY link_dest");
  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_sector</B><TD></TD></TD><TD><B>$l_lrs_links</B></TD><TD><B>$l_lrs_ships</B></TD><TD colspan=2><B>$l_port</B></TD><TD><B>$l_planets</B></TD><TD><B>$l_ines</B></TD><TD><B>$l_fighters</B></TD></TR>";
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
    $result2 = mysql_query("SELECT port_type FROM universe WHERE sector_id='$row[link_dest]'");
    $result3 = mysql_query("SELECT planet_id FROM planets WHERE sector_id='$row[link_dest]'");
    $resultSDa = mysql_query("SELECT SUM(quantity) as mines from sector_defence WHERE sector_id='$row[link_dest]' and defence_type='M'");
    $resultSDb = mysql_query("SELECT SUM(quantity) as fighters from sector_defence WHERE sector_id='$row[link_dest]' and defence_type='F'");

    $sectorinfo = mysql_fetch_array($result2);
    $defM = mysql_fetch_array($resultSDa);
    $defF = mysql_fetch_array($resultSDb);
    $port_type = $sectorinfo[port_type];
    $has_planet = mysql_num_rows($result3);
    $has_mines = NUMBER($defM[mines]);
    $has_fighters = NUMBER($defF[fighters]);


    if ($port_type != "none") {
      $icon_alt_text = ucfirst($port_type);
      $icon_port_type_name = $port_type . ".gif";
      $image_string = "<img align=absmiddle height=12 width=12 alt=\"$icon_alt_text\" src=\"images/$icon_port_type_name\">&nbsp;";
    } else {
      $image_string = "&nbsp;";
    }


    echo "<TR BGCOLOR=\"$color\"><TD><A HREF=move.php3?sector=$row[link_dest]>$row[link_dest]</A></TD><TD><A HREF=lrscan.php3?sector=$row[link_dest]>Scan</A></TD><TD>$num_links</TD><TD>$num_ships</TD><TD WIDTH=12>$image_string</TD><TD>$port_type</TD><TD>$has_planet</TD><TD>$has_mines</TD><TD>$has_fighters</TD></TR>";
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
    echo "$l_none.";
  }
  else
  {
    echo "<BR>$l_lrs_click";
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
    echo "$l_lrs_cantscan<BR><BR>";
    TEXT_GOTOMAIN();
    die();
  }

  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_sector $sector";
  if($sectorinfo[sector_name] != "")
  {
    echo " ($sectorinfo[sector_name])";
  }
  echo "</B></TR>";
  echo "</TABLE><BR>";

  echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\">";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_links</B></TD></TR>";
  echo "<TR><TD>";
  if($num_links == 0)
  {
    echo "$l_none";
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
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_ships</B></TD></TR>";
  echo "<TR><TD>";
  if($sector != 0)
  {
    // get ships located in the scanned sector
    $result4 = mysql_query("SELECT ship_id,ship_name,character_name,cloak FROM ships WHERE sector='$sector' AND on_planet='N'");
    if(mysql_num_rows($result4) < 1)
    {
      echo "$l_none";
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
        echo "$l_none";
      }
    }
  }
  else
  {
    echo "$l_lrs_zero";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_port</B></TD></TR>";
  echo "<TR><TD>";
  if($sectorinfo[port_type] == "none")
  {
    echo "$l_none";
    $port_bnthelper_string="<!--port:none:0:0:0:0:-->";
  }
  else
  {
    if ($sectorinfo[port_type] != "none") {
      $port_type = $sectorinfo[port_type];
      $icon_alt_text = ucfirst($port_type);
      $icon_port_type_name = $port_type . ".gif";
      $image_string = "<img align=absmiddle height=12 width=12 alt=\"$icon_alt_text\" src=\"images/$icon_port_type_name\">";
    }
    echo "$image_string $sectorinfo[port_type]";

    $port_bnthelper_string="<!--port:" . $sectorinfo[port_type] . ":" . $sectorinfo[port_ore] . ":" . $sectorinfo[port_organics] . ":" . $sectorinfo[port_goods] . ":" . $sectorinfo[port_energy] . ":-->";
  }
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_planets</B></TD></TR>";
  echo "<TR><TD>";
  $query = mysql_query("SELECT name, owner FROM planets WHERE sector_id=$sectorinfo[sector_id]");
  if(mysql_num_rows($query) > 0)
  {
    $planet = mysql_fetch_array($query);
    if(empty($planet[name]))
      echo "$l_unnamed";
    else
      echo "$planet[name]";

    if($planet[owner] == 0)
    {
      echo " ($l_unowned)";
    }
    else
    {
      $result5 = mysql_query("SELECT character_name FROM ships WHERE ship_id=$planet[owner]");
      $planet_owner_name = mysql_fetch_array($result5);
      echo " ($planet_owner_name[character_name])";
    }
    while($planet = mysql_fetch_array($query))
    {
      echo "<BR>";
      if(empty($planet[name]))
        echo "$l_unnamed";
      else
        echo "$planet[name]";

      if($planet[owner] == 0)
      {
        echo " ($l_unowned)";
      }
      else
      {
        $result5 = mysql_query("SELECT character_name FROM ships WHERE ship_id=$planet[owner]");
        $planet_owner_name = mysql_fetch_array($result5);
        echo " ($planet_owner_name[character_name])";
      }
    }
  }
  else
  {
    echo "$l_none";
    $planet_bnthelper_string="<!--planet:N:::-->";
  }
  $resultSDa = mysql_query("SELECT SUM(quantity) as mines from sector_defence WHERE sector_id='$sector' and defence_type='M'");
  $resultSDb = mysql_query("SELECT SUM(quantity) as fighters from sector_defence WHERE sector_id='$sector' and defence_type='F'");
  $defM = mysql_fetch_array($resultSDa);
  $defF = mysql_fetch_array($resultSDb);

  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD><B>$l_mines</B></TD></TR>";
  $has_mines =  NUMBER($defM[mines] ) ;
  echo "<TR><TD>" . $has_mines;
  echo "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_fighters</B></TD></TR>";
  $has_fighters =  NUMBER($defF[fighters] ) ;
  echo "<TR><TD>" . $has_fighters;
  echo "</TD></TR>";
  echo "</TABLE><BR>";

  echo "<a href=move.php3?sector=$sector>$l_clickme</a> $l_lrs_moveto $sector.";
}


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
