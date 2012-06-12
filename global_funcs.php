<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: global_funcs.php

if (preg_match("/global_funcs.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function mypw($one,$two)
{
   return pow($one*1,$two*1);
}

function bigtitle()
{
  global $title;
  echo "<h1>$title</h1>\n";
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
  echo "\n<script type=\"text/javascript\">\n";
  echo "<!--\n";
}

function TEXT_JAVASCRIPT_END()
{
  echo "\n// -->\n";
  echo "</script>\n";
}

function checklogin()
{
  $flag = 0;

  global $username, $l_global_needlogin, $l_global_died;
  global $password, $l_login_died, $l_die_please;
  global $db, $dbtables;

  $result1 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username' LIMIT 1");
  $playerinfo = $result1->fields;

  /* Check the cookie to see if username/password are empty - check password against database */
  if ($username == "" or $password == "" or $password != $playerinfo['password'])
  {
    echo $l_global_needlogin;
    $flag = 1;
  }

  /* Check for destroyed ship */
  if ($playerinfo['ship_destroyed'] == "Y")
  {
    /* if the player has an escapepod, set the player up with a new ship */
    if ($playerinfo['dev_escapepod'] == "Y")
    {
      $result2 = $db->Execute("UPDATE $dbtables[ships] SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N',dev_lssd='N' where email='$username'");
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
  if ($server_closed && $flag==0)
  {
    echo $l_login_closed_message;
    $flag=1;
  }



  return $flag;
}

function connectdb($do_die = true) // Returns true, false or a halt.
{
    global $dbhost, $dbport, $dbuname, $dbpass, $dbname;
    global $db_type, $db_persistent;
    global $db;

    // Not too sure if we still need these variables.
    global $default_lang, $lang, $gameroot;

    // Check to see if we are already connected to the database.
    // If so just return true.
    if ($db instanceof ADOConnection)
    {
        return (boolean) true;
    }

    // Ok, seems that we are not connected to the database at this current time.
    // So we now need to setup all the database connection now.
    if (!empty($dbport))
    {
        $dbhost.= ":$dbport";
    }

    $db = NewADOConnection($db_type);
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

    if ($db_persistent == 1)
    {
        $result = @$db->PConnect("$dbhost", "$dbuname", "$dbpass", "$dbname");
    }
    else
    {
        $result = @$db->Connect("$dbhost", "$dbuname", "$dbpass", "$dbname");
    }

    // Check to see if we have connected ok.
    // This should work...
    if ( ($db instanceof ADOConnection) && (is_resource($db->_connectionID) || is_object($db->_connectionID)) )
    {
        // Set our character set to utf-8
        $db->Execute("set names 'utf8'");

        // Yes we connected ok, so return true.
        return (boolean) true;
    }
    else
    {
        // Bad news, we failed to connect to the database.
        if ($do_die)
        {
            // We need to display the error message onto the screen.
            echo "Unable to connect to the Database.<br>\n";
            echo "Database Error: ". $db->ErrorNo() .": ". $db->ErrorMsg() ."<br>\n";

            // We need to basically die here to stop the game.
            die ("SYSTEM HALT<br>\n");
        }
        // We need to return false.
        return (boolean) false;
    }
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
  setcookie("userpass",$userpass,time()+(3600*24)*365,$gamepath,$gamedomain);
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
    $db->Execute("INSERT INTO $dbtables[logs] VALUES(NULL, $sid, $log_type, NOW(), '$data')");
  }
}

function adminlog($log_type, $data = "")
{
        global $db, $dbtables;
        /* write log_entry to the admin log  */
        $ret = (boolean) false;
        $data = addslashes($data);
        if (!empty($log_type))
        {
                $ret = $db->Execute("INSERT INTO $dbtables[logs] VALUES(NULL, 0, $log_type, NOW(), '$data')");
        }
        if (!$ret)
        {
                return (boolean) false;
        }
        else
        {
                return (boolean) true;
        }
}

function gen_score($sid)
{
  global $dbtables,$db;
  global $upgrade_factor;
  global $upgrade_cost;
  global $torpedo_price;
  global $armor_price;
  global $fighter_price;
  global $ore_price;
  global $organics_price;
  global $goods_price;
  global $energy_price;
  global $colonist_price;
  global $dev_genesis_price;
  global $dev_beacon_price;
  global $dev_emerwarp_price;
  global $dev_warpedit_price;
  global $dev_minedeflector_price;
  global $dev_escapepod_price;
  global $dev_fuelscoop_price;
  global $dev_lssd_price;
  global $base_ore;
  global $base_goods;
  global $base_organics;
  global $base_credits;

  $calc_hull              = "ROUND(pow($upgrade_factor,hull))";
  $calc_engines           = "ROUND(pow($upgrade_factor,engines))";
  $calc_power             = "ROUND(pow($upgrade_factor,power))";
  $calc_computer          = "ROUND(pow($upgrade_factor,computer))";
  $calc_sensors           = "ROUND(pow($upgrade_factor,sensors))";
  $calc_beams             = "ROUND(pow($upgrade_factor,beams))";
  $calc_torp_launchers    = "ROUND(pow($upgrade_factor,torp_launchers))";
  $calc_shields           = "ROUND(pow($upgrade_factor,shields))";
  $calc_armor             = "ROUND(pow($upgrade_factor,armor))";
  $calc_cloak             = "ROUND(pow($upgrade_factor,cloak))";
  $calc_levels            = "($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armor+$calc_cloak)*$upgrade_cost";

  $calc_torps             = "$dbtables[ships].torps*$torpedo_price";
  $calc_armor_pts         = "armor_pts*$armor_price";
  $calc_ship_ore          = "ship_ore*$ore_price";
  $calc_ship_organics     = "ship_organics*$organics_price";
  $calc_ship_goods        = "ship_goods*$goods_price";
  $calc_ship_energy       = "ship_energy*$energy_price";
  $calc_ship_colonists    = "ship_colonists*$colonist_price";
  $calc_ship_fighters     = "ship_fighters*$fighter_price";
  $calc_equip             = "$calc_torps+$calc_armor_pts+$calc_ship_ore+$calc_ship_organics+$calc_ship_goods+$calc_ship_energy+$calc_ship_colonists+$calc_ship_fighters";

  $calc_dev_warpedit      = "dev_warpedit*$dev_warpedit_price";
  $calc_dev_genesis       = "dev_genesis*$dev_genesis_price";
  $calc_dev_beacon        = "dev_beacon*$dev_beacon_price";
  $calc_dev_emerwarp      = "dev_emerwarp*$dev_emerwarp_price";
  $calc_dev_escapepod     = "if (dev_escapepod='Y', $dev_escapepod_price, 0)";
  $calc_dev_fuelscoop     = "if (dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
  $calc_dev_lssd          = "if (dev_lssd='Y', $dev_lssd_price, 0)";
  $calc_dev_minedeflector = "dev_minedeflector*$dev_minedeflector_price";
  $calc_dev               = "$calc_dev_warpedit+$calc_dev_genesis+$calc_dev_beacon+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_fuelscoop+$calc_dev_minedeflector+$calc_dev_lssd";

  $calc_planet_goods      = "SUM($dbtables[planets].organics)*$organics_price+SUM($dbtables[planets].ore)*$ore_price+SUM($dbtables[planets].goods)*$goods_price+SUM($dbtables[planets].energy)*$energy_price";
  $calc_planet_colonists  = "SUM($dbtables[planets].colonists)*$colonist_price";
  $calc_planet_defence    = "SUM($dbtables[planets].fighters)*$fighter_price+if ($dbtables[planets].base='Y', $base_credits+SUM($dbtables[planets].torps)*$torpedo_price, 0)";
  $calc_planet_credits    = "SUM($dbtables[planets].credits)";

  $res = $db->Execute("SELECT $calc_levels+$calc_equip+$calc_dev+$dbtables[ships].credits+$calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits AS score FROM $dbtables[ships] LEFT JOIN $dbtables[planets] ON $dbtables[planets].owner=ship_id WHERE ship_id=$sid AND ship_destroyed='N'");
  $row = $res->fields;
  $score = $row['score'];
  $res = $db->Execute("SELECT balance, loan FROM $dbtables[ibank_accounts] where ship_id = $sid");
  if ($res)
  {
     $row = $res->fields;
     $score += ($row['balance'] - $row['loan']);
  }
  if ($score<0) $score=0;
  $score = ROUND(SQRT($score));
  $db->Execute("UPDATE $dbtables[ships] SET score=$score WHERE ship_id=$sid");

  return $score;
}

function db_kill_player($ship_id, $remove_planets = false)
{
  global $default_prod_ore;
  global $default_prod_organics;
  global $default_prod_goods;
  global $default_prod_energy;
  global $default_prod_fighters;
  global $default_prod_torp;
  global $gameroot;
  global $db,$dbtables;

  include("languages/english.inc");

  $db->Execute("UPDATE $dbtables[ships] SET ship_destroyed='Y',on_planet='N',sector=0,cleared_defences=' ' WHERE ship_id=$ship_id");
  $db->Execute("DELETE from $dbtables[bounty] WHERE placed_by = $ship_id");

  $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner='$ship_id' AND base='Y'");
  $i=0;

  while (!$res->EOF && $res)
  {
    $sectors[$i] = $res->fields[sector_id];
    $i++;
    $res->MoveNext();
  }

  if ($remove_planets == true && $ship_id > 0)
  {
    $db->Execute("DELETE from $dbtables[planets] WHERE owner = $ship_id");
  }
  else
  {
    $db->Execute("UPDATE $dbtables[planets] SET owner=0, corp=0, fighters=0, base='N' WHERE owner=$ship_id");
  }

  if (!empty($sectors))
  {
    foreach ($sectors as $sector)
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

  $headline = $name['character_name'] . $l_killheadline;

  $newstext=str_replace("[name]",$name['character_name'],$l_news_killed);

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

  return round(mypw($level_factor, $level_hull) * 100);
}

function NUM_ENERGY($level_power)
{
  global $level_factor;

  return round(mypw($level_factor, $level_power) * 500);
}

function NUM_FIGHTERS($level_computer)
{
  global $level_factor;

  return round(mypw($level_factor, $level_computer) * 100);
}

function NUM_TORPEDOES($level_torp_launchers)
{
  global $level_factor;

  return round(mypw($level_factor, $level_torp_launchers) * 100);
}

function NUM_ARMOR($level_armor)
{
  global $level_factor;

  return round(mypw($level_factor, $level_armor) * 100);
}

function NUM_BEAMS($level_beams)
{
  global $level_factor;

  return round(mypw($level_factor, $level_beams) * 100);
}

function NUM_SHIELDS($level_shields)
{
  global $level_factor;

  return round(mypw($level_factor, $level_shields) * 100);
}

function SCAN_SUCCESS($level_scan, $level_cloak)
{
  return (5 + $level_scan - $level_cloak) * 5;
}

function SCAN_ERROR($level_scan, $level_cloak)
{
  global $scan_error_factor;

  $sc_error = (4 + $level_scan / 2 - $level_cloak / 2) * $scan_error_factor;

  if ($sc_error<1)
  {
    $sc_error=1;
  }
  if ($sc_error>99)
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
    if ($result3 > 0)
    {
       while (!$result3->EOF && $num_mines > 0)
       {
          $row = $result3->fields;
          if ($row[quantity] > $num_mines)
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
    if ($result3 > 0)
    {
       while (!$result3->EOF && $num_fighters > 0)
       {
          $row=$result3->fields;
          if ($row[quantity] > $num_fighters)
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
    global $db, $dbtables;
    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' ");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if ($result3 > 0)
    {
       while (!$result3->EOF)
       {

          playerlog($result3->fields[ship_id],LOG_RAW, $message);
          $result3->MoveNext();
       }
    }
}

function distribute_toll($sector, $toll, $total_fighters)
{
    global $db, $dbtables;

    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' AND defence_type ='F' ");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if ($result3 > 0)
    {
       while (!$result3->EOF)
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
   if ($result1 > 0)
   {
      while (!$result1->EOF)
      {
         $row=$result1->fields;
         $deftype = $row[defence_type] == 'F' ? 'Fighters' : 'Mines';
         $qty = $row['quantity'];
         $result2 = $db->Execute("SELECT * from $dbtables[sector_defence] where sector_id = $row[sector_id] and ship_id <> $ship_id ORDER BY quantity DESC");
         if ($result2 > 0)
         {
            while (!$result2->EOF && $qty > 0)
            {
               $cur = $result2->fields;
               $targetdeftype = $cur[defence_type] == 'F' ? $l_fighters : $l_mines;
               if ($qty > $cur['quantity'])
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

   if ($result1 > 0)
   {
      while (!$result1->EOF)
      {
         $row = $result1->fields;
         $result2 = $db->Execute("SELECT * from $dbtables[ships] where on_planet = 'Y' and planet_id = '$row[planet_id]' and ship_id <> '$ship_id' ");
         if ($result2 > 0)
         {
            while (!$result2->EOF )
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
  if ($num_bases > 0)
  {

   while (!$res->EOF)
    {
      $bases[$i] = $res->fields;
      $i++;
      $res->MoveNext();
    }
  }
  else

    return "Sector ownership didn't change";

  $owner_num = 0;

  foreach ($bases as $curbase)
  {
    $curcorp=-1;
    $curship=-1;
    $loop = 0;
    while ($loop < $owner_num)
    {
      if ($curbase[corp] != 0)
      {
        if ($owners[$loop][type] == 'C')
        {
          if ($owners[$loop][id] == $curbase[corp])
          {
            $curcorp=$loop;
            $owners[$loop][num]++;
          }
        }
      }

      if ($owners[$loop][type] == 'S')
      {
        if ($owners[$loop][id] == $curbase[owner])
        {
          $curship=$loop;
          $owners[$loop][num]++;
        }
      }

      $loop++;
    }

    if ($curcorp == -1)
    {
      if ($curbase[corp] != 0)
      {
         $curcorp=$owner_num;
         $owner_num++;
         $owners[$curcorp][type] = 'C';
         $owners[$curcorp][num] = 1;
         $owners[$curcorp][id] = $curbase[corp];
      }
    }

    if ($curship == -1)
    {
      if ($curbase[owner] != 0)
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
  while ($loop < $owner_num)
  {
    if ($owners[$loop][type] == 'C')
      $nbcorps++;
    else
    {
      $res = $db->Execute("SELECT team FROM $dbtables[ships] WHERE ship_id=" . $owners[$loop][id]);
      if ($res && $res->RecordCount() != 0)
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
  if ($nbcorps > 1)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");

    return $l_global_warzone;
  }

  //More than one unallied ship, war
  $numunallied = 0;
  foreach ($scorps as $corp)
  {
    if ($corp == 0)
      $numunallied++;
  }
  if ($numunallied > 1)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");

    return $l_global_warzone;
  }

  //Unallied ship, another corp present, war
  if ($numunallied > 0 && $nbcorps > 0)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");

    return $l_global_warzone;
  }

  //Unallied ship, another ship in a corp, war
  if ($numunallied > 0)
  {
    $query = "SELECT team FROM $dbtables[ships] WHERE (";
    $i=0;
    foreach ($ships as $ship)
    {
      $query = $query . "ship_id=$ship";
      $i++;
      if ($i!=$nbships)
        $query = $query . " OR ";
      else
        $query = $query . ")";
    }
    $query = $query . " AND team!=0";
    $res = $db->Execute($query);
    if ($res->RecordCount() != 0)
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
    if ($owners[$i][num] > $owners[$winner][num])
      $winner = $i;
    elseif ($owners[$i][num] == $owners[$winner][num])
    {
      if ($owners[$i][type] == 'C')
        $winner = $i;
    }
    $i++;
  }

  if ($owners[$winner][num] < $min_bases_to_own)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");

    return $l_global_nzone;
  }


  if ($owners[$winner][type] == 'C')
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
    foreach ($owners as $curowner)
    {
      if ($curowner[type] == 'S' && $curowner[id] != $owners[$winner][id] && $curowner[num] == $owners[winners][num])
        $onpar = 1;
        break;
    }

    //Two allies have the same number of bases
    if ($onpar == 1)
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

function player_insignia_name($a_username)
{
    // Somewhat inefficient, but I think this is the best way to do this.

    global $db, $dbtables, $username;
    global $l_insignia;

    // Ok, first things first, always make sure our variable that is to be returned is unset or null.
    unset($player_insignia);

    // Lookup players score.
    $res = $db->Execute("SELECT score FROM $dbtables[ships] WHERE email='$a_username'");
    $playerinfo = $res->fields;

    for ($i = 0; $i < 20; $i++)
    {
        $value = pow(2, $i*2);
        if (!$value)
        {
            // pow returned false so we need to return an error.
            $player_insignia = "<span style='color:#f00;'>ERR</span> [<span style='color:#09f; font-size:12px; cursor:help;' title='Error looking up Insignia, Please Report.'>?</span>]";
            break;
        }

        $value *= (500 * 2);
        if ($playerinfo['score'] <= $value)
        {
            // Ok we have found our Insignia, now set and break out of the for loop.
            $temp_insignia = "l_insignia_" . $i;
            $player_insignia = $$temp_insignia;
            break;
        }
    }

    if (!isset($player_insignia))
    {
        // Hmm, player has out ranked out highest rank, so just return that.
        $player_insignia = end($l_insignia);
    }

    return $player_insignia;
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

function stripnum($str)
{
    $output = preg_replace('/[^0-9]/','',$str);

    return $output;
}

function collect_bounty($attacker,$bounty_on)
{
   global $db,$dbtables,$l_by_thefeds;
   $res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[ships] WHERE bounty_on = $bounty_on AND bounty_on = ship_id and placed_by <> 0");
   if ($res)
   {
      while (!$res->EOF)
      {
         $bountydetails = $res->fields;
         if ($res->fields[placed_by] == 0)
         {
            $placed = $l_by_thefeds;
         }
         else
         {
            $res2 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id = $bountydetails[placed_by]");
            $placed = $res2->fields[character_name];
         }
         $update = $db->Execute("UPDATE $dbtables[ships] SET credits = credits + $bountydetails[amount] WHERE ship_id = $attacker");
         $delete = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");

         playerlog($attacker, LOG_BOUNTY_CLAIMED, "$bountydetails[amount]|$bountydetails[character_name]|$placed");
         playerlog($bountydetails[placed_by],LOG_BOUNTY_PAID,"$bountydetails[amount]|$bountydetails[character_name]");

         $res->MoveNext();
      }
   }
   $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_on = $bounty_on");
}

function cancel_bounty($bounty_on)
{
   global $db,$dbtables;
   $res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[ships] WHERE bounty_on = $bounty_on AND bounty_on = ship_id");
   if ($res)
   {
      while (!$res->EOF)
      {
         $bountydetails = $res->fields;
         if ($bountydetails[placed_by] <> 0)
         {
            $update = $db->Execute("UPDATE $dbtables[ships] SET credits = credits + $bountydetails[amount] WHERE ship_id = $bountydetails[placed_by]");

            playerlog($bountydetails[placed_by],LOG_BOUNTY_CANCELLED,"$bountydetails[amount]|$bountydetails[character_name]");
         }
         $delete = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");
         $res->MoveNext();
      }
   }
}

function get_player($ship_id)
{
   global $db,$dbtables;
   $res = $db->Execute("SELECT character_name from $dbtables[ships] where ship_id = $ship_id");
   if ($res)
   {
      $row = $res->fields;
      $character_name = $row[character_name];

      return $character_name;
   }
   else
   {
      return "Unknown";
   }
}

function log_move($ship_id,$sector_id)
{
   global $db,$dbtables;
   $res = $db->Execute("INSERT INTO $dbtables[movement_log] (ship_id,sector_id,time) VALUES ($ship_id,$sector_id,NOW())");
}

function isLoanPending($ship_id)
{
  global $db, $dbtables;
  global $IGB_lrate;

  $res = $db->Execute("SELECT loan, UNIX_TIMESTAMP(loantime) AS time FROM $dbtables[ibank_accounts] WHERE ship_id=$ship_id");
  if ($res)
  {
    $account=$res->fields;

    if ($account['loan'] == 0)

      return false;

    $curtime=time();
    $difftime = ($curtime - $account['time']) / 60;
    if ($difftime > $IGB_lrate)

      return true;
    else

      return false;
  }
  else

    return false;

}

function get_avg_tech($ship_info = null, $type = "ship")
{
        // Defined in config.php
        global $calc_ship_tech, $calc_planet_tech;

        if ($type == "ship")
        {
                $calc_tech = $calc_ship_tech;
        }
        else
        {
                $calc_tech = $calc_planet_tech;
        }

        $count = count($calc_tech);

        $shipavg  = 0;
        for ($i = 0; $i < $count; $i++)
        {
                $shipavg += $ship_info[$calc_tech[$i]];
        }
        $shipavg /= $count;

        return $shipavg;
}

function bnt_autoload($classname)
{
    $class_location = "classes/" . $classname . ".php";
    if (is_readable($class_location))
    {
        include($class_location);
    }
}

function isSameTeam($attackerTeam = null, $attackieTeam = null)
{
        if ( ($attackerTeam != $attackieTeam) || ($attackerTeam == 0 || $attackieTeam == 0) )
        {
                return (boolean) false;
        }
        else
        {
                return (boolean) true;
        }
}

function getLanguageVars($db = NULL, $dbtables, $language = NULL, $categories = NULL, &$langvars = NULL)
{
    // Check if all supplied args are valid, if not return false.
    if (is_null($db) || is_null($language) || !is_array($categories))
    {
        return false;
    }

    foreach($categories as $category)
    {
        $result = $db->CacheGetAll("SELECT name, value FROM $dbtables[languages] WHERE category=? AND language=?;", array($category, $language));
        foreach($result as $key=>$value)
        {
            # Now cycle through returned array and add into langvars.
            $langvars[$value['name']] = $value['value'];
        }
    }

    return true; // Results were added into array, signal that we were successful.
}
?>
