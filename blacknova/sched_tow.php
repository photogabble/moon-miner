<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>ZONES</B><BR><BR>";
  echo "Towing bigger players out of restricted zones...";
  $num_to_tow = 0;
  do
  {
    $res = mysql_query("SELECT ship_id,character_name,hull,sector,universe.zone_id,max_hull FROM ships,universe,zones WHERE sector=sector_id AND universe.zone_id=zones.zone_id AND max_hull<>0 AND ships.hull>max_hull AND ship_destroyed='N'");
    if($res)
    {
      $num_to_tow = mysql_num_rows($res);
      echo "<BR>$num_to_tow players to tow:<BR>";
      while($row = mysql_fetch_array($res))
      {
        echo "...towing $row[character_name] out of $row[sector] (max_hull=$row[max_hull] hull=$row[hull])...";
        $newsector = rand(0, $sector_max);
        echo " to sector $newsector.<BR>";
        $query = mysql_query("UPDATE ships SET sector=$newsector,cleared_defences=' ' where ship_id=$row[ship_id]");
        playerlog($row[ship_id], LOG_TOW, "$row[sector] $newsector $row[max_hull]");
      }
      mysql_free_result($res);
    }
    else
    {
      echo "<BR>No players to tow.<BR>";
    }
  } while($num_to_tow);
  echo "<BR>";
  
  $multiplier = 0; //no use to run this again
?>