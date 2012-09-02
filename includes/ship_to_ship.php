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
// File: includes/ship_to_ship.php

if (strpos ($_SERVER['PHP_SELF'], 'ship_to_ship.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

function ship_to_ship ($db, $ship_id)
{
    global $attackerbeams, $attackerfighters, $attackershields, $attackertorps, $attackerarmor, $attackertorpdamage, $start_energy, $level_factor;
    global $torp_dmg_rate, $rating_combat_factor, $upgrade_factor, $upgrade_cost, $armor_lost, $fighters_lost, $playerinfo;
    global $l_cmb_startingstats, $l_cmb_statattackerbeams, $l_cmb_statattackerfighters, $l_cmb_statattackershields, $l_cmb_statattackertorps;
    global $l_cmb_statattackerarmor, $l_cmb_statattackertorpdamage, $l_cmb_isattackingyou, $l_cmb_beamexchange, $l_cmb_beamsdestroy;
    global $l_cmb_beamsdestroy2, $l_cmb_nobeamsareleft, $l_cmb_beamshavenotarget, $l_cmb_fighterdestroyedbybeams, $l_cmb_beamsdestroystillhave;
    global $l_cmb_fighterunhindered, $l_cmb_youhavenofightersleft, $l_cmb_breachedsomeshields, $l_cmb_shieldsarehitbybeams, $l_cmb_nobeamslefttoattack;
    global $l_cmb_yourshieldsbreachedby, $l_cmb_yourshieldsarehit, $l_cmb_hehasnobeamslefttoattack, $l_cmb_yourbeamsbreachedhim;
    global $l_cmb_yourbeamshavedonedamage, $l_cmb_nobeamstoattackarmor, $l_cmb_yourarmorbreachedbybeams, $l_cmb_yourarmorhitdamaged;
    global $l_cmb_torpedoexchange, $l_cmb_hehasnobeamslefttoattackyou, $l_cmb_yourtorpsdestroy, $l_cmb_yourtorpsdestroy2;
    global $l_cmb_youhavenotorpsleft, $l_cmb_hehasnofighterleft, $l_cmb_torpsdestroyyou, $l_cmb_someonedestroyedfighters, $l_cmb_hehasnotorpsleftforyou;
    global $l_cmb_youhavenofightersanymore, $l_cmb_youbreachedwithtorps, $l_cmb_hisarmorishitbytorps, $l_cmb_notorpslefttoattackarmor;
    global $l_cmb_yourarmorbreachedbytorps, $l_cmb_yourarmorhitdmgtorps, $l_cmb_hehasnotorpsforyourarmor, $l_cmb_fightersattackexchange;
    global $l_cmb_enemylostallfighters, $l_cmb_helostsomefighters, $l_cmb_youlostallfighters, $l_cmb_youalsolostsomefighters, $l_cmb_hehasnofightersleftattack;
    global $l_cmb_younofightersattackleft, $l_cmb_youbreachedarmorwithfighters, $l_cmb_youhitarmordmgfighters, $l_cmb_youhavenofighterstoarmor;
    global $l_cmb_hasbreachedarmorfighters, $l_cmb_yourarmorishitfordmgby, $l_cmb_nofightersleftheforyourarmor, $l_cmb_hehasbeendestroyed;
    global $l_cmb_escapepodlaunched, $l_cmb_yousalvaged, $l_cmb_yousalvaged2, $l_cmb_youdidntdestroyhim, $l_cmb_shiptoshipcombatstats;

    include_once './includes/collect_bounty.php';

    $resx = $db->Execute("LOCK TABLES {$db->prefix}ships WRITE, {$db->prefix}universe WRITE, {$db->prefix}zones READ");
    db_op_result ($db, $resx, __LINE__, __FILE__);

    $result2 = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array ($ship_id));
    db_op_result ($db, $result2, __LINE__, __FILE__);
    $targetinfo = $result2->fields;

    echo "<br><br>-=-=-=-=-=-=-=--<br>
    $l_cmb_startingstats:<br>
    <br>
    $l_cmb_statattackerbeams: $attackerbeams<br>
    $l_cmb_statattackerfighters: $attackerfighters<br>
    $l_cmb_statattackershields: $attackershields<br>
    $l_cmb_statattackertorps: $attackertorps<br>
    $l_cmb_statattackerarmor: $attackerarmor<br>
    $l_cmb_statattackertorpdamage: $attackertorpdamage<br>";

    $targetbeams = NUM_BEAMS ($targetinfo['beams']);
    if ($targetbeams > $targetinfo['ship_energy'])
    {
        $targetbeams = $targetinfo['ship_energy'];
    }
    $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetbeams;
    $targetshields = NUM_SHIELDS ($targetinfo['shields']);
    if ($targetshields > $targetinfo['ship_energy'])
    {
        $targetshields = $targetinfo['ship_energy'];
    }
    $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetshields;

    $targettorpnum = round (pow ($level_factor, $targetinfo['torp_launchers'])) * 2;
    if ($targettorpnum > $targetinfo['torps'])
    {
        $targettorpnum = $targetinfo['torps'];
    }
    $targettorpdmg = $torp_dmg_rate * $targettorpnum;
    $targetarmor = $targetinfo['armor_pts'];
    $targetfighters = $targetinfo['ship_fighters'];
    $targetdestroyed = 0;
    $playerdestroyed = 0;
    echo "-->$targetinfo[ship_name] $l_cmb_isattackingyou<br><br>";
    echo "$l_cmb_beamexchange<br>";
    if ($targetfighters > 0 && $attackerbeams > 0)
    {
        if ($attackerbeams > round ($targetfighters / 2))
        {
            $temp = round ($targetfighters/2);
            $lost = $targetfighters-$temp;
            $targetfighters = $temp;
            $attackerbeams = $attackerbeams-$lost;
            $l_cmb_beamsdestroy = str_replace ("[cmb_lost]", $lost, $l_cmb_beamsdestroy);
            echo "<-- $l_cmb_beamsdestroy<br>";
        }
        else
        {
            $targetfighters = $targetfighters-$attackerbeams;
            $l_cmb_beamsdestroy2 = str_replace ("[cmb_attackerbeams]", $attackerbeams, $l_cmb_beamsdestroy2);
            echo "--> $l_cmb_beamsdestroy2<br>";
            $attackerbeams = 0;
        }
    }
    elseif ($targetfighters > 0 && $attackerbeams < 1)
    echo "$l_cmb_nobeamsareleft<br>";
    else
        echo "$l_cmb_beamshavenotarget<br>";
    if ($attackerfighters > 0 && $targetbeams > 0)
    {
        if ($targetbeams > round ($attackerfighters / 2))
        {
            $temp=round ($attackerfighters/2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $targetbeams = $targetbeams - $lost;
            $l_cmb_fighterdestroyedbybeams = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_fighterdestroyedbybeams);
            $l_cmb_fighterdestroyedbybeams = str_replace ("[cmb_lost]", $lost, $l_cmb_fighterdestroyedbybeams);
            echo "--> $l_cmb_fighterdestroyedbybeams<br>";
        }
        else
        {
            $attackerfighters = $attackerfighters - $targetbeams;
            $l_cmb_beamsdestroystillhave = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_beamsdestroystillhave);
            $l_cmb_beamsdestroystillhave = str_replace ("[cmb_targetbeams]", $targetbeams, $l_cmb_beamsdestroystillhave);
            $l_cmb_beamsdestroystillhave = str_replace ("[cmb_attackerfighters]", $attackerfighters, $l_cmb_beamsdestroystillhave);
            echo "<-- $l_cmb_beamsdestroystillhave<br>";
            $targetbeams = 0;
        }
    }
    elseif ($attackerfighters > 0 && $targetbeams < 1)
    {
        echo "$l_cmb_fighterunhindered<br>";
    }
    else
    {
        echo "$l_cmb_youhavenofightersleft<br>";
    }

    if ($attackerbeams > 0)
    {
        if ($attackerbeams > $targetshields)
        {
            $attackerbeams = $attackerbeams - $targetshields;
            $targetshields = 0;
            $l_cmb_breachedsomeshields = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_breachedsomeshields);
            echo "<-- $l_cmb_breachedsomeshields<br>";
        }
        else
        {
            $l_cmb_shieldsarehitbybeams = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_shieldsarehitbybeams);
            $l_cmb_shieldsarehitbybeams = str_replace ("[cmb_attackerbeams]", $attackerbeams, $l_cmb_shieldsarehitbybeams);
            echo "$l_cmb_shieldsarehitbybeams<br>";
            $targetshields = $targetshields - $attackerbeams;
            $attackerbeams = 0;
        }
    }
    else
    {
        $l_cmb_nobeamslefttoattack = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_nobeamslefttoattack);
        echo "$l_cmb_nobeamslefttoattack<br>";
    }
    if ($targetbeams > 0)
    {
        if ($targetbeams > $attackershields)
        {
            $targetbeams = $targetbeams - $attackershields;
            $attackershields = 0;
            $l_cmb_yourshieldsbreachedby = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourshieldsbreachedby);
            echo "--> $l_cmb_yourshieldsbreachedby<br>";
        }
        else
        {
            $l_cmb_yourshieldsarehit = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourshieldsarehit);
            $l_cmb_yourshieldsarehit = str_replace ("[cmb_targetbeams]", $targetbeams, $l_cmb_yourshieldsarehit);
            echo "<-- $l_cmb_yourshieldsarehit<br>";
            $attackershields = $attackershields - $targetbeams;
            $targetbeams = 0;
        }
    }
    else
    {
        $l_cmb_hehasnobeamslefttoattack = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnobeamslefttoattack);
        echo "$l_cmb_hehasnobeamslefttoattack<br>";
    }
    if ($attackerbeams > 0)
    {
        if ($attackerbeams > $targetarmor)
        {
            $targetarmor=0;
            $l_cmb_yourbeamsbreachedhim = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourbeamsbreachedhim);
            echo "--> $l_cmb_yourbeamsbreachedhim<br>";
        }
        else
        {
            $targetarmor=$targetarmor-$attackerbeams;
            $l_cmb_yourbeamshavedonedamage = str_replace ("[cmb_attackerbeams]", $attackerbeams, $l_cmb_yourbeamshavedonedamage);
            $l_cmb_yourbeamshavedonedamage = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourbeamshavedonedamage);
            echo "$l_cmb_yourbeamshavedonedamage<br>";
        }
    }
    else
    {
        $l_cmb_nobeamstoattackarmor = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_nobeamstoattackarmor);
        echo "$l_cmb_nobeamstoattackarmor<br>";
    }
    if ($targetbeams > 0)
    {
        if ($targetbeams > $attackerarmor)
        {
            $attackerarmor = 0;
            $l_cmb_yourarmorbreachedbybeams = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorbreachedbybeams);
            echo "--> $l_cmb_yourarmorbreachedbybeams<br>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $targetbeams;
            $l_cmb_yourarmorhitdamaged = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorhitdamaged);
            $l_cmb_yourarmorhitdamaged = str_replace ("[cmb_targetbeams]", $targetbeams, $l_cmb_yourarmorhitdamaged);
            echo "<-- $l_cmb_yourarmorhitdamaged<br>";
        }
    }
    else
    {
        $l_cmb_hehasnobeamslefttoattackyou = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnobeamslefttoattackyou);
        echo "$l_cmb_hehasnobeamslefttoattackyou<br>";
    }
    echo "<br>$l_cmb_torpedoexchange<br>";
    if ($targetfighters > 0 && $attackertorpdamage > 0)
    {
        if ($attackertorpdamage > round ($targetfighters / 2))
        {
            $temp=round ($targetfighters / 2);
            $lost=$targetfighters - $temp;
            $targetfighters = $temp;
            $attackertorpdamage = $attackertorpdamage - $lost;
            $l_cmb_yourtorpsdestroy = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourtorpsdestroy);
            $l_cmb_yourtorpsdestroy = str_replace ("[cmb_lost]", $lost, $l_cmb_yourtorpsdestroy);
            echo "--> $l_cmb_yourtorpsdestroy<br>";
        }
        else
        {
            $targetfighters = $targetfighters - $attackertorpdamage;
            $l_cmb_yourtorpsdestroy2 = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourtorpsdestroy2);
            $l_cmb_yourtorpsdestroy2 = str_replace ("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_yourtorpsdestroy2);
            echo "<-- $l_cmb_yourtorpsdestroy2<br>";
            $attackertorpdamage = 0;
        }
    }
    elseif ($targetfighters > 0 && $attackertorpdamage < 1)
    {
        $l_cmb_youhavenotorpsleft = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youhavenotorpsleft);
        echo "$l_cmb_youhavenotorpsleft<br>";
    }
    else
    {
        $l_cmb_hehasnofighterleft = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnofighterleft);
        echo "$l_cmb_hehasnofighterleft<br>";
    }
    if ($attackerfighters > 0 && $targettorpdmg > 0)
    {
        if ($targettorpdmg > round ($attackerfighters / 2))
        {
            $temp = round ($attackerfighters / 2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $targettorpdmg = $targettorpdmg - $lost;
            $l_cmb_torpsdestroyyou = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_torpsdestroyyou);
            $l_cmb_torpsdestroyyou = str_replace ("[cmb_lost]", $lost, $l_cmb_torpsdestroyyou);
            echo "--> $l_cmb_torpsdestroyyou<br>";
        }
        else
        {
            $attackerfighters = $attackerfighters - $targettorpdmg;
            $l_cmb_someonedestroyedfighters = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_someonedestroyedfighters);
            $l_cmb_someonedestroyedfighters = str_replace ("[cmb_targettorpdmg]", $targettorpdmg, $l_cmb_someonedestroyedfighters);
            echo "<-- $l_cmb_someonedestroyedfighters<br>";
            $targettorpdmg=0;
        }
    }
    elseif ($attackerfighters > 0 && $targettorpdmg < 1)
    {
        $l_cmb_hehasnotorpsleftforyou = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnotorpsleftforyou);
        echo "$l_cmb_hehasnotorpsleftforyou<br>";
    }
    else
    {
        $l_cmb_youhavenofightersanymore = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youhavenofightersanymore);
        echo "$l_cmb_youhavenofightersanymore<br>";
    }
    if ($attackertorpdamage > 0)
    {
        if ($attackertorpdamage > $targetarmor)
        {
            $targetarmor = 0;
            $l_cmb_youbreachedwithtorps = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youbreachedwithtorps);
            echo "--> $l_cmb_youbreachedwithtorps<br>";
        }
        else
        {
            $targetarmor=$targetarmor-$attackertorpdamage;
            $l_cmb_hisarmorishitbytorps = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hisarmorishitbytorps);
            $l_cmb_hisarmorishitbytorps = str_replace ("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_hisarmorishitbytorps);
            echo "<-- $l_cmb_hisarmorishitbytorps<br>";
        }
    }
    else
    {
        $l_cmb_notorpslefttoattackarmor = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_notorpslefttoattackarmor);
        echo "$l_cmb_notorpslefttoattackarmor<br>";
    }
    if ($targettorpdmg > 0)
    {
        if ($targettorpdmg > $attackerarmor)
        {
            $attackerarmor = 0;
            $l_cmb_yourarmorbreachedbytorps = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorbreachedbytorps);
            echo "<-- $l_cmb_yourarmorbreachedbytorps<br>";
        }
        else
        {
            $attackerarmor=$attackerarmor-$targettorpdmg;
            $l_cmb_yourarmorhitdmgtorps = str_replace ("[cmb_targettorpdmg]", $targettorpdmg, $l_cmb_yourarmorhitdmgtorps);
            $l_cmb_yourarmorhitdmgtorps = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorhitdmgtorps);
            echo "<-- $l_cmb_yourarmorhitdmgtorps<br>";
        }
    }
    else
    {
        $l_cmb_hehasnotorpsforyourarmor = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnotorpsforyourarmor);
        echo "$l_cmb_hehasnotorpsforyourarmor<br>";
    }
    echo "<br>$l_cmb_fightersattackexchange<br>";
    if ($attackerfighters > 0 && $targetfighters > 0)
    {
        if ($attackerfighters > $targetfighters)
        {
            $l_cmb_enemylostallfighters = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_enemylostallfighters);
            echo "--> $l_cmb_enemylostallfighters<br>";
            $temptargfighters = 0;
        }
        else
        {
            $l_cmb_helostsomefighters = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_helostsomefighters);
            $l_cmb_helostsomefighters = str_replace ("[cmb_attackerfighters]", $attackerfighters, $l_cmb_helostsomefighters);
            echo "$l_cmb_helostsomefighters<br>";
            $temptargfighters = $targetfighters - $attackerfighters;
        }
        if ($targetfighters > $attackerfighters)
        {
            echo "<-- $l_cmb_youlostallfighters<br>";
            $tempplayfighters = 0;
        }
        else
        {
            $l_cmb_youalsolostsomefighters = str_replace ("[cmb_targetfighters]", $targetfighters, $l_cmb_youalsolostsomefighters);
            echo "<-- $l_cmb_youalsolostsomefighters<br>";
            $tempplayfighters = $attackerfighters - $targetfighters;
        }
        $attackerfighters = $tempplayfighters;
        $targetfighters = $temptargfighters;
    }
    elseif ($attackerfighters > 0 && $targetfighters < 1)
    {
        $l_cmb_hehasnofightersleftattack = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnofightersleftattack);
        echo "$l_cmb_hehasnofightersleftattack<br>";
    }
    else
    {
        $l_cmb_younofightersattackleft = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_younofightersattackleft);
        echo "$l_cmb_younofightersattackleft<br>";
    }
    if ($attackerfighters > 0)
    {
        if ($attackerfighters > $targetarmor)
        {
            $targetarmor = 0;
            $l_cmb_youbreachedarmorwithfighters = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youbreachedarmorwithfighters);
            echo "--> $l_cmb_youbreachedarmorwithfighters<br>";
        }
        else
        {
            $targetarmor = $targetarmor - $attackerfighters;
            $l_cmb_youhitarmordmgfighters = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youhitarmordmgfighters);
            $l_cmb_youhitarmordmgfighters = str_replace ("[cmb_attackerfighters]", $attackerfighters, $l_cmb_youhitarmordmgfighters);
            echo "<-- $l_cmb_youhitarmordmgfighters<br>";
        }
    }
    else
    {
        $l_cmb_youhavenofighterstoarmor = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youhavenofighterstoarmor);
        echo "$l_cmb_youhavenofighterstoarmor<br>";
    }
    if ($targetfighters > 0)
    {
        if ($targetfighters > $attackerarmor)
        {
            $attackerarmor = 0;
            $l_cmb_hasbreachedarmorfighters = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hasbreachedarmorfighters);
            echo "<-- $l_cmb_hasbreachedarmorfighters<br>";
        }
        else
        {
            $attackerarmor = $attackerarmor - $targetfighters;
            $l_cmb_yourarmorishitfordmgby = str_replace ("[cmb_targetfighters]", $targetfighters, $l_cmb_yourarmorishitfordmgby);
            $l_cmb_yourarmorishitfordmgby = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorishitfordmgby);
            echo "--> $l_cmb_yourarmorishitfordmgby<br>";
        }
    }
    else
    {
        $l_cmb_nofightersleftheforyourarmor = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_nofightersleftheforyourarmor);
        echo "$l_cmb_nofightersleftheforyourarmor<br>";
    }
    if ($targetarmor < 1)
    {
        $l_cmb_hehasbeendestroyed = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasbeendestroyed);
        echo "<br>$l_cmb_hehasbeendestroyed<br>";
        if ($attackerarmor > 0)
        {
            $rating_change=round ($targetinfo['rating'] * $rating_combat_factor);
            $free_ore = round ($targetinfo['ship_ore'] / 2);
            $free_organics = round ($targetinfo['ship_organics'] / 2);
            $free_goods = round ($targetinfo['ship_goods'] / 2);
            $free_holds = NUM_HOLDS ($playerinfo['hull']) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
            if ($free_holds > $free_goods)
            {
                $salv_goods = $free_goods;
                $free_holds = $free_holds - $free_goods;
            }
            elseif ($free_holds > 0)
            {
                $salv_goods = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_goods = 0;
            }
            if ($free_holds > $free_ore)
            {
                $salv_ore = $free_ore;
                $free_holds = $free_holds - $free_ore;
            }
            elseif ($free_holds > 0)
            {
                $salv_ore = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_ore = 0;
            }
            if ($free_holds > $free_organics)
            {
                $salv_organics = $free_organics;
                $free_holds = $free_holds - $free_organics;
            }
            elseif ($free_holds > 0)
            {
                $salv_organics = $free_holds;
                $free_holds = 0;
            }
            else
            {
                $salv_organics = 0;
            }
            $ship_value = $upgrade_cost * (round (pow ($upgrade_factor, $targetinfo['hull']))+round (pow ($upgrade_factor, $targetinfo['engines']))+round (pow ($upgrade_factor, $targetinfo['power']))+round (pow ($upgrade_factor, $targetinfo['computer']))+round (pow ($upgrade_factor, $targetinfo['sensors']))+round (pow ($upgrade_factor, $targetinfo['beams']))+round (pow ($upgrade_factor, $targetinfo['torp_launchers']))+round (pow ($upgrade_factor, $targetinfo['shields']))+round (pow ($upgrade_factor, $targetinfo['armor']))+round (pow ($upgrade_factor, $targetinfo['cloak'])));
            $ship_salvage_rate = mt_rand (10,20);
            $ship_salvage = $ship_value*$ship_salvage_rate / 100;
            $l_cmb_yousalvaged = str_replace ("[cmb_salv_ore]", $salv_ore, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace ("[cmb_salv_organics]", $salv_organics, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace ("[cmb_salv_goods]", $salv_goods, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace ("[cmb_salvage_rate]", $ship_salvage_rate, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace ("[cmb_salvage]", $ship_salvage, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged2 = str_replace ("[cmb_number_rating_change]", NUMBER (abs($rating_change)), $l_cmb_yousalvaged2);
            echo $l_cmb_yousalvaged . "<br>" . $l_cmb_yousalvaged2;
            $update3 = $db->Execute ("UPDATE {$db->prefix}ships SET ship_ore=ship_ore+?, ship_organics=ship_organics+?, ship_goods=ship_goods+?, credits=credits+? WHERE ship_id=?", array ($salv_ore, $salv_organics, $salv_goods, $ship_salvage, $playerinfo['ship_id']));
            db_op_result ($db, $update3, __LINE__, __FILE__);
        }

        if ($targetinfo['dev_escapepod'] == "Y")
        {
            $rating = round ($targetinfo['rating'] / 2 );
            echo "$l_cmb_escapepodlaunched<br><br>";
            echo "<br><br>ship_id=$targetinfo[ship_id]<br><br>";
            $test = $db->Execute("UPDATE {$db->prefix}ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=?,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating=?,dev_lssd='N' WHERE ship_id=?", array ($start_energy, $rating, $targetinfo['ship_id']));
            db_op_result ($db, $test, __LINE__, __FILE__);
            player_log ($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|Y");
            collect_bounty ($db, $playerinfo['ship_id'], $targetinfo['ship_id']);
        }
        else
        {
            player_log ($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
            db_kill_player ($db, $targetinfo['ship_id']);
            collect_bounty ($db, $playerinfo['ship_id'], $targetinfo['ship_id']);
        }
    }
    else
    {
        $l_cmb_youdidntdestroyhim = str_replace ("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youdidntdestroyhim);
        echo "$l_cmb_youdidntdestroyhim<br>";
        $target_rating_change = round ($targetinfo['rating'] * .1);
        $target_armor_lost = $targetinfo['armor_pts'] - $targetarmor;
        $target_fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
        $target_energy = $targetinfo['ship_energy'];
        player_log ($db, $targetinfo['ship_id'], LOG_ATTACKED_WIN, "$playerinfo[character_name] $armor_lost $fighters_lost");
        $update4 = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy=?,ship_fighters=ship_fighters-?, armor_pts=armor_pts-?, torps=torps-? WHERE ship_id=?", array ($target_energy, $target_fighters_lost, $target_armor_lost, $targettorpnum, $targetinfo['ship_id']));
        db_op_result ($db, $update4, __LINE__, __FILE__);
    }
    echo "<br>_+_+_+_+_+_+_<br>";
    echo "$l_cmb_shiptoshipcombatstats<br>";
    echo "$l_cmb_statattackerbeams: $attackerbeams<br>";
    echo "$l_cmb_statattackerfighters: $attackerfighters<br>";
    echo "$l_cmb_statattackershields: $attackershields<br>";
    echo "$l_cmb_statattackertorps: $attackertorps<br>";
    echo "$l_cmb_statattackerarmor: $attackerarmor<br>";
    echo "$l_cmb_statattackertorpdamage: $attackertorpdamage<br>";
    echo "_+_+_+_+_+_+<br>";
    $resx = $db->Execute("UNLOCK TABLES");
    db_op_result ($db, $resx, __LINE__, __FILE__);
}
?>
