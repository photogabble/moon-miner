<?

include("config.php3");
$title="System Update";

include("header.php3");
connectdb();

bigtitle();

function QUERYOK($res)
{
  if($res)
  {
    echo " ok.<BR>";
  }
  else
  {
    die(" FAILED.");
  }
}

if($swordfish != $adminpass) 
{
  echo "<FORM ACTION=sysupdate.php3 METHOD=POST>";
  echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Submit><INPUT TYPE=RESET VALUE=Reset>";
  echo "</FORM>";
}
else
{
  srand((double)microtime() * 1000000);
  
  //-------------------------------------------------------------------------------------------------
  mysql_query("LOCK TABLES ships WRITE, universe WRITE, ibank_accounts WRITE, zones READ, planets WRITE");

  // add turns
  echo "<B>TURNS</B><BR><BR>";
  echo "Adding turns...";
  QUERYOK(mysql_query("UPDATE ships SET turns=turns+1 WHERE turns<$max_turns"));
  //Someone explain why we are doing this???  If they get neg turns then they can stay.
  //echo "Ensuring minimum turns are 0...";
  //QUERYOK(mysql_query("UPDATE ships SET turns=0 WHERE turns<0"));
  echo "Ensuring maximum turns are $max_turns...";
  QUERYOK(mysql_query("UPDATE ships SET turns=$max_turns WHERE turns>$max_turns"));
  echo "<BR>";
  
  // add commodities to ports
  echo "<B>PORTS</B><BR><BR>";
  echo "Adding ore to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=port_ore+$ore_rate WHERE port_type='ore' AND port_ore<$ore_limit"));
  echo "Adding ore to all ore ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=port_ore+$ore_rate WHERE port_type!='special' AND port_type!='none' AND port_ore<$ore_limit"));
  echo "Ensuring minimum ore levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_ore=0 WHERE port_ore<0"));
  echo "<BR>";
  echo "Adding organics to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=port_organics+$organics_rate WHERE port_type='organics' AND port_organics<$organics_limit"));
  echo "Adding organics to all organics ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=port_organics+$organics_rate WHERE port_type!='special' AND port_type!='none' AND port_organics<$organics_limit"));
  echo "Ensuring minimum organics levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_organics=0 WHERE port_organics<0"));
  echo "<BR>";
  echo "Adding goods to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=port_goods+$goods_rate WHERE port_type='goods' AND port_goods<$goods_limit"));
  echo "Adding goods to all goods ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=port_goods+$goods_rate WHERE port_type!='special' AND port_type!='none' AND port_goods<$goods_limit"));
  echo "Ensuring minimum goods levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_goods=0 WHERE port_goods<0"));
  echo "<BR>";
  echo "Adding energy to all commodities ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=port_energy+$energy_rate WHERE port_type='energy' AND port_energy<$energy_limit"));
  echo "Adding energy to all energy ports...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=port_energy+$energy_rate WHERE port_type!='special' AND port_type!='none' AND port_energy<$energy_limit"));
  echo "Ensuring minimum energy levels are 0...";
  QUERYOK(mysql_query("UPDATE universe SET port_energy=0 WHERE port_energy<0"));
  echo "<BR>";
  
  // update planet production
  echo "<B>PLANETS</B><BR><BR>";
  $res = mysql_query("SELECT * FROM planets");
  while($row = mysql_fetch_array($res))
  {
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
        playerlog($row[owner], "Your planet in sector $row[sector_id] had too little food and $starvation people died!");
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
    mysql_query("UPDATE planets SET organics=organics+$organics_production, ore=ore+$ore_production, goods=goods+$goods_production, energy=energy+$energy_production, colonists=colonists+$reproduction-$starvation, torps=torps+$torp_production, fighters=fighters+$fighter_production, credits=credits*$interest_rate+$credits_production WHERE planet_id=$row[planet_id]");
  }
  mysql_free_result($res);
  echo "Planets updated.<BR><BR>";
  echo "<BR>";
  
  // update planet production
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
        $query = mysql_query("UPDATE ships SET sector=$newsector where ship_id=$row[ship_id]");
        playerlog($row[ship_id], "Your ship was towed from sector $row[sector] to sector $newsector because your hull size exceeded $row[max_hull].");
      }
      mysql_free_result($res);
    }
    else
    {
      echo "<BR>No players to tow.<BR>";
    }
  } while($num_to_tow);
  echo "<BR>";

  // proceed with iBank maintenance
  echo "<B>IBANK</B><BR><BR>";
  $ibank_result = mysql_query("SELECT * from ibank_accounts");
  $num_accounts = mysql_num_rows($ibank_result);

  if($num_accounts > 0)
  {
    for($i=1; $i<=$num_accounts ; $i++)
    {
	    $account = mysql_fetch_array($ibank_result);
	    // Check if the user actually has a ballance on his acount
	    if($account[ballance] > 0)
	    {
		    // Calculate Interest
		    $interest = round($ibank_interest * $account[ballance]);
		    // Calculate Mortage
		    $mortage_interest = round($ibank_loaninterest * $account[loan]);
		    $mortage_payment = round($mortage_interest * 2);
		    // Update users bank account
		    mysql_query("UPDATE ibank_accounts SET ballance = ballance + $interest WHERE id = $account[id]");
		    // Update the banks main account
		    mysql_query("UPDATE ibank_accounts SET ballance = ballance - $interest WHERE id = $bank_owner");			
		    // Check if the user has a loan
		    if($account[loan] > 0)
		    {
			    // Decide what type of repayment should be done.
			    if($account[ballance] < $mortage_payment)
			    {	// The user don't have enough money on his IGB account then we start collecting from his ship account 
				    // at twice the cost, for the extra trouble. This is in the Information at Manage own account.
				    $extrafee = $mortage_payment * 2;
				    mysql_query("UPDATE ibank_accounts SET loan = loan - $mortage_interest WHERE id = $account[id]");
				    mysql_query("UPDATE ships SET credits = credits - $extrafee WHERE ship_id = $account[id]");
			    }
			    else
			    {	// Normal repayment / mortage
				    mysql_query("UPDATE ibank_accounts SET ballance = ballance - $mortage_payment, loan = loan - $mortage_interest WHERE id = $account[id]");
			    }
			    mysql_query("UPDATE ibank_accounts SET ballance = ballance + $mortage_payment WHERE id = $bank_owner");
		    }
		    echo "ID: $account[id] Ballance: $account[ballance] Interest: $interest - Loan: $account[loan] Mortage: $mortage_payment<br>\n";
	    }
    }
  }

  mysql_query("UNLOCK TABLES");
  //-------------------------------------------------------------------------------------------------
}

include("footer.php3");

?> 
