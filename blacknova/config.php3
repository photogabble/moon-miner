<?

include("config_local.php3");
include("global_funcs.php3");

/* GUI colors (temporary until we have something nicer) */
$color_header = "#500050";
$color_line1 = "#300030";
$color_line2 = "#400040";

/* Localization (regional) settings */
$local_number_dec_point = ".";
$local_number_thousands_sep = ",";

/* game variables */
$ip = getenv("REMOTE_ADDR");
$sector_max = 3000;
$universe_size = 200;
$game_name = "BlackNova Traders v0.1.12 - Where the bugs just keep on coming!";
$fed_max_hull = 8;
$maxlen_password = 16;
$max_rank=100;
$rating_combat_factor=.2;    //ammount of rating gained from combat
$server_closed=false;        //true = block logins but not new account creation
$server_closed_message="Server closed until further notice.";

/* specify which special features are allowed */
$allow_fullscan = true;                // full long range scan
$allow_navcomp = true;                 // navigation computer
$allow_ibank = false;                  // Intergalactic Bank (IGB)
$allow_genesis_destroy = true;         // Genesis torps can destroy planets

// iBank Config - Intergalactic Banking
// Trying to keep ibank constants unique by prefixing with $ibank_
// Please EDIT the following variables to your liking.

$ibank_owner = 0;			// Use 0 for no human player or ID of Owner of IGB some trusted player or admin.
$ibank_interest = 0.01;			// Interest rate for account funds
$ibank_paymentfee = 0.005; 		// Paymentfee
$ibank_loaninterest = 0.008;		// Loan interest i.e 8%
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
$ore_rate = 5000;
$ore_prate = 0.25;
$ore_limit = 100000000;

$organics_price = 5;
$organics_delta = 2;
$organics_rate = 5000;
$organics_prate = 0.5;
$organics_limit = 100000000;

$goods_price = 15;
$goods_delta = 7;
$goods_rate = 5000;
$goods_prate = 0.25;
$goods_limit = 100000000;

$energy_price = 3;
$energy_delta = 1;
$energy_rate = 5000;
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
$start_turns = 200;

$max_turns = 600;
$max_emerwarp = 10;

$fullscan_cost = 1;
$scan_error_factor=20;

?>
