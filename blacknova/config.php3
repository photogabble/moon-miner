<?
include("config_local.php3");
include("global_funcs.php3");

/* Main scheduler variables (game flow control)
-----------------------------------------------*/

/* 
  Set this to how often (in minutes) you are running
  the scheduler script.
*/
$sched_ticks = 6;

/* All following vars are in minutes.
   These are TRUE minutes, no matter to what interval
   you're running the scheduler script! The scheduler
   will auto-adjust, possibly running many of the same
   events in a single call.
*/
$sched_turns = 2;    //New turns rate (also includes towing, furangee)
$sched_ports = 2;    //How often port production occurs
$sched_planets = 2;  //How often planet production occurs
$sched_IGB = 2;      //How often IGB interests are added
$sched_ranking = 30; //How often rankings will be generated
$sched_news = 15;    //How often news are generated
$sched_degrade = 6;  //How often sector fighters degrade when unsupported by a planet

/* Scheduler config end */

/* GUI colors (temporary until we have something nicer) */
$color_header = "#500050";
$color_line1 = "#300030";
$color_line2 = "#400040";

/* Localization (regional) settings */
$local_number_dec_point = ".";
$local_number_thousands_sep = ",";
$language = "english";

/* game variables */
$ip = getenv("REMOTE_ADDR");
$mine_hullsize = 8; //Minimum size hull has to be to hit mines and fighters
$ewd_maxhullsize = 15; //Max hull size before EWD degrades
$sector_max = 3000;
$link_max=10;
$universe_size = 200;
$game_name = "BlackNova Traders v0.2";
$fed_max_hull = 8;
$maxlen_password = 16;
$max_rank=100;
$rating_combat_factor=.8;    //ammount of rating gained from combat
$server_closed=false;        //true = block logins but not new account creation
$server_closed_message="Server closed until further notice.";
$account_creation_closed=false;    //true = block new account creation
$account_creation_closed_message="Game closed for tournament play";

/* newbie niceness variables */
$newbie_nice = "YES";
$newbie_extra_nice = "YES";
$newbie_hull = "8";
$newbie_engines = "8";
$newbie_power = "8";
$newbie_computer = "8";
$newbie_sensors = "8";
$newbie_armour = "8";
$newbie_shields = "8";
$newbie_beams = "8";
$newbie_torp_launchers = "8";
$newbie_cloak = "8";

/* specify which special features are allowed */
$allow_fullscan = true;                // full long range scan
$allow_navcomp = true;                 // navigation computer
$allow_ibank = true;                  // Intergalactic Bank (IGB)
$allow_genesis_destroy = true;         // Genesis torps can destroy planets

// iBank Config - Intergalactic Banking
// Trying to keep ibank constants unique by prefixing with $ibank_
// Please EDIT the following variables to your liking.

$ibank_interest = 0.0003;			// Interest rate for account funds NOTE: this is calculated every system update!
$ibank_paymentfee = 0.05; 		// Paymentfee
$ibank_loaninterest = 0.0006;		// Loan interest i.e 8%
$ibank_loanfactor = 10;			// x Times what the user currently have in account
$ibank_loanlimit = 250000000;		// This minus already existing loans is the maximum.

// Information displayed on the 'Manage Own Account' section
$ibank_ownaccount_info = "Interest rate is " . $ibank_interest * 100 . "%<BR>Loan rate is " .
$ibank_loaninterest * 100 . "%<P>If you have loans Make sure you have enough credits deposited each turn " .
  "to pay the interest and mortage, otherwise it will be deducted from your ships acccount at <FONT COLOR=RED>" .
  "twice the current Loan rate (" . $ibank_loaninterest * 100 * 2 .")%</FONT>.";

// end of iBank config

// default planet production percentages
$default_prod_ore      = 20.0;
$default_prod_organics = 20.0;
$default_prod_goods    = 20.0;
$default_prod_energy   = 20.0;
$default_prod_fighters = 10.0;
$default_prod_torp     = 10.0;

/* port pricing variables */
$ore_price = 11;
$ore_delta = 5;
$ore_rate = 75000;
$ore_prate = 0.25;
$ore_limit = 100000000;

$organics_price = 5;
$organics_delta = 2;
$organics_rate = 5000;
$organics_prate = 0.5;
$organics_limit = 100000000;

$goods_price = 15;
$goods_delta = 7;
$goods_rate = 75000;
$goods_prate = 0.25;
$goods_limit = 100000000;

$energy_price = 3;
$energy_delta = 1;
$energy_rate = 75000;
$energy_prate = 0.1;
$energy_limit = 100000000;

$inventory_factor = 1;
$upgrade_cost = 1000;
$upgrade_factor = 2;
$level_factor = 1.5;

$dev_genesis_price = 1000000;
$dev_beacon_price = 100;
$dev_emerwarp_price = 1000000;
$dev_warpedit_price = 100000;
$dev_minedeflector_price = 10;
$dev_escapepod_price = 100000;
$dev_fuelscoop_price = 100000;

$fighter_price = 50;
$fighter_prate = .01;

$torpedo_price = 25;
$torpedo_prate = .01;
$torp_dmg_rate = 10;

$credits_prate = 3.0;

$armour_price = 5;
$basedefense = 1;  // Additional factor added to tech levels by having a base on your planet. All your base are belong to us.

$colonist_price = 5;
$colonist_production_rate = .005;
$colonist_reproduction_rate = 0.0005;
$colonist_limit = 100000000;

$interest_rate = 1.0005;

$base_ore = 10000;
$base_goods = 10000;
$base_organics = 10000;
$base_credits = 10000000;
$base_modifier = 1;

$start_fighters = 10;
$start_armour = 10;
$start_credits = 1000;
$start_energy = 100;
$start_turns = 1200;

$max_turns = 2500;
$max_emerwarp = 10;

$fullscan_cost = 1;
$scan_error_factor=20;

$max_planets_sector = 5;
$max_traderoutes_player = 40;

$min_bases_to_own = 3;

$default_lang = '/languages/english.inc';

$avail_lang[0][file] = '/languages/english.inc';
$avail_lang[0][name] = 'English';
$avail_lang[1][file] = '/languages/german.inc';
$avail_lang[1][name] = 'Deutsch';
$avail_lang[2][file] = '/languages/french.inc';
$avail_lang[2][name] = 'Français';

// Anti-cheat settings
$IGB_min_turns = $start_turns; //Turns a player has to play before ship transfers are allowed 0=disable
$IGB_svalue = 0.15; //Max amount of sender's value allowed for ship transfers 0=disable
$IGB_trate = 1440; //Time (in minutes) before two similar transfers are allowed for ship transfers.0=disable
$corp_planet_transfers = 0; //If transferring credits to/from corp planets is allowed. 1=enable
$min_value_capture = 50; //Percantage of planet's value a ship must be worth to be able to capture it. 0=disable
$defence_degrade_rate = 0.05;
$energy_per_fighter = 0.10;
?>
