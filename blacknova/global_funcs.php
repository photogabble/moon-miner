<?
// Separate userpass into username & password to support the legacy of multiple cookies for login.
if ($userpass != '' and $userpass != '+') {
  $username = substr($userpass, 0, strpos($userpass, "+"));
  $password = substr($userpass, strpos($userpass, "+")+1);

}

// Ensure lang is set
if (!isset($lang) || empty($lang))
  $lang = $default_lang;

//Log constants

define(LOG_LOGIN, 1);
define(LOG_LOGOUT, 2);
define(LOG_ATTACK_OUTMAN, 3);           //sent to target when better engines
define(LOG_ATTACK_OUTSCAN, 4);          //sent to target when better cloak
define(LOG_ATTACK_EWD, 5);              //sent to target when EWD engaged
define(LOG_ATTACK_EWDFAIL, 6);          //sent to target when EWD failed
define(LOG_ATTACK_LOSE, 7);             //sent to target when he lost
define(LOG_ATTACKED_WIN, 8);            //sent to target when he won
define(LOG_TOLL_PAID, 9);               //sent when paid a toll
define(LOG_HIT_MINES, 10);              //sent when hit mines
define(LOG_SHIP_DESTROYED_MINES, 11);   //sent when destroyed by mines
define(LOG_PLANET_DEFEATED_D, 12);      //sent when one of your defeated planets is destroyed instead of captured
define(LOG_PLANET_DEFEATED, 13);        //sent when a planet is defeated
define(LOG_PLANET_NOT_DEFEATED, 14);    //sent when a planet survives
define(LOG_RAW, 15);                    //this log is sent as-is
define(LOG_TOLL_RECV, 16);              //sent when you receive toll money
define(LOG_DEFS_DESTROYED, 17);         //sent for destroyed sector defenses
define(LOG_PLANET_EJECT, 18);           //sent when ejected from a planet due to alliance switch
define(LOG_BADLOGIN, 19);               //sent when bad login
define(LOG_PLANET_SCAN, 20);            //sent when a planet has been scanned
define(LOG_PLANET_SCAN_FAIL, 21);       //sent when a planet scan failed
define(LOG_PLANET_CAPTURE, 22);         //sent when a planet is captured
define(LOG_SHIP_SCAN, 23);              //sent when a ship is scanned
define(LOG_SHIP_SCAN_FAIL, 24);         //sent when a ship scan fails
define(LOG_FURANGEE_ATTACK, 25);        //furangees send this to themselves
define(LOG_STARVATION, 26);             //sent when colonists are starving... Is this actually used in the game?
define(LOG_TOW, 27);                    //sent when a player is towed
define(LOG_DEFS_DESTROYED_F, 28);       //sent when a player destroys fighters
define(LOG_DEFS_KABOOM, 29);            //sent when sector fighters destroy you
define(LOG_HARAKIRI, 30);               //sent when self-destructed
define(LOG_TEAM_REJECT, 31);            //sent when player refuses invitation
define(LOG_TEAM_RENAME, 32);            //sent when renaming a team
define(LOG_TEAM_M_RENAME, 33);          //sent to members on team rename
define(LOG_TEAM_KICK, 34);              //sent to booted player
define(LOG_TEAM_CREATE, 35);            //sent when created a team
define(LOG_TEAM_LEAVE, 36);             //sent when leaving a team
define(LOG_TEAM_NEWLEAD, 37);           //sent when leaving a team, appointing a new leader
define(LOG_TEAM_LEAD, 38);              //sent to the new team leader
define(LOG_TEAM_JOIN, 39);              //sent when joining a team
define(LOG_TEAM_NEWMEMBER, 40);         //sent to leader on join
define(LOG_TEAM_INVITE, 41);            //sent to invited player
define(LOG_TEAM_NOT_LEAVE, 42);         //sent to leader on leave
define(LOG_ADMIN_HARAKIRI, 43);         //sent to admin on self-destruct
define(LOG_ADMIN_PLANETDEL, 44);        //sent to admin on planet destruction instead of capture
define(LOG_DEFENCE_DEGRADE, 45);        //sent sector fighters have no supporting planet
define(LOG_PLANET_CAPTURED, 46);            //sent to player when he captures a planet

// Database tables variables
$dbtables['ibank_accounts'] = "${db_prefix}ibank_accounts";
$dbtables['links'] = "${db_prefix}links";
$dbtables['planets'] = "${db_prefix}planets";
$dbtables['traderoutes'] = "${db_prefix}traderoutes";
$dbtables['news'] = "${db_prefix}news";
$dbtables['ships'] = "${db_prefix}ships";
$dbtables['teams'] = "${db_prefix}teams";
$dbtables['universe'] = "${db_prefix}universe";
$dbtables['zones'] = "${db_prefix}zones";
$dbtables['messages'] = "${db_prefix}messages";
$dbtables['furangee'] = "${db_prefix}furangee";
$dbtables['sector_defence'] = "${db_prefix}sector_defence";
$dbtables['scheduler'] = "${db_prefix}scheduler";
$dbtables['ip_bans'] = "${db_prefix}ip_bans";
$dbtables['IGB_transfers'] = "${db_prefix}IGB_transfers";
$dbtables['logs'] = "${db_prefix}logs";
$dbtables['gen_id'] = "${db_prefix}gen_id";

function bigtitle()
{
  global $title;
  echo "<H1>$title</H1>\n";
}

function TEXT_GOTOMAIN()
{
  global $l_global_mmenu;
  echo $l_global_mmenu;
}

function TEXT_GOTOLOGIN()
{
global $l_global_mlogin;
  echo $l_global_mlogin;
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

  global $username, $l_global_needlogin, $l_global_died;
  global $password, $l_login_died, $l_die_please;
  global $db, $dbtables;

  $result1 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
  $playerinfo = $result1->fields;

  /* Check the cookie to see if username/password are empty - check password against database */
  if($username == "" or $password == "" or $password != $playerinfo['password'])
  {
    echo $l_global_needlogin;
    $flag = 1;
  }

  /* Check for destroyed ship */
  if($playerinfo['ship_destroyed'] == "Y")
  {
    /* if the player has an escapepod, set the player up with a new ship */
    if($playerinfo['dev_escapepod'] == "Y")
    {
      $result2 = $db->Execute("UPDATE $dbtables[ships] SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage='', on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N' where email='$username'");
      echo $l_login_died;
      $flag = 1;
    }
    else
    {
      /* if the player doesn't have an escapepod - they're dead, delete them. */
      /* uhhh  don't delete them to prevent self-distruct inherit*/
      echo $l_global_died;

      echo $l_die_please;
      $flag = 1;
    }
  }
  global $server_closed;
  global $l_login_closed_message;
  if($server_closed && $flag==0)
  {
    echo $l_login_closed_message;
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
  global $default_lang;
  global $lang;
  global $gameroot;
  global $db_type;
  global $db_persistent;
  global $db;
  global $ADODB_FETCH_MODE;

  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

  if(!empty($dbport))
    $dbhost.= ":$dbport";

  $db = ADONewConnection("$db_type");
  if($db_persistent == 1)
    $result = $db->PConnect("$dbhost", "$dbuname", "$dbpass", "$dbname");
  else
    $result = $db->Connect("$dbhost", "$dbuname", "$dbpass", "$dbname");

  if(!$result)
    die ("Unable to connect to the database");
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


function playerlog($sid, $log_type, $data = "")
{
  global $db, $dbtables;
  /* write log_entry to the player's log - identified by player's ship_id - sid. */
  if ($sid != "" && !empty($log_type))
  {
    $db->Execute("INSERT INTO $dbtables[logs] VALUES('', $sid, $log_type, NOW(), '$data')");
  }
}

function adminlog($log_type, $data = "")
{
  global $db, $dbtables;
  /* write log_entry to the admin log  */
  if (!empty($log_type))
  {
    $db->Execute("INSERT INTO $dbtables[logs] VALUES('', 0, $log_type, NOW(), '$data')");
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
  global $db, $dbtables;

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

  $calc_torps = "$dbtables[ships].torps*$torpedo_price";
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

  $calc_planet_goods = "SUM($dbtables[planets].organics)*$organics_price+SUM($dbtables[planets].ore)*$ore_price+SUM($dbtables[planets].goods)*$goods_price+SUM($dbtables[planets].energy)*$energy_price";
  $calc_planet_colonists = "SUM($dbtables[planets].colonists)*$colonist_price";
  $calc_planet_defence = "SUM($dbtables[planets].fighters)*$fighter_price+IF($dbtables[planets].base='Y', $base_credits+SUM($dbtables[planets].torps)*$torpedo_price, 0)";
  $calc_planet_credits = "SUM($dbtables[planets].credits)";

  $res = $db->Execute("SELECT ROUND(SQRT($calc_levels+$calc_equip+$calc_dev+$dbtables[ships].credits+$calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits)) AS score FROM $dbtables[ships] LEFT JOIN $dbtables[planets] ON $dbtables[planets].owner=ship_id WHERE ship_id=$sid AND ship_destroyed='N'");
  $row = $res->fields;
  $score = $row[score];
  $db->Execute("UPDATE $dbtables[ships] SET score=$score WHERE ship_id=$sid");

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
  global $gameroot;

  include($gameroot . "/languages/$default_lang");

  $db->Execute("UPDATE $dbtables[ships] SET ship_destroyed='Y',on_planet='N',sector=0,cleared_defences=' ' WHERE ship_id=$ship_id");

  $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner='$ship_id' AND base='Y'");
  $i=0;

  while(!$res->EOF)
  {
    $sectors[$i] = $res->fields[sector_id];
    $i++;
    $res->MoveNext();
  }

  $db->Execute("UPDATE $dbtables[planets] SET owner=0, base='N' WHERE owner=$ship_id");

  if(!empty($sectors))
  {
    foreach($sectors as $sector)
    {
      calc_ownership($sector);
    }
  }
  $db->Execute("DELETE FROM $dbtables[sector_defence] where ship_id=$ship_id");

  $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='N' AND owner=$ship_id");
  $zone = $res->fields;

$db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE zone_id=$zone[zone_id]");



$query = $db->Execute("select character_name from $dbtables[ships] where ship_id='$ship_id'");
$name = $query->fields;


$headline = $name[character_name] . $l_killheadline;


$newstext=str_replace("[name]",$name[character_name],$l_news_killed);

$news = $db->Execute("INSERT INTO $dbtables[news] (headline, newstext, user_id, date, news_type) VALUES ('$headline','$newstext','$ship_id',NOW(), 'killed')");

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
    global $db, $dbtables;

    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='M' order by quantity ASC");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(!$result3->EOF && $num_mines > 0)
       {
          $row = $result3->fields;
          if($row[quantity] > $num_mines)
          {
             $update = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity - $num_mines where defence_id = $row[defence_id]");
             $num_mines = 0;
          }
          else
          {
             $update = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
             $num_mines -= $row[quantity];
          }
          $result3->MoveNext();
       }
    }

}

function destroy_fighters($sector, $num_fighters)
{
    global $db, $dbtables;

    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='F' order by quantity ASC");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(!$result3->EOF && $num_fighters > 0)
       {
          $row=$result3->fields;
          if($row[quantity] > $num_fighters)
          {
             $update = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity - $num_fighters where defence_id = $row[defence_id]");
             $num_fighters = 0;
          }
          else
          {
             $update = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
             $num_fighters -= $row[quantity];
          }
          $result3->MoveNext();
       }
    }

}

function message_defence_owner($sector, $message)
{
    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' ");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(!$result3->EOF)
       {

          playerlog($result3->fields[ship_id],LOG_RAW, $message);

       }
    }
}

function distribute_toll($sector, $toll, $total_fighters)
{
    global $db, $dbtables;

    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' AND defence_type ='F' ");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(!$result3->EOF)
       {
          $row = $result3->fields;
          $toll_amount = ROUND(($row['quantity'] / $total_fighters) * $toll);
          $db->Execute("UPDATE $dbtables[ships] set credits=credits + $toll_amount WHERE ship_id = $row[ship_id]");
          playerlog($row[ship_id], LOG_TOLL_RECV, "$toll_amount|$sector");
          $result3->MoveNext();
       }
    }

}

function defence_vs_defence($ship_id)
{
   global $db, $dbtables;

   $result1 = $db->Execute("SELECT * from $dbtables[sector_defence] where ship_id = $ship_id");
   if($result1 > 0)
   {
      while(!$result1->EOF)
      {
         $row=$result1->fields;
         $deftype = $row[defence_type] == 'F' ? 'Fighters' : 'Mines';
         $qty = $row['quantity'];
         $result2 = $db->Execute("SELECT * from $dbtables[sector_defence] where sector_id = $row[sector_id] and ship_id <> $ship_id ORDER BY quantity DESC");
         if($result2 > 0)
         {
            while(!$result2->EOF && $qty > 0)
            {
               $cur = $result2->fields;
               $targetdeftype = $cur[defence_type] == 'F' ? $l_fighters : $l_mines;
               if($qty > $cur['quantity'])
               {
                  $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $cur[defence_id]");
                  $qty -= $cur['quantity'];
                  $db->Execute("UPDATE $dbtables[sector_defence] SET quantity = $qty where defence_id = $row[defence_id]");
                  playerlog($cur[ship_id], LOG_DEFS_DESTROYED, "$cur[quantity]|$targetdeftype|$row[sector_id]");
                  playerlog($row[ship_id], LOG_DEFS_DESTROYED, "$cur[quantity]|$deftype|$row[sector_id]");
               }
               else
               {
                  $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
                  $db->Execute("UPDATE $dbtables[sector_defence] SET quantity=quantity - $qty WHERE defence_id = $cur[defence_id]");
                  playerlog($cur[ship_id], LOG_DEFS_DESTROYED, "$qty|$targetdeftype|$row[sector_id]");
                  playerlog($row[ship_id], LOG_DEFS_DESTROYED, "$qty|$deftype|$row[sector_id]");
                  $qty = 0;
               }
               $result2->MoveNext();
            }
         }
         $result1->MoveNext();
      }
      $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE quantity <= 0");
   }
}

function kick_off_planet($ship_id,$whichteam)
{
   global $db, $dbtables;

   $result1 = $db->Execute("SELECT * from $dbtables[planets] where owner = '$ship_id' ");

   if($result1 > 0)
   {
      while(!$result1->EOF)
      {
         $row = $result1->fields;
         $result2 = $db->Execute("SELECT * from $dbtables[ships] where on_planet = 'Y' and planet_id = '$row[planet_id]' and ship_id <> '$ship_id' ");
         if($result2 > 0)
         {
            while(!$result2->EOF )
            {
               $cur = $result2->fields;
               $db->Execute("UPDATE $dbtables[ships] SET on_planet = 'N',planet_id = '0' WHERE ship_id='$cur[ship_id]'");
               playerlog($cur[ship_id], LOG_PLANET_EJECT, "$cur[sector]|$row[character_name]");
               $result2->MoveNext();
            }
         }
         $result1->MoveNext();
      }
   }
}


function calc_ownership($sector)
{
  global $min_bases_to_own, $l_global_warzone, $l_global_nzone, $l_global_team, $l_global_player;
  global $db, $dbtables;

  $res = $db->Execute("SELECT owner, corp FROM $dbtables[planets] WHERE sector_id=$sector AND base='Y'");
  $num_bases = $res->RecordCount();

  $i=0;
  if($num_bases > 0)
  {

   while(!$res->EOF)
    {
      $bases[$i] = $res->fields;
      $i++;
      $res->MoveNext();
    }
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
      if($curbase[owner] != 0)
      {
        $curship=$owner_num;
        $owner_num++;
        $owners[$curship][type] = 'S';
        $owners[$curship][num] = 1;
        $owners[$curship][id] = $curbase[owner];
      }
    }
  }

  // We've got all the contenders with their bases.
  // Time to test for conflict

  $loop=0;
  $nbcorps=0;
  $nbships=0;
  while($loop < $owner_num)
  {
    if($owners[$loop][type] == 'C')
      $nbcorps++;
    else
    {
      $res = $db->Execute("SELECT team FROM $dbtables[ships] WHERE ship_id=" . $owners[$loop][id]);
      if($res && $res->RecordCount() != 0)
      {
        $curship = $res->fields;
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
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
    return $l_global_warzone;
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
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
    return $l_global_warzone;
  }

  //Unallied ship, another corp present, war
  if($numunallied > 0 && $nbcorps > 0)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
    return $l_global_warzone;
  }

  //Unallied ship, another ship in a corp, war
  if($numunallied > 0)
  {
    $query = "SELECT team FROM $dbtables[ships] WHERE (";
    $i=0;
    foreach($ships as $ship)
    {
      $query = $query . "ship_id=$ship";
      $i++;
      if($i!=$nbships)
        $query = $query . " OR ";
      else
        $query = $query . ")";
    }
    $query = $query . " AND team!=0";
    $res = $db->Execute($query);
    if($res->RecordCount() != 0)
    {
      $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
      return $l_global_warzone;
    }
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

  if($owners[$winner][num] < $min_bases_to_own)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
    return $l_global_nzone;
  }


  if($owners[$winner][type] == 'C')
  {
    $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='Y' && owner=" . $owners[$winner][id]);
    $zone = $res->fields;

    $res = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=" . $owners[$winner][id]);
    $corp = $res->fields;

    $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
    return "$l_global_team $corp[team_name]!";
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
      $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
      return $l_global_nzone;
    }
    else
    {
      $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='N' && owner=" . $owners[$winner][id]);
      $zone = $res->fields;

      $res = $db->Execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id=" . $owners[$winner][id]);
      $ship = $res->fields;

      $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
      return "$l_global_player $ship[character_name]!";
    }
  }
}


function t_port($ptype) {

global $l_ore, $l_none, $l_energy, $l_organics, $l_goods, $l_special;

switch ($ptype) {
    case "ore":
        $ret=$l_ore;
        break;
    case "none":
        $ret=$l_none;
        break;
    case "energy":
        $ret=$l_energy;
        break;
    case "organics":
        $ret=$l_organics;
        break;
    case "goods":
        $ret=$l_goods;
        break;
    case "special":
        $ret=$l_special;
        break;


}

return $ret;
}

function GenNextID($id)
{
  global $db, $dbtables;

  $db->Execute("LOCK TABLES $dbtables[gen_id] write");
  $res = $db->Execute("SELECT count FROM $dbtables[gen_id] WHERE name='$id'");
  if(!$res)
    return(-1);
  $count = $res->fields[count];
  $res = $db->Execute("UPDATE $dbtables[gen_id] SET count=count+1 WHERE name='$id'");
  $count++;
  $db->Execute("UNLOCK TABLES");
  return($count);
}


?>
