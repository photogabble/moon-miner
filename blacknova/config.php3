<?

include("config_local.php3");

/* all my variables */

/* GUI colors (temporary until we have something nicer) */
$color_header = "SILVER";
$color_line1 = "WHITE";
$color_line2 = "LIGHTGREY";

/* game variables */
$ip = getenv("REMOTE_ADDR");
$sector_max = 3000;
$universe_size = 200;
$game_name = "BlackNova Traders v0.1.10 - with stronger and faster bugs!";

$fed_max_hull = 8;

// iBank Config - Intergalactic Banking
// Trying to keep ibank constants unique by prefixing with $ibank_ 
// Please EDIT the following variables to your liking.
$allow_ibank = false;
$ibank_owner = 0; 				    // Use 0 for no human player or ID of Owner of IGB some thrusted player or admin.
$ibank_interest = 0.01; 		  // Interest rate for account funds
$ibank_paymentfee = 0.005; 		// Paymentfee
$ibank_loaninterest = 0.008; 	// Loan interest i.e 8%
$ibank_loanfactor = 10; 		  // x Times what the user currently have in account
$ibank_loanlimit = 250000000; // This minus already existing loans is the maximum. 
// Information displayed on the 'Manage Own Account' section
$ibank_ownaccount_info = "Interest rate is " . $ibank_interest * 100 . "%<BR>Loan rate is " .
  $ibank_loaninterest * 100 . "%<P>If you have loans Make sure you have enough credits deposited each turn " .
  "to pay the interest and mortage, otherwise it will be deducted from your ships acccount at <FONT COLOR=RED>" .
  "twice the current Loan rate (" . $ibank_loaninterest * 100 * 2 .")%</FONT>.";
// end of iBank config

/* port pricing variables */
$ore_price = 11;
$ore_delta = 5;
$ore_rate= 5000;
$ore_prate=0.05;
$ore_limit = 100000000;
$organics_price = 5;
$organics_delta = 2;
$organics_rate= 5000;
$organics_prate= 0.1;
$organics_limit = 100000000;
$goods_price = 15;
$goods_delta = 7;
$goods_rate= 5000;
$goods_prate= 0.05;
$goods_limit = 100000000;
$energy_price = 3;
$energy_delta = 1;
$energy_rate= 5000;
$energy_prate= 0.2;
$energy_limit = 100000000;
$inventory_factor = 1;
$upgrade_cost=1000;
$upgrade_factor=2;
$level_factor=1.5;
$dev_genesis_price=1000;
$dev_beacon_price=100;
$dev_emerwarp_price=10000;
$dev_warpedit_price=100000;
$dev_minedeflector_price=10;
$dev_escapepod_price=100000;
$dev_fuelscoop_price=100000;
$fighter_price=50;
$fighter_prate=.001;
$torpedo_price=25;
$torpedo_prate=.001;
$armour_price=5;
$colonist_price=5;
$colonist_production_rate=.005;
$colonist_reproduction_rate=0.0005;
$colonist_limit = 100000000;
$interest_rate=1.0005;
$base_ore = 10000;
$base_goods = 10000;
$base_organics = 10000;
$base_credits = 10000000;
$base_modifier = 1;
$start_fighters=10;
$start_armour=10;
$start_credits=1000;
$start_energy=100;
$start_turns=200;
$max_turns=600;

$allow_fullscan = true;
$fullscan_cost = 1;

/* functions */
function bigtitle()
{
  global $title;
  echo "<H1>$title</H1>";
}
function checklogin()
{
  
  $flag=0;
  global $username;
  global $password;
  $result1 = mysql_query ("SELECT * FROM ships WHERE email='$username'");
  $playerinfo=mysql_fetch_array($result1);
  /* Check the cookie to see if username/password are empty - check password against database */
  if ($username=="" or  $password=="" or $password!=$playerinfo[password])
  {
    echo  "You need to log in, click <a href=login.php3>here</a>.";
    $flag=1;
  }
  /* Check for destroyed ship */
  if ($playerinfo[ship_destroyed]=="Y")
  {
    /* if the player has an escapepod, set the player up with a new ship */
    if ($playerinfo[dev_escapepod]="Y")
    {
      $result2 = mysql_query ("UPDATE ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage='', on_planet='N', dev_warpedit=0, dev_genesis=1, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N' where email='$username'");
      echo "Your ship was destroyed, but your escape pods saved you and your crew.  Click <a href=main.php3>here</a> to continue with a new ship.";
      $flag=1;
    }else{
      /* if the player doesn't have an escapepod - they're dead, delete them. */  
      echo "Player is DEAD!  Here's what happened:<BR><BR>";
      
      include("player-log/".$playerinfo[ship_id]);
      unlink("player-log/".$playerinfo[ship_id]);
      $result3 = mysql_query ("DELETE FROM ships WHERE ship_id = $playerinfo[ship_id]");
      echo "Dead player has now been deleted.  Click <a href=new.php3>here</a> to start with a new player.";
      $flag=1;
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
  mysql_connect($dbhost .":" .$dbport, $dbuname, $dbpass);
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
  $plog=fopen("player-log/".$sid,"a");
  fwrite($plog,"$log_entry <BR><BR>");
  fclose($plog);
}
?>
