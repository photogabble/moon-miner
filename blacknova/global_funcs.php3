<?
// Separate userpass into username & password to support the legacy of multiple cookies for login.
if ($userpass != '' and $userpass != '+') {
  $username = substr($userpass, 0, strpos($userpass, "+"));
  $password = substr($userpass, strpos($userpass, "+")+1);
}

function bigtitle()
{
  global $title;
  echo "<H1>$title</H1>\n";
}

function TEXT_GOTOMAIN()
{
  global $interface;
  echo "Click <A HREF=$interface>here</A> to return to the main menu.";
}

function TEXT_GOTOLOGIN()
{
  echo "Click <A HREF=login.php3>here</A> to return to the login screen.";
}

function TEXT_JAVASCRIPT_BEGIN()
{
  echo "\n<SCRIPT LANGUAGE=\"JavaScript\">\n";
  echo "<!--\n";
}

function TEXT_JAVASCRIPT_END()
{
  echo "\n// -->\n";
  echo "</SCRIPT>\n";
}

function checklogin()
{
  $flag = 0;

  global $username;
  global $password;

  $result1 = mysql_query("SELECT * FROM ships WHERE email='$username'");
  $playerinfo = mysql_fetch_array($result1);

  /* Check the cookie to see if username/password are empty - check password against database */
  if($username == "" or $password == "" or $password != $playerinfo['password'])
  {
    echo "You need to log in, click <A HREF=login.php3>here</A>.";
    $flag = 1;
  }

  /* Check for destroyed ship */
  if($playerinfo['ship_destroyed'] == "Y")
  {
    /* if the player has an escapepod, set the player up with a new ship */
    if($playerinfo['dev_escapepod'] == "Y")
    {
      $result2 = mysql_query("UPDATE ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage='', on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N' where email='$username'");
      echo "Your ship was destroyed, but your escape pods saved you and your crew.  Click <A HREF=$interface>here</A> to continue with a new ship.";
      $flag = 1;
    }
    else
    {
      /* if the player doesn't have an escapepod - they're dead, delete them. */
      /* uhhh  don't delete them to prevent self-distruct inherit*/  
      echo "Player is DEAD!  Here's what happened:<BR><BR>";
      
      include("player-log/" . $playerinfo['ship_id']);
      echo "Better luck next time.";
      $flag = 1;
    }
  }
  global $server_closed;
  global $server_closed_message;
  if($server_closed && $flag==0)
  {
    echo $server_closed_message;
    $flag=1;
  }



  return $flag;
}

function connectdb()
{
  /* connect to database - and if we can't stop right there */
  global $dbhost;
  global $dbport;
  global $dbuname;
  global $dbpass;
  global $dbname;
  mysql_connect($dbhost . ":" .$dbport, $dbuname, $dbpass);
  @mysql_select_db("$dbname") or die ("Unable to select database.");
}

function updatecookie()
{
  // refresh the cookie with username/password/id/res - times out after 60 mins, and player must login again.
  global $gamepath;
  global $gamedomain;
  global $userpass;
  global $username;
  global $password;
  global $id;
  global $res;
  // The new combined cookie login.
  $userpass = $username."+".$password;
  SetCookie("userpass",$userpass,time()+(3600*24)*365,$gamepath,$gamedomain);
  if ($userpass != '' and $userpass != '+') {
      setcookie("username","",0); // Legacy support, delete the old login cookies.
      setcookie("password","",0); // Legacy support, delete the old login cookies.
    $username = substr($userpass, 0, strpos($userpass, "+"));
    $password = substr($userpass, strpos($userpass, "+")+1);
  }
  setcookie("id", $id);
  setcookie("res", $res);
}


function playerlog($sid,$log_entry)
{
  /* write log_entry to the player's log - identified by player's ship_id - sid. */
  if ($sid != "") {
      $log_entry = date("l dS of F Y h:i:s A") . ":  " . $log_entry;
      $plog = fopen("player-log/" . $sid, "a");
      fwrite($plog, "$log_entry <BR>");
      fclose($plog);
  } 
}

function adminlog($sid,$log_entry)
{
  /* write log_entry to the admin log  */
  if ($sid != "") {
      $log_entry = date("l dS of F Y h:i:s A") . ":  " . $log_entry;
      $alog = fopen("admin.log","a");
      fwrite($alog, "$log_entry <BR>\n");
      fclose($alog);
  }
}

function gen_score($sid)
{
  global $ore_price;
  global $organics_price;
  global $goods_price;
  global $energy_price;
  global $upgrade_cost;
  global $upgrade_factor;
  global $dev_genesis_price;
  global $dev_beacon_price;
  global $dev_emerwarp_price;
  global $dev_warpedit_price;
  global $dev_minedeflector_price;
  global $dev_escapepod_price;
  global $dev_fuelscoop_price;
  global $fighter_price;
  global $torpedo_price;
  global $armour_price;
  global $colonist_price;
  global $base_ore;
  global $base_goods;
  global $base_organics;
  global $base_credits;
  
  $calc_hull = "ROUND(POW($upgrade_factor,hull))";
  $calc_engines = "ROUND(POW($upgrade_factor,engines))";
  $calc_power = "ROUND(POW($upgrade_factor,power))";
  $calc_computer = "ROUND(POW($upgrade_factor,computer))";
  $calc_sensors = "ROUND(POW($upgrade_factor,sensors))";
  $calc_beams = "ROUND(POW($upgrade_factor,beams))";
  $calc_torp_launchers = "ROUND(POW($upgrade_factor,torp_launchers))";
  $calc_shields = "ROUND(POW($upgrade_factor,shields))";
  $calc_armour = "ROUND(POW($upgrade_factor,armour))";
  $calc_cloak = "ROUND(POW($upgrade_factor,cloak))";
  $calc_levels = "($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak)*$upgrade_cost";

  $calc_torps = "ships.torps*$torpedo_price";
  $calc_armour_pts = "armour_pts*$armour_price";
  $calc_ship_ore = "ship_ore*$ore_price";
  $calc_ship_organics = "ship_organics*$organics_price";
  $calc_ship_goods = "ship_goods*$goods_price";
  $calc_ship_energy = "ship_energy*$energy_price";
  $calc_ship_colonists = "ship_colonists*$colonist_price";
  $calc_ship_fighters = "ship_fighters*$fighter_price";
  $calc_equip = "$calc_torps+$calc_armour_pts+$calc_ship_ore+$calc_ship_organics+$calc_ship_goods+$calc_ship_energy+$calc_ship_colonists+$calc_ship_fighters";

  $calc_dev_warpedit = "dev_warpedit*$dev_warpedit_price";
  $calc_dev_genesis = "dev_genesis*$dev_genesis_price";
  $calc_dev_beacon = "dev_beacon*$dev_beacon_price";
  $calc_dev_emerwarp = "dev_emerwarp*$dev_emerwarp_price";
  $calc_dev_escapepod = "IF(dev_escapepod='Y', $dev_escapepod_price, 0)";
  $calc_dev_fuelscoop = "IF(dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
  $calc_dev_minedeflector = "dev_minedeflector*$dev_minedeflector_price";
  $calc_dev = "$calc_dev_warpedit+$calc_dev_genesis+$calc_dev_beacon+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_fuelscoop+$calc_dev_minedeflector";

  $calc_planet_goods = "SUM(planets.organics)*$organics_price+SUM(planets.ore)*$ore_price+SUM(planets.goods)*$goods_price+SUM(planets.energy)*$energy_price";
  $calc_planet_colonists = "SUM(planets.colonists)*$colonist_price";
  $calc_planet_defence = "SUM(planets.fighters)*$fighter_price+IF(base='Y', $base_credits+SUM(planets.torps)*$torpedo_price, 0)";
  $calc_planet_credits = "SUM(planets.credits)";

  $res = mysql_query("SELECT ROUND(SQRT($calc_levels+$calc_equip+$calc_dev+ships.credits+$calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits)) AS score FROM ships LEFT JOIN planets ON planets.owner=ship_id WHERE ship_id=$sid AND ship_destroyed='N'");
  $row = mysql_fetch_array($res);
  $score = $row[score];
  mysql_query("UPDATE ships SET score=$score WHERE ship_id=$sid");

  return $score;
}

function db_kill_player($ship_id)
{
  global $default_prod_ore;
  global $default_prod_organics;
  global $default_prod_goods;
  global $default_prod_energy;
  global $default_prod_fighters;
  global $default_prod_torp;

  mysql_query("UPDATE ships SET ship_destroyed='Y',on_planet='N',sector=0 WHERE ship_id=$ship_id");
  mysql_query("UPDATE planets SET owner=0 WHERE owner=$ship_id");
  mysql_query("DELETE FROM sector_defence where ship_id=$ship_id");
}

function NUMBER($number, $decimals = 0)
{
  global $local_number_dec_point;
  global $local_number_thousands_sep;
  return number_format($number, $decimals, $local_number_dec_point, $local_number_thousands_sep);
}

function NUM_HOLDS($level_hull)
{
  global $level_factor;
  return round(pow($level_factor, $level_hull) * 100);
}

function NUM_ENERGY($level_power)
{
  global $level_factor;
  return round(pow($level_factor, $level_power) * 500);
}

function NUM_FIGHTERS($level_computer)
{
  global $level_factor;
  return round(pow($level_factor, $level_computer) * 100);
}

function NUM_TORPEDOES($level_torp_launchers)
{
  global $level_factor;
  return round(pow($level_factor, $level_torp_launchers) * 100);
}

function NUM_ARMOUR($level_armour)
{
  global $level_factor;
  return round(pow($level_factor, $level_armour) * 100);
}

function NUM_BEAMS($level_beams)
{
  global $level_factor;
  return round(pow($level_factor, $level_beams) * 100);
}

function NUM_SHIELDS($level_shields)
{
  global $level_factor;
  return round(pow($level_factor, $level_shields) * 100);
}

function SCAN_SUCCESS($level_scan, $level_cloak)
{
  return (5 + $level_scan - $level_cloak) * 5;
}

function SCAN_ERROR($level_scan, $level_cloak)
{
  global $scan_error_factor;

  $sc_error = (4 + $level_scan / 2 - $level_cloak / 2) * $scan_error_factor;

  if($sc_error<1)
  {
    $sc_error=1;
  }
  if($sc_error>99)
  {
    $sc_error=99;
  }

  return $sc_error;
}

function explode_mines($sector, $num_mines)
{
    $result3 = mysql_query ("SELECT * FROM sector_defence WHERE sector_id='$sector' and defence_type ='M' order by quantity ASC");
    echo mysql_error();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(($row = mysql_fetch_array($result3)) && $num_mines > 0)
       {
          if($row[quantity] > $num_mines)
          {
             $update = mysql_query("UPDATE sector_defence set quantity=quantity - $num_mines where defence_id = $row[defence_id]");
             $num_mines = 0;
          }
          else
          {
             $update = mysql_query("DELETE FROM sector_defence WHERE defence_id = $row[defence_id]");
             $num_mines -= $row[quantity];
          }
                 
       }
       mysql_free_result($result3);
    }

}

function destroy_fighters($sector, $num_fighters)
{
    $result3 = mysql_query ("SELECT * FROM sector_defence WHERE sector_id='$sector' and defence_type ='F' order by quantity ASC");
    echo mysql_error();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(($row = mysql_fetch_array($result3)) && $num_fighters > 0)
       {
          if($row[quantity] > $num_fighters)
          {
             $update = mysql_query("UPDATE sector_defence set quantity=quantity - $num_fighters where defence_id = $row[defence_id]");
             $num_fighters = 0;
          }
          else
          {
             $update = mysql_query("DELETE FROM sector_defence WHERE defence_id = $row[defence_id]");
             $num_fighters -= $row[quantity];
          }
                 
       }
       mysql_free_result($result3);
    }

}

function message_defence_owner($sector, $message)
{
    $result3 = mysql_query ("SELECT * FROM sector_defence WHERE sector_id='$sector' ");
    echo mysql_error();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while($row = mysql_fetch_array($result3))
       {

          playerlog($row[ship_id],$message);
                 
       }
       mysql_free_result($result3);
    }

}

function distribute_toll($sector, $toll, $total_fighters)
{
    $result3 = mysql_query ("SELECT * FROM sector_defence WHERE sector_id='$sector' AND defence_type ='F' "); 
    echo mysql_error();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while($row = mysql_fetch_array($result3))
       {
          $toll_amount = ROUND(($row['quantity'] / $total_fighters) * $toll);
          mysql_query("UPDATE ships set credits=credits + $toll_amount WHERE ship_id = $row[ship_id]");
          playerlog($row[ship_id],"You received $toll_amount credits as toll for entry to sector $sector.");
                 
       }
       mysql_free_result($result3);
    }

}

function calc_ownership($sector)
{
  $res = mysql_query("SELECT owner, corp FROM planets WHERE sector_id=$sector AND base='Y'");  
  $num_bases = mysql_num_rows($res);
  
  $i=0;
  if($num_bases > 0)
  {
    while($row = mysql_fetch_array($res))
    {
      $bases[$i] = $row;
      $i++;
    }
    mysql_free_result($res);
  }
  else
    return "Sector ownership didn't change";

  $owner_num = 0;
  
  foreach($bases as $curbase)
  {
    $curcorp=-1;
    $curship=-1;
    $loop = 0;
    while ($loop < $owner_num)
    {
      if($curbase[corp] != 0)
      {
        if($owners[$loop][type] == 'C')
        {
          if($owners[$loop][id] == $curbase[corp])
          {
            $curcorp=$loop;
            $owners[$loop][num]++;
          }
        }
      }
      
      if($owners[$loop][type] == 'S')
      {
        if($owners[$loop][id] == $curbase[owner])
        {
          $curship=$loop;
          $owners[$loop][num]++;
        }
      }
      
      $loop++;
    }
    
    if($curcorp == -1)
    {
      if($curbase[corp] != 0)
      {
         $curcorp=$owner_num;
         $owner_num++;
         $owners[$curcorp][type] = 'C';
         $owners[$curcorp][num] = 1;
         $owners[$curcorp][id] = $curbase[corp];
      }
    }    
    
    if($curship == -1)
    {
      $curship=$owner_num;
      $owner_num++;
      $owners[$curship][type] = 'S';
      $owners[$curship][num] = 1;
      $owners[$curship][id] = $curbase[owner];
    }
  }

  // We've got all the contenders with their bases.
  // Time to test for conflict
  
  /* This is debug code
  echo "Owners:<p>";
  foreach($owners as $owner)
  {
    echo "Type : $owner[type]<br>";
    echo "Bases : $owner[num]<br>";
    echo "Id : $owner[id]<br>";
  }
     End of debug code  */
  
  $loop=0;
  $nbcorps=0;
  $nbships=0;
  while($loop < $owner_num)
  {
    if($owners[$loop][type] == 'C')
      $nbcorps++;
    else
    {
      $res = mysql_query("SELECT team FROM ships WHERE ship_id=" . $owners[$loop][id]);
      if($res && mysql_num_rows($res) != 0)
      {
        $curship = mysql_fetch_array($res);
        $ships[$nbships]=$owners[$loop][id];
        $scorps[$nbships]=$curship[team];
        $nbships++;
      }
    }
    
    $loop++;
  }

  //More than one corp, war
  if($nbcorps > 1)
  {
    mysql_query("UPDATE universe SET zone_id=4 WHERE sector_id=$sector");
    return "Zone is now a War Zone!";
  }

  //More than one unallied ship, war
  $numunallied = 0;
  foreach($scorps as $corp)
  {
    if($corp == 0)
      $numunallied++;
  }
  if($numunallied > 1)
  {
    mysql_query("UPDATE universe SET zone_id=4 WHERE sector_id=$sector");
    return "Zone is now a War Zone!";
  }
  
  //Unallied ship, another corp present, war
  if($numunallied > 0 && $nbcorps > 0)
  {
    mysql_query("UPDATE universe SET zone_id=4 WHERE sector_id=$sector");
    return "Zone is now a War Zone!";
  }

  //Ok, all bases are allied at this point. Let's make a winner.
  $winner = 0;
  $i = 1;
  while ($i < $owner_num)
  {
    if($owners[$i][num] > $owners[$winner][num])
      $winner = $i;
    elseif($owners[$i][num] == $owners[$winner][num])
    {
      if($owners[$i][type] == 'C')
        $winner = $i;
    }
    $i++;
  }

  if($owners[$winner][type] == 'C')
  {
    $res = mysql_query("SELECT zone_id FROM zones WHERE corp_zone='Y' && owner=" . $owners[$winner][id]);
    $zone = mysql_fetch_array($res);
    
    $res = mysql_query("SELECT team_name FROM teams WHERE id=" . $owners[$winner][id]);
    $corp = mysql_fetch_array($res);

    mysql_query("UPDATE universe SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
    return "Zone now belongs to alliance $corp[team_name]!";
  }
  else
  {
    $onpar = 0;
    foreach($owners as $curowner)
    {
      if($curowner[type] == 'S' && $curowner[id] != $owners[$winner][id] && $curowner[num] == $owners[winners][num])
        $onpar = 1;
        break;
    }
    
    //Two allies have the same number of bases
    if($onpar == 1)
    {
      mysql_query("UPDATE universe SET zone_id=1 WHERE sector_id=$sector");
      return "Zone is a neutral zone.";
    }
    else
    {
      $res = mysql_query("SELECT zone_id FROM zones WHERE corp_zone='N' && owner=" . $owners[$winner][id]);
      $zone = mysql_fetch_array($res);
    
      $res = mysql_query("SELECT character_name FROM ships WHERE ship_id=" . $owners[$winner][id]);
      $ship = mysql_fetch_array($res);

      mysql_query("UPDATE universe SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
      return "Zone now belongs to player $ship[character_name]!";      
    }
  }
}


?>
