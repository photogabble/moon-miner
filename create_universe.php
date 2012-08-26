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
// File: create_universe.php

// This is required by Setup Info, So DO NOT REMOVE
// create_universe_port_fix,0.2.0,25-02-2004,TheMightyDude

$index_page = true;
include 'global_includes.php';

// HTML Table Functions

if (!function_exists('print_flush'))
{
    function print_flush ($text="")
    {
        echo $text;
//        flush();
    }
}

if (!function_exists('true_or_false'))
{
    function true_or_false ($true_or_false, $stat, $true, $false)
    {
        return(($true_or_false == $Stat) ? $true : $false);
    }
}

if (!function_exists('table_header'))
{
    function table_header ($title="")
    {
        print_flush ( "<div align=\"center\">\n");
        print_flush ( "  <center>\n");
        print_flush ( "  <table border=\"0\" cellpadding=\"1\" width=\"700\" cellspacing=\"1\" bgcolor=\"#000000\">\n");
        print_flush ( "    <tr>\n");
        print_flush ( "      <th width=\"700\" colspan=\"2\" height=\"20\" bgcolor=\"#9999cc\" align=\"left\"><font color=\"#000000\" size=\"2\">$title</font></th>\n");
        print_flush ( "    </tr>\n");
    }
}

if (!function_exists('table_row'))
{
    function table_row ($db, $data, $failed="Failed", $passed="Passed")
    {
        $err = true_or_false (0, $db->ErrorNo(), "No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());
        print_flush ( "    <tr title=\"$err\">\n");
        print_flush ( "      <td width=\"600\" bgcolor=\"#ccccff\"><font size=\"1\" color=\"#000000\">$data</font></td>\n");
        if ($db->ErrorNo()!=0)
            {print_flush ( "      <td width=\"100\" align=\"center\" bgcolor=\"#C0C0C0\"><font size=\"1\" color=\"red\">$failed</font></td>\n");}
        else
            {print_flush ( "      <td width=\"100\" align=\"center\" bgcolor=\"#C0C0C0\"><font size=\"1\" color=\"Blue\">$passed</font></td>\n");}
        echo "    </tr>\n";
    }
}

if (!function_exists('table_2col'))
{
    function table_2col ($name, $value)
    {
        print_flush ("    <tr>\n");
        print_flush ( "      <td width=\"600\" bgcolor=\"#ccccff\"><font size=\"1\" color=\"#000000\">$name</font></td>\n");
        print_flush ( "      <td width=\"100\" bgcolor=\"#C0C0C0\"><font size=\"1\" color=\"#000000\">$value</font></td>\n");
        print_flush ( "    </tr>\n");
    }
}

if (!function_exists('table_1col'))
{
    function table_1col ($data)
    {
        print_flush ( "    <tr>\n");
        print_flush ( "      <td width=\"700\" colspan=\"2\" bgcolor=\"#C0C0C0\" align=\"left\"><font color=\"#000000\" size=\"1\">$data</font></td>\n");
        print_flush ( "    </tr>\n");
    }
}

if (!function_exists('table_spacer'))
{
    function table_spacer ()
    {
        print_flush ( "    <tr>\n");
        print_flush ( "      <td width=\"100%\" colspan=\"2\" bgcolor=\"#9999cc\" height=\"1\"></td>\n");
        print_flush ( "    </tr>\n");
    }
}

if (!function_exists('table_footer'))
{
    function table_footer ($footer='')
    {
        if (!empty($footer))
        {
            print_flush ( "    <tr>\n");
            print_flush ( "      <td width=\"100%\" colspan=\"2\" bgcolor=\"#9999cc\" align=\"left\"><font color=\"#000000\" size=\"1\">$footer</font></td>\n");
            print_flush ( "    </tr>\n");
        }
        print_flush ( "  </table>\n");
        print_flush ( "  </center>\n");
        print_flush ( "</div><p>\n");
    }
}

// Set timelimit and randomize timer.

set_time_limit(0);

// Include config files and db scheme.

include 'includes/schema.php';

update_cookie();

// This is needed here until the language database is installed
$title = 'Create universe';
include 'header.php';

connect_database ();

bigtitle ();

// Manually set step var if info isn't correct.

if (!isset($_POST['swordfish']))
{
    $_POST['swordfish'] = '';
}

if (!isset($engage))
{
    $engage = '';
}


if ($adminpass!= $_POST['swordfish'])
{
    $step="0";
}

if ($engage == "" && $adminpass == $_POST['swordfish'] )
{
    $step="1";
}

if ($engage == "1" && $adminpass == $_POST['swordfish'] )
{
    $step="2";
}

// Main switch statement.

switch ($step) {
   case "1":
      echo "<form action=create_universe.php method=post>";
      echo "<table>";

// Domain Check
if ($bnt_ls)
    {
      echo "<tr><td colspan=2 aling=center>";
      echo "<table border=1 cellspacing=0 cellpadding=2 width=100%>";
      echo "<tr><td>";

      echo "<font color=red><strong>Domain Check!</strong></font><br>";
      echo "Make sure you call the <strong>create_universe.php</strong> from the same URL as:<br>";
      echo "- your cronjob calls <strong>scheduler.php</strong><br>";

        echo "<br>This URL will be used on the Public list: ";
        $gm_url = $SERVER_NAME;
        if ( ($gm_url == "localhost") || ($gm_url == "127.0.0.1") || ($gm_url == "") )
        {
            $gm_url = $gamedomain . $gamepath;
            $gm_url = (substr($gm_url,0,1)==".")?substr($gm_url,1):$gm_url;
            echo "<font COLOR=red><strong>http://$gm_url</strong></font><br>";
            echo "It is better if you run the create_universe.php from the correct URL!<br>";
            echo "Or correct the gamedomain and gamepath in your <strong>config_local.php</strong><br>";
            echo "This URL is trasmited if your cronjob calls scheduler.php with localhost!";
        } else {
            $gm_url = $gm_url . strrev(strstr(strrev($_SERVER['PHP_SELF']),"/"));
            echo "<font COLOR=green><strong>http://$gm_url</strong></font><br>";
            echo "YES, if this URL is correct ... continue !<br>";
            echo "Remember: if your cronjob calls scheduler.php with localhost than run create_universe with localhost to check the correctnes of the transmitted URL!";
        }


      echo "</td></tr>";
      echo "</table></td></tr>";
    }
echo"</table>";
// Domain Check End

    table_header ("Create Universe [Base/Planet Setup]");
    table_2col ("Percent Special","<input type=text name=special size=10 maxlength=10 value=1>");
    table_2col ("Percent Ore","<input type=text name=ore size=10 maxlength=10 value=15>");
    table_2col ("Percent Organics","<input type=text name=organics size=10 maxlength=10 value=10>");
    table_2col ("Percent Goods","<input type=text name=goods size=10 maxlength=10 value=15>");
    table_2col ("Percent Energy","<input type=text name=energy size=10 maxlength=10 value=10>");

    table_1col ("Percent Empty: Equal to 100 - total of above.");

    table_2col ("Initial Commodities to Sell [% of max]","<input type=text name=initscommod size=10 maxlength=10 value=100.00>");
    table_2col ("Initial Commodities to Buy [% of max]","<input type=text name=initbcommod size=10 maxlength=10 value=100.00>");
    table_footer (" ");

    table_header ("Create Universe [Sector/Link Setup] --- Stage 1");

    $fedsecs = intval($sector_max / 200);
    $loops = intval($sector_max / 500);

    table_2col ("Number of sectors total (<strong>overrides config</strong>)","<input type=text name=sektors size=10 maxlength=10 value=$sector_max>");
    table_2col ("Number of Federation sectors","<input type=text name=fedsecs size=10 maxlength=10 value=$fedsecs>");
    table_2col ("Number of loops","<input type=text name=loops size=10 maxlength=10 value=$loops>");
    table_2col ("Percent of sectors with unowned planets","<input type=text name=planets size=10 maxlength=10 value=10>");
    table_footer (" ");

    echo "<input type=hidden name=engage value=1>\n";
    echo "<input type=hidden name=step value=2>\n";
    echo "<input type=hidden name=swordfish value=$swordfish>\n";

    table_header ("Submit Settings");
    table_1col ("<p align='center'><input type=submit value=Submit><input type=reset value=Reset></p>");
    table_footer (" ");

    echo "</form>";
      break;
   case "2":

    table_header ("Create Universe Confirmation [So you would like your $sector_max sector universe to have:] --- Stage2");

      $sector_max = round($sektors);
      if ($fedsecs > $sector_max)
      {
    table_1col ("<font color=red>The number of Federation sectors must be smaller than the size of the universe!</font>");
    table_footer (" ");
         break;
      }
      $spp = round($sector_max*$special/100);
      $oep = round($sector_max*$ore/100);
      $ogp = round($sector_max*$organics/100);
      $gop = round($sector_max*$goods/100);
      $enp = round($sector_max*$energy/100);
      $empty = $sector_max-$spp-$oep-$ogp-$gop-$enp;
      $nump = round ($sector_max*$planets/100);

      echo "<form action=create_universe.php method=post>\n";
      echo "<input type=hidden name=step value=3>\n";
      echo "<input type=hidden name=spp value=$spp>\n";
      echo "<input type=hidden name=oep value=$oep>\n";
      echo "<input type=hidden name=ogp value=$ogp>\n";
      echo "<input type=hidden name=gop value=$gop>\n";
      echo "<input type=hidden name=enp value=$enp>\n";
      echo "<input type=hidden name=initscommod value=$initscommod>\n";
      echo "<input type=hidden name=initbcommod value=$initbcommod>\n";
      echo "<input type=hidden name=nump value=$nump>\n";
      echo "<input type=hidden name=fedsecs value=$fedsecs>\n";
      echo "<input type=hidden name=loops value=$loops>\n";
      echo "<input type=hidden name=engage value=2>\n";
      echo "<input type=hidden name=swordfish value=$swordfish>\n";

    table_2col ("Special ports",$spp);
    table_2col ("Ore ports",$oep);
    table_2col ("Organics ports",$ogp);
    table_2col ("Goods ports",$gop);
    table_2col ("Energy ports",$enp);
    table_spacer ();
    table_2col ("Initial commodities to sell",$initscommod."%");
    table_2col ("Initial commodities to buy",$initbcommod."%");
    table_spacer ();
    table_2col ("Empty sectors",$empty);
    table_2col ("Federation sectors",$fedsecs);
    table_2col ("Loops",$loops);
    table_2col ("Unowned planets",$nump);
    table_spacer ();

    table_1col ("<p align='center'><input type=submit value=Confirm></p>");
    table_spacer ();

    table_1col ("<font color=red>WARNING: ALL TABLES WILL BE DROPPED AND THE GAME WILL BE RESET WHEN YOU CLICK 'CONFIRM'!</font>");
    table_footer (" ");

      echo "</form>";

      break;
   case "3":
      create_schema();
      include 'includes/ini_to_db.php';

      $gameconfig_result = ini_to_db($db, "config/configset_classic.ini.php", "gameconfig", "game");
      if ($gameconfig_result)
      {
          echo "Config variables imported into database successfully.\n<br>";
      }
      else
      {
          echo "Config variables NOT imported into the database successfully.\n<br>";
      }

      $result = ini_to_db($db, "languages/english.ini.php", "languages", "english");
      if ($result)
      {
          echo "English language imported into database successfully.\n<br>";
      }
      else
      {
          echo "English language NOT imported into database successfully.\n<br>";
      }

      $result = ini_to_db($db, "languages/french.ini.php", "languages", "french");
      if ($result)
      {
          echo "French language imported into database successfully.\n<br>";
      }
      else
      {
          echo "French language NOT imported into database successfully.\n<br>";
      }

      $result = ini_to_db($db, "languages/german.ini.php", "languages", "german");
      if ($result)
      {
          echo "German language imported into database successfully.\n<br>";
      }
      else
      {
          echo "German language NOT imported into database successfully.\n<br>";
      }

      $result = ini_to_db($db, "languages/spanish.ini.php", "languages", "spanish");
      if ($result)
      {
          echo "Spanish language imported into database successfully.\n<br>";
      }
      else
      {
          echo "Spanish language NOT imported into database successfully.\n<br>";
      }

      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=4>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<input type=hidden name=fedsecs value=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<p align='center'><input type=submit value=Confirm></p>";
      echo "</form>";
      break;
   case "4":

      // New database driven language entries
      load_languages($db, $lang, array('create_universe', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars, $db_logging);
      table_header ("Setting up Sectors --- STAGE 4");

      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

      $insert = $db->Execute("INSERT INTO {$db->prefix}universe (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES ('0', 'Sol', '1', 'special', '0', '0', '0', '0', 'Sol: Hub of the Universe', '0', '0', '0')");
      db_op_result ($db, $insert, __LINE__, __FILE__);
      table_row ($db, "Creating Sol sector","Failed","Created");

      $update = $db->Execute("UPDATE {$db->prefix}universe SET sector_id=0 WHERE sector_id=1");
      db_op_result ($db, $update, __LINE__, __FILE__);
      table_row ($db, "Converting Sol Sector Id to 0","False","True");

      $insert = $db->Execute("INSERT INTO {$db->prefix}universe (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES ('1', 'Alpha Centauri', '1', 'energy',  '0', '0', '0', '0', 'Alpha Centauri: Gateway to the Galaxy', '0', '0', '1')");
      db_op_result ($db, $insert, __LINE__, __FILE__);
      table_row ($db, "Creating Alpha Centauri in sector 1","Failed","Created");

      table_spacer ();

      $remaining = $sector_max-2;
      ### Cycle through remaining sectors

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize)+1;
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>($sector_max)) $finish=($sector_max);
        $start=2;

        for ($i = 1; $i <= $loops; $i++)
        {
            $insert="INSERT INTO {$db->prefix}universe (sector_id,zone_id,angle1,angle2,distance) VALUES ";
            for ($j = $start; $j < $finish; $j++)
            {
                $distance=intval(mt_rand(1,$universe_size));
                $angle1=mt_rand(0,180);
                $angle2=mt_rand(0,90);
                $insert.="(NULL,'1',$angle1,$angle2,$distance)";
                if ($j<($finish-1)) $insert .= ", "; else $insert .= ";";
            }
            ### Now lets post the information to the mysql database.
//          $db->Execute("$insert");
            if ($start<$sector_max && $finish<=$sector_max) $db->Execute($insert);

        table_row ($db, "Inserting loop $i of $loops Sector Block [".($start)." - ".($finish-1)."] into the Universe.","Failed","Inserted");

            $start = $finish;
            $finish += $loopsize;
            if ($finish>($sector_max)) $finish=($sector_max);
        };

    table_spacer ();

      $replace = $db->Execute("REPLACE INTO {$db->prefix}zones (zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('1', 'Unchartered space', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', '0' )");
      db_op_result ($db, $replace, __LINE__, __FILE__);
      table_row ($db, "Setting up Zone (Unchartered space)","Failed","Set");

      $replace = $db->Execute("REPLACE INTO {$db->prefix}zones(zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('2', 'Federation space', 0, 'N', 'N', 'N', 'N', 'N', 'N',  'Y', 'N', '$fed_max_hull')");
      db_op_result ($db, $replace, __LINE__, __FILE__);
      table_row ($db, "Setting up Zone (Federation space)","Failed","Set");

      $replace = $db->Execute("REPLACE INTO {$db->prefix}zones(zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('3', 'Free-Trade space', 0, 'N', 'N', 'Y', 'N', 'N', 'N','Y', 'N', '0')");
      db_op_result ($db, $replace, __LINE__, __FILE__);
      table_row ($db, "Setting up Zone (Free-Trade space)","Failed","Set");

      $replace = $db->Execute("REPLACE INTO {$db->prefix}zones(zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('4', 'War Zone', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y','N', 'Y', '0')");
      db_op_result ($db, $replace, __LINE__, __FILE__);
      table_row ($db, "Setting up Zone (War Zone)","Failed","Set");

      $update = $db->Execute("UPDATE {$db->prefix}universe SET zone_id='2' WHERE sector_id<$fedsecs");
      db_op_result ($db, $update, __LINE__, __FILE__);
      table_row ($db, "Setting up the $fedsecs Federation Sectors","Failed","Set");

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Special Ports

# !!!!! DO NOT ALTER LOOPSIZE !!!!!
# This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($spp / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$spp) $finish=($spp);

    # Well since we hard coded a special port already, we start from 1.
        $start=1;

    table_spacer ();

        $sql_query=$db->Execute("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY RAND() DESC LIMIT $spp");
        db_op_result ($db, $sql_query, __LINE__, __FILE__);
        $update="UPDATE {$db->prefix}universe SET zone_id='3',port_type='special' WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update="UPDATE {$db->prefix}universe SET zone_id='3',port_type='special' WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $resx = $db->Execute($update);
            db_op_result ($db, $resx, __LINE__, __FILE__);

    table_row ($db, "Loop $i of $loops (Setting up Special Ports) Port [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$spp) $finish=($spp);
        }

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Ore Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($oep / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$oep) $finish=($oep);
        $start=0;

    table_spacer ();

        $sql_query=$db->Execute("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY RAND() DESC LIMIT $oep");
        db_op_result ($db, $sql_query, __LINE__, __FILE__);
        $update="UPDATE {$db->prefix}universe SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update="UPDATE {$db->prefix}universe SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $resx = $db->Execute($update);
            db_op_result ($db, $resx, __LINE__, __FILE__);

    table_row ($db, "Loop $i of $loops (Setting up Ore Ports) Block [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$oep) $finish=($oep);
        }

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Organic Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($ogp / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$ogp) $finish=($ogp);
        $start=0;

    table_spacer ();

        $sql_query=$db->Execute("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY RAND() DESC LIMIT $ogp");
        db_op_result ($db, $sql_query, __LINE__, __FILE__);
        $update="UPDATE {$db->prefix}universe SET port_type='organics',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update="UPDATE {$db->prefix}universe SET port_type='organics',port_ore=$initbore,port_organics=$initsorganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $resx = $db->Execute($update);
            db_op_result ($db, $resx, __LINE__, __FILE__);

    table_row ($db, "Loop $i of $loops (Setting up Organics Ports) Block [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$ogp) $finish=($ogp);
        }

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Goods Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($gop / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$gop) $finish=($gop);
        $start=0;

    table_spacer ();

        $sql_query=$db->Execute("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY RAND() DESC LIMIT $gop");
        db_op_result ($db, $sql_query, __LINE__, __FILE__);
        $update="UPDATE {$db->prefix}universe SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update="UPDATE {$db->prefix}universe SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $resx = $db->Execute($update);
            db_op_result ($db, $resx, __LINE__, __FILE__);

    table_row ($db, "Loop $i of $loops (Setting up Goods Ports) Block [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$gop) $finish=($gop);
        }

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Energy Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($enp / $loopsize);
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$enp) $finish=($enp);

    # Well since we hard coded an energy port already, we start from 1.
        $start=1;

    table_spacer ();

        $sql_query=$db->Execute("SELECT sector_id FROM {$db->prefix}universe WHERE port_type='none' ORDER BY RAND() DESC LIMIT $enp");
        db_op_result ($db, $sql_query, __LINE__, __FILE__);
        $update="UPDATE {$db->prefix}universe SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";

        for ($i = 1; $i <= $loops; $i++)
        {
            $update="UPDATE {$db->prefix}universe SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
            for ($j = $start; $j < $finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if ($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $resx = $db->Execute($update);
            db_op_result ($db, $resx, __LINE__, __FILE__);

    table_row ($db, "Loop $i of $loops (Setting up Energy Ports) Block [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$enp) $finish=($enp);
        }

    table_spacer ();
    table_footer ("Completed successfully");

      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=5>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<input type=hidden name=fedsecs value=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<p align='center'><input type=submit value=Confirm></p>";
      echo "</form>";
      include_once 'footer.php';
      break;
   case "5":

      // New database driven language entries
      load_languages($db, $lang, array('create_universe', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars, $db_logging);
        $p_add=0;$p_skip=0;$i=0;

table_header ("Setting up Universe Sectors --- Stage 5");

        do
        {
            $num = mt_rand(2, ($sector_max-1));
            $select = $db->Execute("SELECT {$db->prefix}universe.sector_id FROM {$db->prefix}universe, {$db->prefix}zones WHERE {$db->prefix}universe.sector_id=$num AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id AND {$db->prefix}zones.allow_planet='N'") or die("DB error");
            db_op_result ($db, $select, __LINE__, __FILE__);
            if ($select->RecordCount() == 0)
            {
                $insert = $db->Execute("INSERT INTO {$db->prefix}planets (colonists, owner, corp, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, sector_id) VALUES (2,0,0,$default_prod_ore,$default_prod_organics,$default_prod_goods,$default_prod_energy, $default_prod_fighters, $default_prod_torp,$num)");
                db_op_result ($db, $insert, __LINE__, __FILE__);
                $p_add++;
            }
        }
        while ($p_add < $nump);

table_row ($db, "Selecting $nump sectors to place unowned planets in.","Failed","Selected");

table_spacer ();

## Adds Sector Size *2 amount of links to the links table ##

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize)+1;
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$sector_max) $finish=($sector_max);
        $start=0;

        for ($i = 1; $i <= $loops; $i++)
        {
            $update = "INSERT INTO {$db->prefix}links (link_start,link_dest) VALUES ";
            for ($j = $start; $j < $finish; $j++)
            {
                $k = $j + 1;
                $update .= "($j,$k), ($k,$j)";
                if ($j<($finish-1)) $update .= ", "; else $update .= ";";
            }
            if ($start<$sector_max && $finish<=$sector_max)
            {
                $resx = $db->Execute($update);
                db_op_result ($db, $resx, __LINE__, __FILE__);
            }

            table_row ($db, "Creating loop $i of $loops sectors (from sector ".($start)." to ".($finish-1).") - loop $i","Failed","Created");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$sector_max) $finish=$sector_max;
        }

//      print_flush ("<br>Sector Links created successfully.<br>");

####################

table_spacer ();

//      print_flush ("<br>Randomly One-way Linking $i Sectors (out of $sector_max sectors)<br>\n");

## Adds Sector Size amount of links to the links table ##

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize)+1;
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$sector_max) $finish=($sector_max);
        $start=0;

        for ($i = 1; $i <= $loops; $i++)
        {
            $insert="INSERT INTO {$db->prefix}links (link_start,link_dest) VALUES ";
            for ($j = $start; $j < $finish; $j++)
            {
                $link1=intval(mt_rand(1,$sector_max-1));
                $link2=intval(mt_rand(1,$sector_max-1));
                $insert.="($link1,$link2)";
                if ($j<($finish-1)) $insert .= ", "; else $insert .= ";";
            }
#           print_flush ("<font color='#ff0'>Creating loop $i of $loopsize Random One-way Links (from sector ".($start)." to ".($finish-1).") - loop $i</font><br>\n");

            if ($start<$sector_max && $finish<=$sector_max)
            {
                $resx = $db->Execute($insert);
                db_op_result ($db, $resx, __LINE__, __FILE__);
            }

table_row ($db, "Creating loop $i of $loops Random One-way Links (from sector ".($start)." to ".($finish-1).") - loop $i","Failed","Created");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$sector_max) $finish=($sector_max);
        }

//      print_flush ("Completed successfully.<br>\n");

######################

table_spacer ();

//      print_flush ("<br>Randomly Two-way Linking Sectors<br>\n");

## Adds Sector Size*2 amount of links to the links table ##

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize)+1;
        if ($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$sector_max) $finish=($sector_max);
        $start=0;

        for ($i = 1; $i <= $loops; $i++)
        {
            $insert="INSERT INTO {$db->prefix}links (link_start,link_dest) VALUES ";
            for ($j = $start; $j < $finish; $j++)
            {
                $link1=intval(mt_rand(1,$sector_max-1));
                $link2=intval(mt_rand(1,$sector_max-1));
                $insert.="($link1,$link2), ($link2,$link1)";
                if ($j<($finish-1)) $insert .= ", "; else $insert .= ";";
            }
//          print_flush ("<font color='#ff0'>Creating loop $i of $loopsize Random Two-way Links (from sector ".($start)." to ".($finish-1).") - loop $i</font><br>\n");
            if ($start<$sector_max && $finish<=$sector_max)
            {
                $resx = $db->Execute($insert);
                db_op_result ($db, $resx, __LINE__, __FILE__);
            }

table_row ($db, "Creating loop $i of $loops Random Two-way Links (from sector ".($start)." to ".($finish-1).") - loop $i","Failed","Created");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$sector_max) $finish=($sector_max);
        }

$resx = $db->Execute("DELETE FROM {$db->prefix}links WHERE link_start = '{$sector_max}' OR link_dest ='{$sector_max}' ");
db_op_result ($db, $resx, __LINE__, __FILE__);
table_row ($db, "Removing links to and from the end of the Universe","Failed","Deleted");

table_footer ("Completed successfully.");

      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=7>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<INPUT TYPE=HIDDEN NAME=fedsecs VALUE=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<p align='center'><input type=submit value=Confirm></p>";
      echo "</form>";
      include_once 'footer.php';
      break;
   case "7":

      // New database driven language entries
      load_languages($db, $lang, array('create_universe', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars, $db_logging);
      table_header ("Configuring game scheduler --- Stage 7");

      table_2col ("Update ticks will occur every $sched_ticks minutes.","<p align='center'><font size=\"1\" color=\"Blue\">Already Set</font></p>");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_turns.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Turns will occur every $sched_turns minutes","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_defenses.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Defenses will be checked every $sched_turns minutes","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_xenobe.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Xenobes will play every $sched_turns minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_igb, 0, 'sched_igb.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Interests on IGB accounts will be accumulated every $sched_igb minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_news, 0, 'sched_news.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "News will be generated every $sched_news minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_planets, 0, 'sched_planets.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Planets will generate production every $sched_planets minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_ports, 0, 'sched_ports.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Ports will regenerate every $sched_ports minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_tow.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Ships will be towed from fed sectors every $sched_turns minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_ranking, 0, 'sched_ranking.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Rankings will be generated every $sched_ranking minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_degrade, 0, 'sched_degrade.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Sector Defences will degrade every $sched_degrade minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_apocalypse, 0, 'sched_apocalypse.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "The planetary apocalypse will occur every $sched_apocalypse minutes.","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, $sched_thegovernor, 0, 'sched_thegovernor.php', NULL,unix_timestamp(now()))");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "The Governor will run every $sched_thegovernor minutes.","Failed","Inserted");

      if ($bnt_ls===true)
      {
//            $db->Execute("INSERT INTO {$db->prefix}scheduler VALUES(NULL, 'Y', 0, 60, 0, 'bnt_ls_client.php', NULL,unix_timestamp(now()))");
//        table_row ($db, "The public list updater will occur every 60 minutes","Failed","Inserted");

            $creating=1;
//            include "bnt_ls_client.php";
      }
      table_footer ("Completed successfully");

      table_header ("Inserting Admins Acount Information");

      $update = $db->Execute("INSERT INTO {$db->prefix}ibank_accounts (ship_id,balance,loan) VALUES (1,0,0)");
      db_op_result ($db, $update, __LINE__, __FILE__);
      table_row ($db, "Inserting Admins ibank Information","Failed","Inserted");

      $stamp = date("Y-m-d H:i:s");
      $resxx = $db->Execute("INSERT INTO {$db->prefix}ships VALUES(NULL,'Game Admin\'s ship','N','Game Admin','$adminpass','$admin_mail',0,0,0,0,0,0,0,0,0,0,$start_armor,0,$start_credits,0,0,0,0,$start_energy,0,$start_fighters,0,$start_turns,'N',0,1,0,0,'N','N',0,0, '$stamp',0,0,0,0,'1.1.1.1',0,0,0,0,'Y','N','N','Y',' ','$default_lang', 'N')");
      db_op_result ($db, $resxx, __LINE__, __FILE__);

      table_1col ("Admins login Information:<br>Username: '$admin_mail'<br>Password: '$adminpass'");
      table_row ($db, "Inserting Admins Ship Information","Failed","Inserted");

      $resxx = $db->Execute("INSERT INTO {$db->prefix}zones VALUES(NULL,'Game Admin\'s Territory', 1, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
      db_op_result ($db, $resxx, __LINE__, __FILE__);
      table_row ($db, "Inserting Admins Zone Information","Failed","Inserted");
      table_footer ("Completed successfully.");

      print_flush ("<br><br><center><br><strong>Congratulations! Universe created successfully.</strong><br>");
      print_flush ("<strong>Click <a href=index.php>here</A> to return to the login screen.</strong></center>");

      include_once 'footer.php';
      break;
   default:
      echo "<form action=create_universe.php method=post>";
      echo "Password: <input type=password name=swordfish size=20 maxlength=20>&nbsp;&nbsp;";
      echo "<input type=submit value=Submit><input type=hidden name=step value=1>";
      echo "<input type=reset value=Reset>";
      echo "</form>";
      break;
}

//include "footer.php";
?>
