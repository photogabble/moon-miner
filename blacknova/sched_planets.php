<?

  if(!isset($swordfish) || $swordfish != $adminpass)
    die("Script has not been called properly");

  echo "<B>PLANETS</B><BR><BR>";
  $res = $db->Execute("SELECT * FROM planets");
  while(!$res->EOF)
  {
    $row = $res->fields;
    $production = min($row[colonists], $colonist_limit) * $colonist_production_rate;

    $organics_production = ($production * $organics_prate * $row[prod_organics] / 100.0) - $production * $organics_consumption;
    if(($row[organics] + $organics_production) > $organics_limit)
    {
      $organics_production = 0;
    }
    if($row[organics] + $organics_production < 0)
    {
      $organics_production = -$row[organics];
      $starvation = floor(-($organics_test / $organics_consumption / $colonist_production_rate * $starvation_death_rate));
      if($row[owner] && $starvation > 0)
      {
        playerlog($row[owner], LOG_STARVATION, "$row[sector_id]|$starvation");
      }
    }
    else
    {
      $starvation = 0;
    }
    $ore_production = $production * $ore_prate * $row[prod_ore] / 100.0;
    if(($row[ore] + $ore_production) > $ore_limit)
    {
      $ore_production = 0;
    }

    $goods_production = $production * $goods_prate * $row[prod_goods] / 100.0;
    if(($row[goods] + $goods_production) > $goods_limit)
    {
      $goods_production = 0;
    }

    $energy_production = $production * $energy_prate * $row[prod_energy] / 100.0;
    if(($row[energy] + $energy_production) > $energy_limit)
    {
      $energy_production = 0;
    }

    $reproduction = round(($row[colonists] - $starvation) * $colonist_reproduction_rate);
    if(($row[colonists] + $reproduction - $starvation) > $colonist_limit)
    {
      $reproduction = $colonist_limit - $row[colonists] ;
    }
    $total_percent = $row[prod_organics] + $row[prod_ore] + $row[prod_goods] + $row[prod_energy];
    if($row[owner])
    {
      $fighter_production = $production * $fighter_prate * $row[prod_fighters] / 100.0;
      $torp_production = $production * $torpedo_prate * $row[prod_torp] / 100.0;
      $total_percent += $row[prod_fighters] + $row[prod_torp];
    }
    else
    {
      $fighter_production = 0;
      $torp_production = 0;
    }
    $credits_production = $production * $credits_prate * (100.0 - $total_percent) / 100.0;
    $db->Execute("UPDATE $dbtables[planets] SET organics=organics+$organics_production, ore=ore+$ore_production, goods=goods+$goods_production, energy=energy+$energy_production, colonists=colonists+$reproduction-$starvation, torps=torps+$torp_production, fighters=fighters+$fighter_production, credits=credits*$interest_rate+$credits_production WHERE planet_id=$row[planet_id]");
    $res->MoveNext();
  }
  echo "Planets updated.<BR><BR>";
  echo "<BR>";

?>