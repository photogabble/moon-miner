<?

function bigtitle()
{
  global $title;
  echo "<H1>$title</H1>";
}

function TEXT_GOTOMAIN()
{
  global $interface;
  echo "Click <A HREF=$interface>here</A> to return to the main menu.";
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
      $result2 = mysql_query("UPDATE ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage='', on_planet='N', dev_warpedit=0, dev_genesis=1, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N' where email='$username'");
      echo "Your ship was destroyed, but your escape pods saved you and your crew.  Click <A HREF=$interface>here</A> to continue with a new ship.";
      $flag = 1;
    }
    else
    {
      /* if the player doesn't have an escapepod - they're dead, delete them. */  
      echo "Player is DEAD!  Here's what happened:<BR><BR>";
      
      include("player-log/" . $playerinfo['ship_id']);
      unlink("player-log/" . $playerinfo['ship_id']);
      $result3 = mysql_query("DELETE FROM ships WHERE ship_id = " . $playerinfo['ship_id']);
      echo "Dead player has now been deleted.  Click <A HREF=new.php3>here</A> to start with a new player.";
      $flag = 1;
    }
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
  global $username;
  global $password;
  global $id;
  global $res;

  setcookie("username", $username);
  setcookie("password", $password);
  setcookie("id", $id);
  setcookie("res", $res);
}


function playerlog($sid,$log_entry)
{
  /* write log_entry to the player's log - identified by player's ship_id - sid. */
  $log_entry = date("l dS of F Y h:i:s A") . ":  " . $log_entry;
  $plog = fopen("player-log/" . $sid, "a");
  fwrite($plog, "$log_entry <BR><BR>");
  fclose($plog);
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
  
  $calc_hull = "ROUND(POW($upgrade_factor,hull-2))";
  $calc_engines = "ROUND(POW($upgrade_factor,engines-2))";
  $calc_power = "ROUND(POW($upgrade_factor,power-2))";
  $calc_computer = "ROUND(POW($upgrade_factor,computer-2))";
  $calc_sensors = "ROUND(POW($upgrade_factor,sensors-2))";
  $calc_beams = "ROUND(POW($upgrade_factor,beams-2))";
  $calc_torp_launchers = "ROUND(POW($upgrade_factor,torp_launchers-2))";
  $calc_shields = "ROUND(POW($upgrade_factor,shields-2))";
  $calc_armour = "ROUND(POW($upgrade_factor,armour-2))";
  $calc_cloak = "ROUND(POW($upgrade_factor,cloak-2))";
  $calc_levels = "($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak)*$upgrade_cost";

  $calc_torps = "torps*$torpedo_price";
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

  $calc_planet_goods = "SUM(planet_organics)*$organics_price+SUM(planet_ore)*$ore_price+SUM(planet_goods)*$goods_price+SUM(planet_energy)*$energy_price";
  $calc_planet_colonists = "SUM(planet_colonists)*$colonist_price";
  $calc_planet_defence = "SUM(planet_fighters)*$fighter_price+IF(base='Y', $base_credits+SUM(base_torp)*$torpedo_price, 0)";
  $calc_planet_credits = "SUM(planet_credits)";

  $res = mysql_query("SELECT ROUND(SQRT($calc_levels+$calc_equip+$calc_dev+credits+$calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits)) AS score FROM ships LEFT JOIN universe ON planet_owner=ship_id WHERE ship_id=$sid AND ship_destroyed='N'");
  $row = mysql_fetch_array($res);
  $score = $row[score];
  mysql_query("UPDATE ships SET score=$score WHERE ship_id=$sid");

  return $score;
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

function SCAN_SUCCESS($level_scan, $level_cloak)
{
  return (5 + $level_scan - $level_cloak) * 5;
}

?>
