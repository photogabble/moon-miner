<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>Degrading Sector Fighters with no friendly base</B><BR><BR>";
  $res = mysql_query("SELECT * from sector_defence where defence_type = 'F'");
  echo mysql_error();
  while($row = mysql_fetch_array($res))
  {
     $res3 = mysql_query("SELECT * from ships where ship_id = $row[ship_id]");
     echo mysql_error();
     $sched_playerinfo = mysql_fetch_array($res3);
     $res2 = mysql_query("SELECT * from planets where (owner = $row[ship_id] or (corp = $sched_playerinfo[team] AND $sched_playerinfo[team] <> 0)) and sector_id = $row[sector_id]"); 
     echo mysql_error();
     if(mysql_num_rows($res2) < 1)
     {     
        mysql_query("UPDATE sector_defence set quantity = quantity - GREATEST(ROUND(quantity * $defence_degrade_rate),1) where defence_id = $row[defence_id] and quantity > 0");
        $degrade_rate = $defence_degrade_rate * 100;
        playerlog($row[ship_id], LOG_DEFENCE_DEGRADE, "$row[sector_id]|$degrade_rate");
     }
  }
  mysql_query("DELETE from sector_defence where quantity <= 0");
  mysql_free_result($res);
?>
