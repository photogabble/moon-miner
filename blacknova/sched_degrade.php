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
     $res2 = mysql_query("SELECT * from planets where (owner = $row[ship_id] or (corp = $sched_playerinfo[team] AND $sched_playerinfo[team] <> 0)) and sector_id = $row[sector_id] and energy > 0"); 
     echo mysql_error();
     if(mysql_num_rows($res2) < 1)
     {     
        mysql_query("UPDATE sector_defence set quantity = quantity - GREATEST(ROUND(quantity * $defence_degrade_rate),1) where defence_id = $row[defence_id] and quantity > 0");
        $degrade_rate = $defence_degrade_rate * 100;
        playerlog($row[ship_id], LOG_DEFENCE_DEGRADE, "$row[sector_id]|$degrade_rate");
     }
     else
     {
        $energy_required = ROUND($row[quantity] * $energy_per_fighter);
        $res4 = mysql_query("SELECT IFNULL(SUM(energy),0) as energy_available from planets where (owner = $row[ship_id] or (corp = $sched_playerinfo[team] AND $sched_playerinfo[team] <> 0)) and sector_id = $row[sector_id]"); 
        echo mysql_error();
        $planet_energy = mysql_fetch_array($res4);
        $energy_available = $planet_energy[energy_available];
        echo "available $energy_available, required $energy_required.";
        if($energy_available > $energy_required)
        {
           while($degrade_row = mysql_fetch_array($res2))
           {
              mysql_query("UPDATE planets set energy = energy - GREATEST(ROUND($energy_required * (energy / $energy_available)),1)  where planet_id = $degrade_row[planet_id] ");
              echo mysql_error();
           }
        }
        else
        {
           mysql_query("UPDATE sector_defence set quantity = quantity - GREATEST(ROUND(quantity * $defence_degrade_rate),1) where defence_id = $row[defence_id] ");
           $degrade_rate = $defence_degrade_rate * 100;
           playerlog($row[ship_id], LOG_DEFENCE_DEGRADE, "$row[sector_id]|$degrade_rate");  
        }
        
     }
  }
  mysql_query("DELETE from sector_defence where quantity <= 0");
  mysql_free_result($res);
?>
