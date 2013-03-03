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
// File: includes/planet_combat.php

// Todo: Finish separating out variables in echo statements
if (strpos ($_SERVER['PHP_SELF'], 'planet_combat.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function planet_combat ($db)
{
    global $playerinfo, $ownerinfo, $planetinfo, $torpedo_price, $colonist_price, $ore_price, $organics_price, $goods_price, $energy_price;
    global $planetbeams, $planetfighters, $planetshields, $planettorps, $attackerbeams, $attackerfighters, $attackershields, $upgrade_factor, $upgrade_cost;
    global $attackertorps, $attackerarmor, $torp_dmg_rate, $level_factor, $attackertorpdamage, $start_energy, $min_value_capture, $l_cmb_atleastoneturn;
    global $l_cmb_atleastoneturn, $l_cmb_shipenergybb, $l_cmb_shipenergyab, $l_cmb_shipenergyas, $l_cmb_shiptorpsbtl, $l_cmb_shiptorpsatl;
    global $l_cmb_planettorpdamage, $l_cmb_beams, $l_cmb_fighters, $l_cmb_shields, $l_cmb_torps;
    global $l_cmb_torpdamage, $l_cmb_armor, $l_cmb_you, $l_cmb_planet, $l_cmb_combatflow, $l_cmb_defender, $l_cmb_attackingplanet;
    global $l_cmb_youfireyourbeams, $l_cmb_defenselost, $l_cmb_defenselost2, $l_cmb_planetarybeams, $l_cmb_planetarybeams2;
    global $l_cmb_youdestroyedplanetshields, $l_cmb_beamsexhausted, $l_cmb_breachedyourshields, $l_cmb_destroyedyourshields;
    global $l_cmb_breachedyourarmor, $l_cmb_destroyedyourarmor, $l_cmb_torpedoexchangephase, $l_cmb_nofightersleft;
    global $l_cmb_youdestroyfighters, $l_cmb_planettorpsdestroy, $l_cmb_planettorpsdestroy2, $l_cmb_torpsbreachedyourarmor;
    global $l_cmb_planettorpsdestroy3, $l_cmb_youdestroyedallfighters, $l_cmb_youdestroyplanetfighters, $l_cmb_fightercombatphase;
    global $l_cmb_youdestroyedallfighters2, $l_cmb_youdestroyplanetfighters2, $l_cmb_allyourfightersdestroyed, $l_cmb_fightertofighterlost;
    global $l_cmb_youbreachedplanetshields, $l_cmb_shieldsremainup, $l_cmb_fighterswarm, $l_cmb_swarmandrepel, $l_cmb_engshiptoshipcombat;
    global $l_cmb_shipdock, $l_cmb_approachattackvector, $l_cmb_noshipsdocked, $l_cmb_yourshipdestroyed, $l_cmb_escapepod;
    global $l_cmb_finalcombatstats, $l_cmb_youlostfighters, $l_cmb_youlostarmorpoints, $l_cmb_energyused, $l_cmb_planetdefeated;
    global $l_cmb_citizenswanttodie, $l_cmb_youmaycapture, $l_cmb_planetnotdefeated, $l_cmb_planetstatistics;
    global $l_cmb_fighterloststat, $l_cmb_energyleft, $l_planet_capture1;

    include_once './includes/collect_bounty.php';
    include_once './includes/calc_score.php';
    include_once './includes/ship_to_ship.php';

    if ($playerinfo['turns'] < 1 )
    {
        echo $l_cmb_atleastoneturn . "<br><br>";
        \bnt\bnttext::gotomain ($langvars);
        include_once './footer.php';
        die ();
    }

    // Planetary defense system calculation

    include_once './includes/calc_planet_beams.php';
    $planetbeams        = calc_planet_beams ($db, $ownerinfo, $base_defense, $planetinfo);

    $planetfighters     = $planetinfo['fighters'];

    include_once './includes/calc_planet_shields.php';
    $planetshields      = calc_planet_shields ($db);

    include_once './includes/calc_planet_torps.php';
    $planettorps        = calc_planet_torps ($db);

    // Attacking ship calculations

    $attackerbeams      = \bnt\CalcLevels::Beams ($playerinfo['beams'], $level_factor);
    $attackerfighters   = $playerinfo['ship_fighters'];
    $attackershields    = \bnt\CalcLevels::Shields ($playerinfo['shields'], $level_factor);
    $attackertorps      = round (pow ($level_factor, $playerinfo['torp_launchers'])) * 2;
    $attackerarmor      = $playerinfo['armor_pts'];

    // Now modify player beams, shields and torpedos on available materiel
    $start_energy = $playerinfo['ship_energy'];

    // Beams
    if ($attackerbeams > $playerinfo['ship_energy'])
    {
        $attackerbeams   = $playerinfo['ship_energy'];
    }
    $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackerbeams;

    // Shields
    if ($attackershields > $playerinfo['ship_energy'])
    {
        $attackershields = $playerinfo['ship_energy'];
    }
    $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackershields;

    // Torpedos
    if ($attackertorps > $playerinfo['torps'])
    {
        $attackertorps = $playerinfo['torps'];
    }
    $playerinfo['torps'] = $playerinfo['torps'] - $attackertorps;

    // Setup torp damage rate for both Planet and Ship
    $planettorpdamage   = $torp_dmg_rate * $planettorps;
    $attackertorpdamage = $torp_dmg_rate * $attackertorps;

    echo "
    <center>
    <hr>
    <table width='75%' border='0'>
    <tr align='center'>
    <td width='9%' height='27'></td>
    <td width='12%' height='27'><font color='white'>" . $l_cmb_beams . "</font></td>
    <td width='17%' height='27'><font color='white'>" . $l_cmb_fighters . "</font></td>
    <td width='18%' height='27'><font color='white'>" . $l_cmb_shields . "</font></td>
    <td width='11%' height='27'><font color='white'>" . $l_cmb_torps . "</font></td>
    <td width='22%' height='27'><font color='white'>" . $l_cmb_torpdamage . "</font></td>
    <td width='11%' height='27'><font color='white'>" . $l_cmb_armor . "</font></td>
    </tr>
    <tr align='center'>
    <td width='9%'> <font color='red'>" . $l_cmb_you . "</td>
    <td width='12%'><font color='red'><strong>" . $attackerbeams . "</strong></font></td>
    <td width='17%'><font color='red'><strong>" . $attackerfighters . "</strong></font></td>
    <td width='18%'><font color='red'><strong>" . $attackershields . "</strong></font></td>
    <td width='11%'><font color='red'><strong>" . $attackertorps . "</strong></font></td>
    <td width='22%'><font color='red'><strong>" . $attackertorpdamage . "</strong></font></td>
    <td width='11%'><font color='red'><strong>" . $attackerarmor . "</strong></font></td>
    </tr>
    <tr align='center'>
    <td width='9%'> <font color='#6098F8'>$l_cmb_planet</font></td>
    <td width='12%'><font color='#6098F8'><strong>" . $planetbeams . "</strong></font></td>
    <td width='17%'><font color='#6098F8'><strong>" . $planetfighters . "</strong></font></td>
    <td width='18%'><font color='#6098F8'><strong>" . $planetshields . "</strong></font></td>
    <td width='11%'><font color='#6098F8'><strong>" . $planettorps . "</strong></font></td>
    <td width='22%'><font color='#6098F8'><strong>" . $planettorpdamage . "</strong></font></td>
    <td width='11%'><font color='#6098F8'><strong>N/A</strong></font></td>
    </tr>
    </table>
    <hr>
    </center>
    ";

    // Begin actual combat calculations

    $planetdestroyed   = 0;
    $attackerdestroyed = 0;

    echo "<br><center><strong><font size='+2'>" . $l_cmb_combatflow . "</font></strong><br><br>\n";
    echo "<table width='75%' border='0'><tr align='center'><td><font color='red'>" . $l_cmb_you . "</font></td><td><font color='#6098F8'>" . $l_cmb_defender . "</font></td>\n";
    echo "<tr align='center'><td><font color='red'><strong>" . $l_cmb_attackingplanet . " " . $playerinfo['sector'] . "</strong></font></td><td></td>";
    echo "<tr align='center'><td><font color='red'><strong>" . $l_cmb_youfireyourbeams . "</strong></font></td><td></td>\n";
    if ($planetfighters > 0 && $attackerbeams > 0)
    {
        if ($attackerbeams > $planetfighters)
        {
            $l_cmb_defenselost = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_defenselost);
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>" . $l_cmb_defenselost . "</strong></font>";
            $attackerbeams = $attackerbeams - $planetfighters;
            $planetfighters = 0;
        }
        else
        {
            $l_cmb_defenselost2 = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_defenselost2);
            $planetfighters = $planetfighters - $attackerbeams;
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>" . $l_cmb_defenselost2 . "</strong></font>";
            $attackerbeams = 0;
        }
    }

    if ($attackerfighters > 0 && $planetbeams > 0)
    {
        // If there are more beams on the planet than attacker has fighters
        if ($planetbeams > round ($attackerfighters / 2))
        {
            // Half the attacker fighters
            $temp = round ($attackerfighters / 2);
            // Attacker loses half his fighters
            $lost = $attackerfighters - $temp;
            // Set attacker fighters to 1/2 it's original value
            $attackerfighters = $temp;
            // Subtract half the attacker fighters from available planetary beams
            $planetbeams = $planetbeams - $lost;
            $l_cmb_planetarybeams = str_replace("[cmb_temp]", $temp, $l_cmb_planetarybeams);
            echo "<tr align='center'><td><font color='red'><strong>" . $l_cmb_planetarybeams  . "</strong></font><td></td>";
        }
        else
        {
            $l_cmb_planetarybeams2 = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_planetarybeams2);
            $attackerfighters = $attackerfighters - $planetbeams;
            echo "<tr align='center'><td><font color='red'><strong>" . $l_cmb_planetarybeams2 . "</strong></font><td></td>";
            $planetbeams = 0;
        }
    }
    if ($attackerbeams > 0)
    {
        if ($attackerbeams > $planetshields)
        {
            $attackerbeams = $attackerbeams - $planetshields;
            $planetshields = 0;
            echo "<tr align='center'><td><font color='red'><strong>" . $l_cmb_youdestroyedplanetshields . "</font></strong><td></td>";
        }
        else
        {
            $l_cmb_beamsexhausted = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_beamsexhausted);
            echo "<tr align='center'><td><font color='red'><strong>" . $l_cmb_beamsexhausted . "</font></strong><td></td>";
            $planetshields = $planetshields - $attackerbeams;
            $attackerbeams = 0;
        }
    }
    if ($planetbeams > 0)
    {
        if ($planetbeams > $attackershields)
        {
            $planetbeams = $planetbeams - $attackershields;
            $attackershields = 0;
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_breachedyourshields</font></strong></td>";
        }
        else
        {
            $attackershields = $attackershields - $planetbeams;
            $l_cmb_destroyedyourshields = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_destroyedyourshields);
            echo "<tr align='center'><td></td><font color='#6098F8'><strong>$l_cmb_destroyedyourshields</font></strong></td>";
            $planetbeams = 0;
        }
    }
    if ($planetbeams > 0)
    {
        if ($planetbeams > $attackerarmor)
        {
            $attackerarmor = 0;
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_breachedyourarmor</strong></font></td>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $planetbeams;
            $l_cmb_destroyedyourarmor = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_destroyedyourarmor);
            echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>$l_cmb_destroyedyourarmor</font></strong></td>";
        }
    }
    echo "<tr align='center'><td><font color='YELLOW'><strong>$l_cmb_torpedoexchangephase</strong></font></td><td><strong><font color='YELLOW'>$l_cmb_torpedoexchangephase</strong></font></td><br>";
    if ($planetfighters > 0 && $attackertorpdamage > 0)
    {
        if ($attackertorpdamage > $planetfighters)
        {
            $l_cmb_nofightersleft = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_nofightersleft);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_nofightersleft</font></strong></td><td></td>";
            $attackertorpdamage = $attackertorpdamage - $planetfighters;
            $planetfighters = 0;
        }
        else
        {
            $planetfighters = $planetfighters - $attackertorpdamage;
            $l_cmb_youdestroyfighters = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_youdestroyfighters);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyfighters</font></strong></td><td></td>";
            $attackertorpdamage = 0;
        }
    }
    if ($attackerfighters > 0 && $planettorpdamage > 0)
    {
        if ($planettorpdamage > round ($attackerfighters / 2))
        {
            $temp = round ($attackerfighters / 2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $planettorpdamage = $planettorpdamage - $lost;
            $l_cmb_planettorpsdestroy = str_replace("[cmb_temp]", $temp, $l_cmb_planettorpsdestroy);
            echo "<tr align='center'><td></td><td><font color='red'><strong>$l_cmb_planettorpsdestroy</strong></font></td>";
        }
        else
        {
            $attackerfighters = $attackerfighters - $planettorpdamage;
            $l_cmb_planettorpsdestroy2 = str_replace("[cmb_planettorpdamage]", $planettorpdamage, $l_cmb_planettorpsdestroy2);
            echo "<tr align='center'><td></td><td><font color='red'><strong>$l_cmb_planettorpsdestroy2</strong></font></td>";
            $planettorpdamage = 0;
        }
    }
    if ($planettorpdamage > 0)
    {
        if ($planettorpdamage > $attackerarmor)
        {
            $attackerarmor = 0;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_torpsbreachedyourarmor</strong></font></td><td></td>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $planettorpdamage;
            $l_cmb_planettorpsdestroy3 = str_replace("[cmb_planettorpdamage]", $planettorpdamage, $l_cmb_planettorpsdestroy3);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_planettorpsdestroy3</strong></font></td><td></td>";
        }
    }
    if ($attackertorpdamage > 0 && $planetfighters > 0)
    {
        $planetfighters = $planetfighters - $attackertorpdamage;
        if ($planetfighters < 0)
        {
            $planetfighters = 0;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyedallfighters</strong></font></td><td></td>";
        }
        else
        {
            $l_cmb_youdestroyplanetfighters = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_youdestroyplanetfighters);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyplanetfighters</strong></font></td><td></td>";
        }
    }
    echo "<tr align='center'><td><font color='YELLOW'><strong>$l_cmb_fightercombatphase</strong></font></td><td><strong><font color='YELLOW'>$l_cmb_fightercombatphase</strong></font></td><br>";
    if ($attackerfighters > 0 && $planetfighters > 0)
    {
        if ($attackerfighters > $planetfighters)
        {
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyedallfighters2</strong></font></td><td></td>";
            $tempplanetfighters = 0;
        }
        else
        {
            $l_cmb_youdestroyplanetfighters2 = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_youdestroyplanetfighters2);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youdestroyplanetfighters2</strong></font></td><td></td>";
            $tempplanetfighters = $planetfighters - $attackerfighters;
        }
        if ($planetfighters > $attackerfighters)
        {
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_allyourfightersdestroyed</strong></font></td><td></td>";
            $tempplayfighters = 0;
        }
        else
        {
            $tempplayfighters = $attackerfighters - $planetfighters;
            $l_cmb_fightertofighterlost = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_fightertofighterlost);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_fightertofighterlost</strong></font></td><td></td>";
        }
        $attackerfighters = $tempplayfighters;
        $planetfighters = $tempplanetfighters;
    }
    if ($attackerfighters > 0 && $planetshields > 0)
    {
        if ($attackerfighters > $planetshields)
        {
            $attackerfighters = $attackerfighters - round ($planetshields / 2);
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_youbreachedplanetshields</strong></font></td><td></td>";
            $planetshields = 0;
        }
        else
        {
            $l_cmb_shieldsremainup = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_shieldsremainup);
            echo "<tr align='center'><td></td><font color='#6098F8'><strong>$l_cmb_shieldsremainup</strong></font></td>";
            $planetshields = $planetshields - $attackerfighters;
        }
    }
    if ($planetfighters > 0)
    {
        if ($planetfighters > $attackerarmor)
        {
            $attackerarmor = 0;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_fighterswarm</strong></font></td><td></td>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $planetfighters;
            echo "<tr align='center'><td><font color='red'><strong>$l_cmb_swarmandrepel</strong></font></td><td></td>";
        }
    }

    echo "</table></center>\n";
    // Send each docked ship in sequence to attack agressor
    $result4 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE planet_id=? AND on_planet='Y'", array ($planetinfo['planet_id']));
    \bnt\dbop::dbresult ($db, $result4, __LINE__, __FILE__);
    $shipsonplanet = $result4->RecordCount();

    if ($shipsonplanet > 0)
    {
        $l_cmb_shipdock = str_replace("[cmb_shipsonplanet]", $shipsonplanet, $l_cmb_shipdock);
        echo "<br><br><center>$l_cmb_shipdock<br>$l_cmb_engshiptoshipcombat</center><br><br>\n";
        while (!$result4->EOF)
        {
            $onplanet = $result4->fields;

            if ($attackerfighters < 0)
            {
                $attackerfighters = 0;
            }

            if ($attackertorps    < 0)
            {
                $attackertorps = 0;
            }

            if ($attackershields  < 0)
            {
                $attackershields = 0;
            }

            if ($attackerbeams    < 0)
            {
                $attackerbeams = 0;
            }

            if ($attackerarmor    < 1)
            {
                break;
            }

            echo "<br>-$onplanet[ship_name] $l_cmb_approachattackvector-<br>";
            ship_to_ship ($db, $onplanet['ship_id']);
            $result4->MoveNext();
        }
    }
    else
        echo "<br><br><center>$l_cmb_noshipsdocked</center><br><br>\n";

    if ($attackerarmor < 1)
    {
        $free_ore = round ($playerinfo['ship_ore'] / 2 );
        $free_organics = round ($playerinfo['ship_organics'] / 2 );
        $free_goods = round ($playerinfo['ship_goods'] / 2 );
        $ship_value = $upgrade_cost * (round (pow ($upgrade_factor, $playerinfo['hull'])) + round (pow ($upgrade_factor, $playerinfo['engines'])) + round (pow ($upgrade_factor, $playerinfo['power'])) + round (pow ($upgrade_factor, $playerinfo['computer'])) + round (pow ($upgrade_factor, $playerinfo['sensors'])) + round (pow ($upgrade_factor, $playerinfo['beams'])) + round (pow ($upgrade_factor, $playerinfo['torp_launchers'])) + round (pow ($upgrade_factor, $playerinfo['shields'])) + round (pow ($upgrade_factor, $playerinfo['armor'])) + round (pow ($upgrade_factor, $playerinfo['cloak'])));
        $ship_salvage_rate = mt_rand (0,10);
        $ship_salvage = $ship_value * $ship_salvage_rate / 100;
        echo "<br><center><font size='+2' COLOR='red'><strong>$l_cmb_yourshipdestroyed</font></strong></center><br>";
        if ($playerinfo['dev_escapepod'] == "Y")
        {
            echo "<center><font color='white'>$l_cmb_escapepod</font></center><br><br>";
            $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=?,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',dev_lssd='N' WHERE ship_id=?", array ($start_energy, $playerinfo['ship_id']));
            \bnt\dbop::dbresult ($db, $resx, __LINE__, __FILE__);
            collect_bounty ($db, $planetinfo['owner'], $playerinfo['ship_id']);
        }
        else
        {
            \bnt\bntplayer::kill ($db, $playerinfo['ship_id'], false, $langvars);
            collect_bounty ($db, $planetinfo['owner'], $playerinfo['ship_id']);
        }
    }
    else
    {
        $free_ore = 0;
        $free_goods = 0;
        $free_organics = 0;
        $ship_salvage_rate = 0;
        $ship_salvage = 0;
        $planetrating = $ownerinfo['hull'] + $ownerinfo['engines'] + $ownerinfo['computer'] + $ownerinfo['beams'] + $ownerinfo['torp_launchers'] + $ownerinfo['shields'] + $ownerinfo['armor'];
        if ($ownerinfo['rating'] != 0 )
        {
            $rating_change = ($ownerinfo['rating'] / abs ($ownerinfo['rating'])) * $planetrating * 10;
        }
        else
        {
            $rating_change=-100;
        }
        echo "<center><br><strong><font size='+2'>$l_cmb_finalcombatstats</font></strong><br><br>";
        $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
        $l_cmb_youlostfighters = str_replace("[cmb_fighters_lost]", $fighters_lost, $l_cmb_youlostfighters);
        $l_cmb_youlostfighters = str_replace("[cmb_playerinfo_ship_fighters]", $playerinfo['ship_fighters'], $l_cmb_youlostfighters);
        echo "$l_cmb_youlostfighters<br>";
        $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
        $l_cmb_youlostarmorpoints = str_replace("[cmb_armor_lost]", $armor_lost, $l_cmb_youlostarmorpoints);
        $l_cmb_youlostarmorpoints = str_replace("[cmb_playerinfo_armor_pts]", $playerinfo['armor_pts'], $l_cmb_youlostarmorpoints);
        $l_cmb_youlostarmorpoints = str_replace("[cmb_attackerarmor]", $attackerarmor, $l_cmb_youlostarmorpoints);
        echo "$l_cmb_youlostarmorpoints<br>";
        $energy = $playerinfo['ship_energy'];
        $energy_lost = $start_energy - $playerinfo['ship_energy'];
        $l_cmb_energyused = str_replace("[cmb_energy_lost]", $energy_lost, $l_cmb_energyused);
        $l_cmb_energyused = str_replace("[cmb_playerinfo_ship_energy]", $start_energy, $l_cmb_energyused);
        echo "$l_cmb_energyused<br></center>";
        $resx = $db->Execute("UPDATE {$db->prefix}ships SET ship_energy=?,ship_fighters=ship_fighters-?, torps=torps-?,armor_pts=armor_pts-?, rating=rating-? WHERE ship_id=?", array ($energy, $fighters_lost, $attackertorps, $armor_lost, $rating_change, $playerinfo['ship_id']));
        \bnt\dbop::dbresult ($db, $resx, __LINE__, __FILE__);
    }

    $result4 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE planet_id=? AND on_planet='Y'", array ($planetinfo['planet_id']));
    \bnt\dbop::dbresult ($db, $result4, __LINE__, __FILE__);
    $shipsonplanet = $result4->RecordCount();

    if ($planetshields < 1 && $planetfighters < 1 && $attackerarmor > 0 && $shipsonplanet == 0)
    {
        echo "<br><br><center><font color='GREEN'><strong>$l_cmb_planetdefeated</strong></font></center><br><br>";

        // Patch to stop players dumping credits for other players.
        include_once './includes/calc_avg_tech.php';
        $self_tech = calc_avg_tech ($playerinfo);
        $target_tech = round (calc_avg_tech ($ownerinfo));

        $roll = mt_rand (0, (integer) $target_tech);
        if ($roll > $self_tech)
        {
            // Reset Planet Assets.
            $sql  = "UPDATE {$db->prefix}planets ";
            $sql .= "SET organics = '0', ore = '0', goods = '0', energy = '0', colonists = '2', credits = '0', fighters = '0', torps = '0', corp = '0', base = 'N', sells = 'N', prod_organics = '20', prod_ore = '20', prod_goods = '20', prod_energy = '20', prod_fighters = '10', prod_torp = '10' ";
            $sql .= "WHERE planet_id = ? LIMIT 1;";
            $resx = $db->Execute($sql, array ($planetinfo['planet_id']));
            \bnt\dbop::dbresult ($db, $resx, __LINE__, __FILE__);
            echo "<div style='text-align:center; font-size:18px; color:#f00;'>The planet become unstable due to not being looked after, and all life and assets have been destroyed.</div>\n";
        }

        if ($min_value_capture != 0)
        {
            $playerscore = calc_score ($db, $playerinfo['ship_id']);
            $playerscore *= $playerscore;

            $planetscore = $planetinfo['organics'] * $organics_price + $planetinfo['ore'] * $ore_price + $planetinfo['goods'] * $goods_price + $planetinfo['energy'] * $energy_price + $planetinfo['fighters'] * $fighter_price + $planetinfo['torps'] * $torpedo_price + $planetinfo['colonists'] * $colonist_price + $planetinfo['credits'];
            $planetscore = $planetscore * $min_value_capture / 100;

            if ($playerscore < $planetscore)
            {
                echo "<center>$l_cmb_citizenswanttodie</center><br><br>";
                $resx = $db->Execute("DELETE FROM {$db->prefix}planets WHERE planet_id=?", array ($planetinfo['planet_id']));
                \bnt\dbop::dbresult ($db, $resx, __LINE__, __FILE__);
                \bnt\PlayerLog::writeLog ($db, $ownerinfo['ship_id'], LOG_PLANET_DEFEATED_D, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
                \bnt\adminLog::writeLog ($db, LOG_ADMIN_PLANETDEL, "$playerinfo[character_name]|$ownerinfo[character_name]|$playerinfo[sector]");
                calc_score ($db, $ownerinfo['ship_id']);
            }
            else
            {
                $l_cmb_youmaycapture = str_replace("[capture]", "<a href='planet.php?planet_id=". $planetinfo['planet_id'] ."&amp;command=capture'>{$l_planet_capture1}</a>", $l_cmb_youmaycapture);
                echo "<center><font color=red>$l_cmb_youmaycapture</font></center><br><br>";
                \bnt\PlayerLog::writeLog ($db, $ownerinfo['ship_id'], LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
                calc_score ($db, $ownerinfo['ship_id']);
                $update7a = $db->Execute("UPDATE {$db->prefix}planets SET owner=0, fighters=0, torps=torps-?, base='N', defeated='Y' WHERE planet_id=?", array ($planettorps, $planetinfo['planet_id']));
                \bnt\dbop::dbresult ($db, $update7a, __LINE__, __FILE__);
            }
        }
        else
        {
            $l_cmb_youmaycapture = str_replace("[capture]", "<a href='planet.php?planet_id=". $planetinfo['planet_id'] ."&amp;command=capture'>{$l_planet_capture1}</a>", $l_cmb_youmaycapture);
            echo "<center>$l_cmb_youmaycapture</center><br><br>";
            \bnt\PlayerLog::writeLog ($db, $ownerinfo['ship_id'], LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
            calc_score ($db, $ownerinfo['ship_id']);
            $update7a = $db->Execute("UPDATE {$db->prefix}planets SET owner=0,fighters=0, torps=torps-?, base='N', defeated='Y' WHERE planet_id=?", array ($planettorps, $planetinfo['planet_id']));
            \bnt\dbop::dbresult ($db, $update7a, __LINE__, __FILE__);
        }

        \bnt\bntownership::calc ($db, $planetinfo['sector_id'], $min_bases_to_own, $langvars);
    }
    else
    {
        echo "<br><br><center><font color='#6098F8'><strong>$l_cmb_planetnotdefeated</strong></font></center><br><br>";
        $fighters_lost = $planetinfo['fighters'] - $planetfighters;
        $l_cmb_fighterloststat = str_replace("[cmb_fighters_lost]", $fighters_lost, $l_cmb_fighterloststat);
        $l_cmb_fighterloststat = str_replace("[cmb_planetinfo_fighters]", $planetinfo['fighters'], $l_cmb_fighterloststat);
        $l_cmb_fighterloststat = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_fighterloststat);
        $energy = $planetinfo['energy'];
        \bnt\PlayerLog::writeLog ($db, $ownerinfo['ship_id'], LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]|$free_ore|$free_organics|$free_goods|$ship_salvage_rate|$ship_salvage");
        calc_score ($db, $ownerinfo['ship_id']);
        $update7b = $db->Execute("UPDATE {$db->prefix}planets SET energy=?,fighters=fighters-?, torps=torps-?, ore=ore+?, goods=goods+?, organics=organics+?, credits=credits+? WHERE planet_id=?", array ($energy, $fighters_lost, $planettorps, $free_ore, $free_goods, $free_organics, $ship_salvage, $planetinfo['planet_id']));
        \bnt\dbop::dbresult ($db, $update7b, __LINE__, __FILE__);
    }
    $update = $db->Execute("UPDATE {$db->prefix}ships SET turns=turns-1, turns_used=turns_used+1 WHERE ship_id=?", array ($playerinfo['ship_id']));
    \bnt\dbop::dbresult ($db, $update, __LINE__, __FILE__);
}
?>
