<?

function bigtitle()
{
  global $title;
  echo "<H1>$title</H1>";
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
      echo "Your ship was destroyed, but your escape pods saved you and your crew.  Click <A HREF=main.php3>here</A> to continue with a new ship.";
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
  /* refresh the cookie with username, password and id - times out after 60 minutes, and player must log-in again. */
  global $username;
  global $password;
  global $id;
  setcookie("username", $username);
  setcookie("password", $password);
  setcookie("id", $id);
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
  
  /* Generate a score for a player - store it in the database - and return it as text as well. */
  $result1 = mysql_query("SELECT * FROM ships WHERE ship_id=$sid");
  $playerinfo = mysql_fetch_array($result1);
  $score = round($playerinfo[credits]+$upgrade_cost*(pow($playerinfo[hull],$upgrade_factor)+pow($playerinfo[engines],$upgrade_factor)+pow($playerinfo[power],$upgrade_factor)+pow($playerinfo[computers],$upgrade_factor)+pow($playerinfo[sensors],$upgrade_factor)+pow($playerinfo[beams],$upgrade_factor)+pow($playerinfo[torp_launchers],$upgrade_factor)+pow($playerinfo[armour],$upgrade_factor)+pow($playerinfo[cloak],$upgrade_factor)+pow($playerinfo[shields],$upgrade_factor))+$playerinfo[ship_fighters]*$fighter_price+$playerinfo[torps]*$torpedo_price+$playerinfo[armour_pts]*$armour_price/1000+$playerinfo[dev_minedeflectors]*dev_minedefelctor_price+$playerinfo[dev_warpedit]*$dev_warpedit_price+$playerinfo[dev_beacon]*$dev_beacon_price+$playerinfo[dev_genesis]*$dev_genesis_price+$playerinfo[dev_emerwarp]*$dev_emerwarp_price+$playerinfo[ship_ore]*$ore_price+$playerinfo[ship_organics]*$organics_price+$playerinfo[ship_goods]*$goods_price+$playerinfo[ship_energy]*$energy_price)/1000;
  if($playerinfo[dev_escapepod] == "Y")
  {
    $score = $score + $dev_escapepod_price / 1000;
  }
  if($playerinfo[dev_fuelscoop] == "Y")
  {
    $score = $score + $dev_fuelscoop_price / 1000;
  }
  $result2 = mysql_query("SELECT * from universe WHERE planet_owner=$sid");
  while($planet = mysql_fetch_array($result2))
  {
    $score = $score + round(($planet[planet_organics]*$organics_price+$planet[planet_ore]*$ore_price+$planet[planet_goods]*$goods_price+$planet[planet_energy]*$energy_price+$planet[planet_credits]+$planet[planet_colonists]*$colonist_price+$planet[planet_fighters]*$fighter_price+$planet[base_torp]*torpedo_price)/1000);
    if($planet[base] == "Y")
    {
      $score = $score + round(($base_ore*$ore_price+$base_goods*$goods_price+$base_goods*$goods_price+$base_credits)/1000);
    }
  }
  $update = mysql_query("UPDATE ships SET score=$score WHERE ship_id=$sid");
  mysql_free_result($result1);
  mysql_free_result($result2);
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

?>
