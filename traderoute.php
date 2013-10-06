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
// File: traderoute.php

include './global_includes.php';
include './includes/traderoute_new.php';

BntLogin::checkLogin ($db, $lang, $langvars, $bntreg, $template);

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('traderoutes'));
$title = $langvars['l_tdr_title'];
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer', 'bounty'));
echo "<h1>" . $title . "</h1>\n";

$portfull = null; // This fixes an error of undefined variables on 1518

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
DbOp::dbResult ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$result = $db->Execute ("SELECT * FROM {$db->prefix}traderoutes WHERE owner = ?;", array ($playerinfo['ship_id']));
DbOp::dbResult ($db, $result, __LINE__, __FILE__);
$num_traderoutes = $result->RecordCount();

if (isset ($traderoutes))
{
    BntAdminLog::writeLog ($db, 902, "{$playerinfo['ship_id']}|Tried to insert a hardcoded TradeRoute.");
    traderoute_die ($db, $lang, $langvars, "<div style='color:#fff; font-size: 12px;'>[<span style='color:#ff0;'>The Governor</span>] <span style='color:#f00;'>Detected Traderoute Hack!</span></div>\n");
}

$traderoutes = array ();
$i = 0;
while (!$result->EOF)
{
    $i = array_push ($traderoutes, $result->fields);
    // $traderoutes[$i] = $result->fields;
    // $i++;
    $result->MoveNext ();
}

$freeholds = BntCalcLevels::Holds ($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
$maxholds = BntCalcLevels::Holds ($playerinfo['hull'], $level_factor);
$maxenergy = BntCalcLevels::Energy ($playerinfo['power'], $level_factor);
if ($playerinfo['ship_colonists'] < 0 || $playerinfo['ship_ore'] < 0 || $playerinfo['ship_organics'] < 0 || $playerinfo['ship_goods'] < 0 || $playerinfo['ship_energy'] < 0 || $freeholds < 0)
{
    if ($playerinfo['ship_colonists'] < 0 || $playerinfo['ship_colonists'] > $maxholds)
    {
        BntAdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_colonists]|colonists|$maxholds");
        $playerinfo['ship_colonists'] = 0;
    }
    if ($playerinfo['ship_ore'] < 0 || $playerinfo['ship_ore'] > $maxholds)
    {
        BntAdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_ore]|ore|$maxholds");
        $playerinfo['ship_ore'] = 0;
    }
    if ($playerinfo['ship_organics'] < 0 || $playerinfo['ship_organics'] > $maxholds)
    {
        BntAdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_organics]|organics|$maxholds");
        $playerinfo['ship_organics'] = 0;
    }
    if ($playerinfo['ship_goods'] < 0 || $playerinfo['ship_goods'] > $maxholds)
    {
        BntAdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_goods]|goods|$maxholds");
        $playerinfo['ship_goods'] = 0;
    }
    if ($playerinfo['ship_energy'] < 0 || $playerinfo['ship_energy'] > $maxenergy)
    {
        BntAdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_energy]|energy|$maxenergy");
        $playerinfo['ship_energy'] = 0;
    }
    if ($freeholds < 0)
    {
        $freeholds = 0;
    }

    $update1 = $db->Execute ("UPDATE {$db->prefix}ships SET ship_ore=?, ship_organics=?, ship_goods=?, ship_energy=?, ship_colonists=? WHERE ship_id=?;", array ($playerinfo['ship_ore'], $playerinfo['ship_organics'], $playerinfo['ship_goods'], $playerinfo['ship_energy'], $playerinfo['ship_colonists'], $playerinfo['ship_id']));
    DbOp::dbResult ($db, $update1, __LINE__, __FILE__);
}

// Default to 1 run if we don't get a valid repeat value.
$tr_repeat = 1;
// Check if we have a $_POST['tr_repeat'] and that the type-casted value is larger than 0.
if (array_key_exists ('tr_repeat', $_POST) == true && (integer) $_POST['tr_repeat'] >0)
{
    // Now type cast the repeat value into an integer.
    $tr_repeat = (integer) $_POST['tr_repeat'];
}

$command = null;
if (array_key_exists ('command', $_REQUEST) == true)
{
    $command = $_REQUEST['command'];
}

if ($command == 'new')
{
    // Displays new trade route form
    traderoute_new ($db, $lang, $langvars, null);
}
elseif ($command == 'create')
{
    // Enters new route in db
    traderoute_create ($db, $lang, $langvars);
}
elseif ($command == 'edit')
{
    // Displays new trade route form, edit
    traderoute_new ($db, $lang, $traderoute_id);
}
elseif ($command == 'delete')
{
    // Displays delete info
    traderoute_delete ($db, $langvars);
}
elseif ($command == 'settings')
{
    // Global traderoute settings form
    traderoute_settings ($db, $lang, $langvars);
}
elseif ($command == 'setsettings')
{
    // Enters settings in db
    traderoute_setsettings ($db, $langvars);
}
elseif (isset ($engage) )
{
    // Perform trade route
    $i = $tr_repeat;
    while ($i > 0)
    {
        $result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email=?", array ($_SESSION['username']));
        DbOp::dbResult ($db, $result, __LINE__, __FILE__);
        $playerinfo = $result->fields;
        include_once './includes/traderoute_engage.php';
        traderoute_engage ($db, $lang, $i, $langvars);
        $i--;
    }
}

if ($command != 'delete')
{
    $langvars['l_tdr_newtdr'] = str_replace ("[here]", "<a href='traderoute.php?command=new'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_newtdr']);
    echo "<p>" . $langvars['l_tdr_newtdr'] . "<p>";
    $langvars['l_tdr_modtdrset'] = str_replace ("[here]", "<a href='traderoute.php?command=settings'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_modtdrset']);
    echo "<p>" . $langvars['l_tdr_modtdrset'] . "<p>";
}
else
{
    $langvars['l_tdr_confdel'] = str_replace ("[here]", "<a href='traderoute.php?command=delete&amp;confirm=yes&amp;traderoute_id=" . $traderoute_id . "'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_confdel']);
    echo "<p>" . $langvars['l_tdr_confdel'] . "<p>";
}

if ($num_traderoutes == 0)
{
    echo $langvars['l_tdr_noactive'] . "<p>";
}
else
{
    echo '<table border=1 cellspacing=1 cellpadding=2 width="100%" align="center">' .
         '<tr bgcolor=' . $color_line2 . '><td align="center" colspan=7><strong><font color=white>
         ';

    if ($command != 'delete')
    {
        echo $langvars['l_tdr_curtdr'];
    }
    else
    {
        echo $langvars['l_tdr_deltdr'];
    }

    echo "</font></strong>" .
         "</td></tr>" .
         "<tr align='center' bgcolor=$color_line2>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_src'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_srctype'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_dest'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_desttype'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_move'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_circuit'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_change'] . "</strong></font></td>" .
         "</tr>";

    $i = 0;
    $curcolor = $color_line1;
    while ($i < $num_traderoutes)
    {
        echo "<tr bgcolor=$curcolor>";
        if ($curcolor == $color_line1)
        {
            $curcolor = $color_line2;
        }
        else
        {
            $curcolor = $color_line1;
        }

        echo "<td><font size=2 color=white>";
        if ($traderoutes[$i]['source_type'] == 'P')
        {
            echo "&nbsp;" . $langvars['l_tdr_portin'] . " <a href=rsmove.php?engage=1&destination=" . $traderoutes[$i]['source_id'] . ">" . $traderoutes[$i]['source_id'] . "</a></font></td>";
        }
        else
        {
            $result = $db->Execute ("SELECT name, sector_id FROM {$db->prefix}planets WHERE planet_id=?;", array ($traderoutes[$i]['source_id']));
            DbOp::dbResult ($db, $result, __LINE__, __FILE__);
            if ($result)
            {
                $planet1 = $result->fields;
                echo "&nbsp;" . $langvars['l_tdr_planet'] . " <strong>$planet1[name]</strong>" . $langvars['l_tdr_within'] . "<a href=\"rsmove.php?engage=1&destination=$planet1[sector_id]\">$planet1[sector_id]</a></font></td>";
            }
            else
            {
                echo "&nbsp;" . $langvars['l_tdr_nonexistance'] . "</font></td>";
            }
        }

        echo "<td align='center'><font size=2 color=white>";
        if ($traderoutes[$i]['source_type'] == 'P')
        {
            $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?;", array ($traderoutes[$i]['source_id']));
            DbOp::dbResult ($db, $result, __LINE__, __FILE__);
            $port1 = $result->fields;
            echo "&nbsp;" . BntPorts::getType ($port1['port_type'], $langvars) . "</font></td>";
        }
        else
        {
            if (empty ($planet1))
            {
                echo "&nbsp;" . $langvars['l_tdr_na'] . "</font></td>";
            }
            else
            {
                echo "&nbsp;" . $langvars['l_tdr_cargo'] . "</font></td>";
            }
        }
        echo "<td><font size=2 color=white>";

        if ($traderoutes[$i]['dest_type'] == 'P')
        {
            echo "&nbsp;" . $langvars['l_tdr_portin'] . " <a href=\"rsmove.php?engage=1&destination=" . $traderoutes[$i]['dest_id'] . "\">" . $traderoutes[$i]['dest_id'] . "</a></font></td>";
        }
        else
        {
            $result = $db->Execute ("SELECT name, sector_id FROM {$db->prefix}planets WHERE planet_id=?;", array ($traderoutes[$i]['dest_id']));
            DbOp::dbResult ($db, $result, __LINE__, __FILE__);
            if ($result)
            {
                $planet2 = $result->fields;
                echo "&nbsp;" . $langvars['l_tdr_planet'] . " <strong>$planet2[name]</strong>" . $langvars['l_tdr_within'] . "<a href=\"rsmove.php?engage=1&destination=$planet2[sector_id]\">$planet2[sector_id]</a></font></td>";
            }
            else
            {
                echo "&nbsp;" . $langvars['l_tdr_nonexistance'] . "</font></td>";
            }
        }
        echo "<td align='center'><font size=2 color=white>";

        if ($traderoutes[$i]['dest_type'] == 'P')
        {
            $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?;", array ($traderoutes[$i]['dest_id']));
            DbOp::dbResult ($db, $result, __LINE__, __FILE__);
            $port2 = $result->fields;
            echo "&nbsp;" . BntPorts::getType ($port2['port_type'], $langvars) . "</font></td>";
        }
        else
        {
            if (empty ($planet2))
            {
                echo "&nbsp;" . $langvars['l_tdr_na'] . "</font></td>";
            }
            else
            {
                echo "&nbsp;";
                if ($playerinfo['trade_colonists'] == 'N' && $playerinfo['trade_fighters'] == 'N' && $playerinfo['trade_torps'] == 'N')
                {
                    echo $langvars['l_tdr_none'];
                }
                else
                {
                    if ($playerinfo['trade_colonists'] == 'Y')
                    {
                        echo $langvars['l_tdr_colonists'];
                    }

                    if ($playerinfo['trade_fighters'] == 'Y')
                    {
                        if ($playerinfo['trade_colonists'] == 'Y')
                        {
                            echo ", ";
                        }

                        echo $langvars['l_tdr_fighters'];
                    }
                    if ($playerinfo['trade_torps'] == 'Y')
                    {
                        echo "<br>" . $langvars['l_tdr_torps'];
                    }
                }
                echo "</font></td>";
            }
        }
        echo "<td align='center'><font size=2 color=white>";

        if ($traderoutes[$i]['move_type'] == 'R')
        {
            echo "&nbsp;RS, ";

            if ($traderoutes[$i]['source_type'] == 'P')
            {
                $src = $port1;
            }
            else
            {
                $src = $planet1['sector_id'];
            }

            if ($traderoutes[$i]['dest_type'] == 'P')
            {
                $dst= $port2;
            }
            else
            {
                $dst = $planet2['sector_id'];
            }

            $dist = traderoute_distance ($db, $langvars, $traderoutes[$i]['source_type'], $traderoutes[$i]['dest_type'], $src, $dst, $traderoutes[$i]['circuit']);

            $langvars['l_tdr_escooped_temp'] = str_replace ("[tdr_dist_triptime]", $dist['triptime'], $langvars['l_tdr_escooped']);
            $langvars['l_tdr_escooped2_temp'] = str_replace ("[tdr_dist_scooped]", $dist['scooped'], $langvars['l_tdr_escooped2']);
            echo $langvars['l_tdr_escooped_temp'] . "<br>" . $langvars['l_tdr_escooped2_temp'];

            echo "</font></td>";
        }
        else
        {
            echo "&nbsp;" . $langvars['l_tdr_warp'];

            if ($traderoutes[$i]['circuit'] == '1')
            {
                echo ", 2 " . $langvars['l_tdr_turns'];
            }
            else
            {
                echo ", 4 " . $langvars['l_tdr_turns'];
            }

            echo "</font></td>";
        }

        echo "<td align='center'><font size=2 color=white>";

        if ($traderoutes[$i]['circuit'] == '1')
        {
            echo "&nbsp;1 " . $langvars['l_tdr_way'] . "</font></td>";
        }
        else
        {
            echo "&nbsp;2 " . $langvars['l_tdr_ways'] . "</font></td>";
        }

        echo "<td align='center'><font size=2 color=white>";
        echo "<a href=\"traderoute.php?command=edit&traderoute_id=" . $traderoutes[$i]['traderoute_id'] . "\">";
        echo $langvars['l_edit'] . "</a><br><a href=\"traderoute.php?command=delete&traderoute_id=" . $traderoutes[$i]['traderoute_id'] . "\">";
        echo $langvars['l_tdr_del'] . "</a></font></td></tr>";

        $i++;
    }
    echo "</table><p>";
}

echo "<div style='text-align:left;'>\n";
BntText::gotoMain ($db, $lang, $langvars);
echo "</div>\n";

include './footer.php';

function traderoute_die ($db, $lang, $langvars, $error_msg)
{
    global $sched_ticks, $color_line1, $color_line2, $color_header, $servertimezone;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    echo "<p>" . $error_msg . "<p>";
    echo "<div style='text-align:left;'>\n";
    BntText::gotoMain ($db, $lang, $langvars);
    echo "</div>\n";
    include_once './footer.php';
    die ();
}

function traderoute_check_compatible ($db, $lang, $langvars, $type1, $type2, $move, $circuit, $src, $dest)
{
    global $playerinfo, $color_line1, $color_line2, $color_header, $servertimezone;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));


    // Check circuit compatibility (we only use types 1 and 2 so block anything else)
    if ($circuit != "1" && $circuit != "2")
    {
        BntAdminLog::writeLog ($db, LOG_RAW, "{$playerinfo['ship_id']}|Tried to use an invalid circuit_type of '{$circuit}', This is normally a result from using an external page and should be banned.");
        traderoute_die ("Invalid Circuit type!<br>*** Possible Exploit has been reported to the admin. ***");
    }

    // Check warp links compatibility
    if ($move == 'warp')
    {
        $query = $db->Execute ("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?;", array ($src['sector_id'], $dest['sector_id']));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        if ($query->EOF)
        {
            $langvars['l_tdr_nowlink1'] = str_replace ("[tdr_src_sector_id]", $src['sector_id'], $langvars['l_tdr_nowlink1']);
            $langvars['l_tdr_nowlink1'] = str_replace ("[tdr_dest_sector_id]", $dest['sector_id'], $langvars['l_tdr_nowlink1']);
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_nowlink1']);
        }

        if ($circuit == '2')
        {
            $query = $db->Execute ("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?;", array ($dest['sector_id'], $src['sector_id']));
            DbOp::dbResult ($db, $query, __LINE__, __FILE__);
            if ($query->EOF)
            {
                $langvars['l_tdr_nowlink2'] = str_replace ("[tdr_src_sector_id]", $src['sector_id'], $langvars['l_tdr_nowlink2']);
                $langvars['l_tdr_nowlink2'] = str_replace ("[tdr_dest_sector_id]", $dest['sector_id'], $langvars['l_tdr_nowlink2']);
                traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_nowlink2']);
            }
        }
    }

  // Check ports compatibility
    if ($type1 == 'port')
    {
        if ($src['port_type'] == 'special')
        {
            if (($type2 != 'planet') && ($type2 != 'corp_planet'))
            {
                traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_sportissrc']);
            }

            if ($dest['owner'] != $playerinfo['ship_id'] && ($dest['corp'] == 0 || ($dest['corp'] != $playerinfo['team'])))
            {
                traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_notownplanet']);
            }
        }
        else
        {
            if ($type2 == 'planet')
            {
                traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_planetisdest']);
            }

            if ($src['port_type'] == $dest['port_type'])
            {
                traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_samecom']);
            }
        }
    }
    else
    {
        if (array_key_exists ('port_type', $dest) == true && $dest['port_type'] == 'special')
        {
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_sportcom']);
        }
    }
}

function traderoute_distance ($db, $langvars, $type1, $type2, $start, $dest, $circuit, $sells = 'N')
{
    global $playerinfo, $color_line1, $color_line2, $color_header, $servertimezone, $level_factor;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    $retvalue['triptime'] = 0;
    $retvalue['scooped1'] = 0;
    $retvalue['scooped2'] = 0;
    $retvalue['scooped'] = 0;

    if ($type1 == 'L')
    {
        $query = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?;", array ($start));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        $start = $query->fields;
    }

    if ($type2 == 'L')
    {
        $query = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?;", array ($dest));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        $dest = $query->fields;
    }

    if ($start['sector_id'] == $dest['sector_id'])
    {
        if ($circuit == '1')
        {
            $retvalue['triptime'] = '1';
        }
        else
        {
            $retvalue['triptime'] = '2';
        }

        return $retvalue;
    }

    $deg = pi() / 180;

    $sa1 = $start['angle1'] * $deg;
    $sa2 = $start['angle2'] * $deg;
    $fa1 = $dest['angle1'] * $deg;
    $fa2 = $dest['angle2'] * $deg;
    $x = $start['distance'] * sin($sa1) * cos($sa2) - $dest['distance'] * sin($fa1) * cos($fa2);
    $y = $start['distance'] * sin($sa1) * sin($sa2) - $dest['distance'] * sin($fa1) * sin($fa2);
    $z = $start['distance'] * cos($sa1) - $dest['distance'] * cos($fa1);
    $distance = round (sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2)));
    $shipspeed = pow ($level_factor, $playerinfo['engines']);
    $triptime = round ($distance / $shipspeed);

    if (!$triptime && $dest['sector_id'] != $playerinfo['sector'])
    {
        $triptime = 1;
    }

    if ($playerinfo['dev_fuelscoop'] == "Y")
    {
        $energyscooped = $distance * 100;
    }
    else
    {
        $energyscooped = 0;
    }

    if ($playerinfo['dev_fuelscoop'] == "Y" && !$energyscooped && $triptime == 1)
    {
        $energyscooped = 100;
    }

    $free_power = BntCalcLevels::Energy ($playerinfo['power'], $level_factor) - $playerinfo['ship_energy'];

    if ($free_power < $energyscooped)
    {
        $energyscooped = $free_power;
    }

    if ($energyscooped < 1)
    {
        $energyscooped = 0;
    }

    $retvalue['scooped1'] = $energyscooped;

    if ($circuit == '2')
    {
        if ($sells == 'Y' && $playerinfo['dev_fuelscoop'] == 'Y' && $type2 == 'P' && $dest['port_type'] != 'energy')
        {
            $energyscooped = $distance * 100;
            $free_power = BntCalcLevels::Energy ($playerinfo['power'], $level_factor);

            if ($free_power < $energyscooped)
            {
                $energyscooped = $free_power;
            }

            $retvalue['scooped2'] = $energyscooped;
        }
        elseif ($playerinfo['dev_fuelscoop'] == 'Y')
        {
            $energyscooped = $distance * 100;
            $free_power = BntCalcLevels::Energy ($playerinfo['power'], $level_factor) - $retvalue['scooped1'] - $playerinfo['ship_energy'];

            if ($free_power < $energyscooped)
            {
                $energyscooped = $free_power;
            }

            $retvalue['scooped2'] = $energyscooped;
        }
    }

    if ($circuit == '2')
    {
        $triptime*= 2;
        $triptime+= 2;
    }
    else
    {
        $triptime+= 1;
    }

    $retvalue['triptime'] = $triptime;
    $retvalue['scooped'] = $retvalue['scooped1'] + $retvalue['scooped2'];

    return $retvalue;
}

function traderoute_create ($db, $lang, $langvars)
{
    global $playerinfo, $color_line1, $color_line2, $color_header;
    global $num_traderoutes, $servertimezone;
    global $max_traderoutes_player;
    global $sector_max;
    global $ptype1;
    global $ptype2;
    global $port_id1;
    global $port_id2;
    global $planet_id1;
    global $planet_id2;
    global $corp_planet_id1;
    global $corp_planet_id2;
    global $move_type;
    global $circuit_type;
    global $editing;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    if ($num_traderoutes >= $max_traderoutes_player && empty ($editing))
    { // Dont let them exceed max traderoutes
        traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_maxtdr']);
    }

    // Database sanity check for source
    if ($ptype1 == 'port')
    {
        // Check for valid Source Port
        if ($port_id1 >= $sector_max)
        {
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_invalidspoint']);
        }

        $query = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?;", array ($port_id1));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        if (!$query || $query->EOF)
        {
            $langvars['l_tdr_errnotvalidport'] = str_replace ("[tdr_port_id]", $port_id1, $langvars['l_tdr_errnotvalidport']);
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_errnotvalidport']);
        }

        // OK we definitely have a port here
        $source= $query->fields;
        if ($source['port_type'] == 'none')
        {
            $langvars['l_tdr_errnoport'] = str_replace ("[tdr_port_id]", $port_id1, $langvars['l_tdr_errnoport']);
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_errnoport']);
        }
    }
    else
    {
        $query = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE planet_id=?;", array ($planet_id1));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        $source = $query->fields;
        if (!$query || $query->EOF)
        {
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_errnosrc']);
        }

        // Check for valid Source Planet
        if ($source['sector_id'] >= $sector_max)
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_invalidsrc']);

        if ($source['owner'] != $playerinfo['ship_id'])
        {
            if (($playerinfo['team'] == 0 || $playerinfo['team'] != $source['corp']) && $source['sells'] == 'N')
            {
                // $langvars['l_tdr_errnotownnotsell'] = str_replace ("[tdr_source_name]", $source[name], $langvars['l_tdr_errnotownnotsell']);
                // $langvars['l_tdr_errnotownnotsell'] = str_replace ("[tdr_source_sector_id]", $source[sector_id], $langvars['l_tdr_errnotownnotsell']);
                // traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_errnotownnotsell']);

                // Check for valid Owned Source Planet
                BntAdminLog::writeLog ($db, 902, "{$playerinfo['ship_id']}|Tried to find someones planet: {$planet_id1} as source.");
                traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_invalidsrc']);
            }
        }
    }
    // OK we have $source, *probably* now lets see if we have ever been there
    // Attempting to fix the map the universe via traderoute bug

    $pl1query = $db->Execute ("SELECT * FROM {$db->prefix}movement_log WHERE sector_id=? AND ship_id = ?;", array ($source['sector_id'], $playerinfo['ship_id']));
    DbOp::dbResult ($db, $pl1query, __LINE__, __FILE__);
    $num_res1 = $pl1query->numRows();
    if ($num_res1 == 0)
    {
        traderoute_die ($db, $lang, $langvars, "You cannot create a traderoute from a sector you have not visited!");
    }
    // Note: shouldnt we, more realistically, require a ship to be *IN* the source sector to create the traderoute?

    // Database sanity check for dest
    if ($ptype2 == 'port')
    {
        // Check for valid Dest Port
        if ($port_id2 >= $sector_max)
        {
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_invaliddport']);
        }

        $query = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?;", array ($port_id2));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        if (!$query || $query->EOF)
        {
            $langvars['l_tdr_errnotvaliddestport'] = str_replace ("[tdr_port_id]", $port_id2, $langvars['l_tdr_errnotvaliddestport']);
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_errnotvaliddestport']);
        }

        $destination = $query->fields;

        if ($destination['port_type'] == 'none')
        {
            $langvars['l_tdr_errnoport2'] = str_replace ("[tdr_port_id]", $port_id2, $langvars['l_tdr_errnoport2']);
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_errnoport2']);
        }
    }
    else
    {
        $query = $db->Execute ("SELECT * FROM {$db->prefix}planets WHERE planet_id=?;", array ($planet_id2));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        $destination = $query->fields;
        if (!$query || $query->EOF)
        {
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_errnodestplanet']);
        }

        // Check for valid Dest Planet
        if ($destination['sector_id'] >= $sector_max)
        {
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_invaliddplanet']);
        }

        if ($destination['owner'] != $playerinfo['ship_id'] && $destination['sells'] == 'N')
        {
            // $langvars['l_tdr_errnotownnotsell2'] = str_replace ("[tdr_dest_name]", $destination['name'], $langvars['l_tdr_errnotownnotsell2']);
            // $langvars['l_tdr_errnotownnotsell2'] = str_replace ("[tdr_dest_sector_id]", $destination['sector_id'], $langvars['l_tdr_errnotownnotsell2']);
            // traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_errnotownnotsell2']);

            // Check for valid Owned Source Planet
            BntAdminLog::writeLog ($db, 902, "{$playerinfo['ship_id']}|Tried to find someones planet: {$planet_id2} as dest.");
            traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_invaliddplanet']);
        }
    }

    // OK now we have $destination lets see if we've been there.
    $pl2query = $db->Execute ("SELECT * FROM {$db->prefix}movement_log WHERE sector_id=? AND ship_id = ?;", array ($destination['sector_id'], $playerinfo['ship_id']));
    DbOp::dbResult ($db, $pl2query, __LINE__, __FILE__);
    $num_res2 = $pl2query->numRows();
    if ($num_res2 == 0)
    {
        traderoute_die ($db, $lang, $langvars, "You cannot create a traderoute into a sector you have not visited!");
    }

    // Check destination - we cannot trade INTO a special port
    if (array_key_exists ('port_type', $destination) == true && $destination['port_type'] == 'special')
    {
        traderoute_die ($db, $lang, $langvars, "You cannot create a traderoute into a special port!");
    }
    // Check traderoute for src => dest
    traderoute_check_compatible ($db, $lang, $langvars, $ptype1, $ptype2, $move_type, $circuit_type, $source , $destination);

    if ($ptype1 == 'port')
    {
        $src_id = $port_id1;
    }
    elseif ($ptype1 == 'planet')
    {
        $src_id = $planet_id1;
    }
    elseif ($ptype1 == 'corp_planet')
    {
        $src_id = $corp_planet_id1;
    }

    if ($ptype2 == 'port')
    {
        $dest_id = $port_id2;
    }
    elseif ($ptype2 == 'planet')
    {
        $dest_id = $planet_id2;
    }
    elseif ($ptype2 == 'corp_planet')
    {
        $dest_id = $corp_planet_id2;
    }

    if ($ptype1 == 'port')
    {
        $src_type = 'P';
    }
    elseif ($ptype1 == 'planet')
    {
        $src_type = 'L';
    }
    elseif ($ptype1 == 'corp_planet')
    {
        $src_type = 'C';
    }

    if ($ptype2 == 'port')
    {
        $dest_type = 'P';
    }
    elseif ($ptype2 == 'planet')
    {
        $dest_type = 'L';
    }
    elseif ($ptype2 == 'corp_planet')
    {
        $dest_type = 'C';
    }

    if ($move_type == 'realspace')
    {
        $mtype = 'R';
    }
    else
    {
        $mtype = 'W';
    }

    if (empty ($editing))
    {
        $query = $db->Execute ("INSERT INTO {$db->prefix}traderoutes VALUES(NULL, ?, ?, ?, ?, ?, ?, ?);", array ($src_id, $dest_id, $src_type, $dest_type, $mtype, $playerinfo['ship_id'], $circuit_type));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        echo "<p>" . $langvars['l_tdr_newtdrcreated'];
    }
    else
    {
        $query = $db->Execute ("UPDATE {$db->prefix}traderoutes SET source_id=?, dest_id=?, source_type=?, dest_type=?, move_type=?, owner=?, circuit=? WHERE traderoute_id=?;", array ($src_id, $dest_id, $src_type, $dest_type, $mtype, $playerinfo['ship_id'], $circuit_type, $editing));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        echo "<p>" . $langvars['l_tdr_modified'];
    }

    $langvars['l_tdr_returnmenu'] = str_replace ("[here]", "<a href='traderoute.php'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_returnmenu']);
    echo " " . $langvars['l_tdr_returnmenu'];
    traderoute_die ($db, $lang, $langvars, "");
}

function traderoute_delete ($db, $langvars)
{
    global $playerinfo, $color_line1, $color_line2, $color_header;
    global $confirm, $servertimezone;
    global $num_traderoutes;
    global $traderoute_id;
    global $traderoutes;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    $query = $db->Execute ("SELECT * FROM {$db->prefix}traderoutes WHERE traderoute_id=?;", array ($traderoute_id));
    DbOp::dbResult ($db, $query, __LINE__, __FILE__);

    if (!$query || $query->EOF)
    {
        traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_doesntexist']);
    }

    $delroute = $query->fields;

    if ($delroute['owner'] != $playerinfo['ship_id'])
    {
        traderoute_die ($db, $lang, $langvars, $langvars['l_tdr_notowntdr']);
    }

    if (empty ($confirm))
    {
        $num_traderoutes = 1;
        $traderoutes[0] = $delroute;
        // Here it continues to the main file area to print the route
    }
    else
    {
        $query = $db->Execute ("DELETE FROM {$db->prefix}traderoutes WHERE traderoute_id=?;", array ($traderoute_id));
        DbOp::dbResult ($db, $query, __LINE__, __FILE__);
        $langvars['l_tdr_returnmenu'] = str_replace ("[here]", "<a href='traderoute.php'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_returnmenu']);
        echo $langvars['l_tdr_deleted'] . " " . $langvars['l_tdr_returnmenu'];
        traderoute_die ($db, $lang, $langvars, "");
    }
}

function traderoute_settings ($db, $lang, $langvars)
{
    global $playerinfo, $color_line1, $color_line2, $color_header, $servertimezone;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    echo "<p><font size=3 color=blue><strong>" . $langvars['l_tdr_globalset'] . "</strong></font><p>";
    echo "<font color=white size=2><strong>" . $langvars['l_tdr_sportsrc'] . " :</strong></font><p>".
         "<form action=traderoute.php?command=setsettings method=post>".
         "<table border=0><tr>".
         "<td><font size=2 color=white> - " . $langvars['l_tdr_colonists'] . " :</font></td>".
         "<td><input type=checkbox name=colonists";

    if ($playerinfo['trade_colonists'] == 'Y')
    {
        echo " checked";
    }

    echo "></tr><tr>".
        "<td><font size=2 color=white> - " . $langvars['l_tdr_fighters'] . " :</font></td>".
        "<td><input type=checkbox name=fighters";

    if ($playerinfo['trade_fighters'] == 'Y')
    {
        echo " checked";
    }

    echo "></tr><tr>".
        "<td><font size=2 color=white> - " . $langvars['l_tdr_torps'] . " :</font></td>".
        "<td><input type=checkbox name=torps";

    if ($playerinfo['trade_torps'] == 'Y')
    {
        echo " checked";
    }

    echo "></tr>".
        "</table>".
        "<p>".
        "<font color=white size=2><strong>" . $langvars['l_tdr_tdrescooped'] . " :</strong></font><p>".
        "<table border=0><tr>".
        "<td><font size=2 color=white>&nbsp;&nbsp;&nbsp;" . $langvars['l_tdr_trade'] . "</font></td>".
        "<td><input type=radio name=energy value=\"Y\"";

    if ($playerinfo['trade_energy'] == 'Y')
    {
        echo " checked";
    }

    echo "></td></tr><tr>".
        "<td><font size=2 color=white>&nbsp;&nbsp;&nbsp;" . $langvars['l_tdr_keep'] . "</font></td>".
        "<td><input type=radio name=energy value=\"N\"";

    if ($playerinfo['trade_energy'] == 'N')
    {
        echo " checked";
    }

    echo "></td></tr><tr><td>&nbsp;</td></tr><tr><td>".
        "<td><input type=submit value=\"" . $langvars['l_tdr_save'] . "\"></td>".
        "</tr></table>".
        "</form>";

    $langvars['l_tdr_returnmenu'] = str_replace ("[here]", "<a href='traderoute.php'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_returnmenu']);
    echo $langvars['l_tdr_returnmenu'];
    traderoute_die ($db, $lang, $langvars, "");
}

function traderoute_setsettings ($db, $langvars)
{
    global $playerinfo, $color_line1, $color_line2, $color_header;
    global $colonists, $servertimezone, $fighters, $torps, $energy;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    empty ($colonists) ? $colonists = 'N' : $colonists = 'Y';
    empty ($fighters) ? $fighters = 'N' : $fighters = 'Y';
    empty ($torps) ? $torps = 'N' : $torps = 'Y';

    $resa = $db->Execute ("UPDATE {$db->prefix}ships SET trade_colonists=?, trade_fighters=?, trade_torps=?, trade_energy=? WHERE ship_id=?;", array ($colonists, $fighters, $torps, $energy, $playerinfo['ship_id']));
    DbOp::dbResult ($db, $resa, __LINE__, __FILE__);

    $langvars['l_tdr_returnmenu'] = str_replace ("[here]", "<a href='traderoute.php'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_returnmenu']);
    echo $langvars['l_tdr_globalsetsaved'] . " " . $langvars['l_tdr_returnmenu'];
    traderoute_die ($db, $lang, $langvars, "");
}

function traderoute_results_table_top ($db, $lang, $langvars)
{
    global $color_line2;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    echo "<table border='1' cellspacing='1' cellpadding='2' width='65%' align='center'>\n";
    echo "  <tr bgcolor='".$color_line2."'>\n";
    echo "    <td align='center' colspan='7'><strong><font color='white'>" . $langvars['l_tdr_res'] . "</font></strong></td>\n";
    echo "  </tr>\n";
    echo "  <tr align='center' bgcolor='".$color_line2."'>\n";
    echo "    <td width='50%'><font size='2' color='white'><strong>";
}

function traderoute_results_source ()
{
    echo "</strong></font></td>\n";
    echo "    <td width='50%'><font size='2' color='white'><strong>";
}

function traderoute_results_destination ()
{
    global $color_line1;
    echo "</strong></font></td>\n";
    echo "  </tr>\n";
    echo "  <tr bgcolor='".$color_line1."'>\n";
    echo "    <td align='center'><font size='2' color='white'>";
}

function traderoute_results_close_cell ()
{
    echo "</font></td>\n";
    echo "    <td align='center'><font size='2' color='white'>";
}

function traderoute_results_show_cost ()
{
    global $color_line2;
    echo "</font></td>\n";
    echo "  </tr>\n";
    echo "  <tr bgcolor='".$color_line2."'>\n";
    echo "    <td align='center'><font size='2' color='white'>";
}

function traderoute_results_close_cost ()
{
    echo "</font></td>\n";
    echo "    <td align='center'><font size='2' color='white'>";
}

function traderoute_results_close_table ()
{
    echo "</font></td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
    // echo "<p><center><font size=3 color=white><strong>\n";
}

function traderoute_results_display_totals ($db, $lang, $langvars, $total_profit)
{
    global $local_number_dec_point, $local_number_thousands_sep;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    if ($total_profit > 0)
    {
        echo "<p><center><font size=3 color=white><strong>" . $langvars['l_tdr_totalprofit'] . " : <font style='color:#0f0;'><strong>" . number_format (abs($total_profit), 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong></font><br>\n";
    }
    else
    {
        echo "<p><center><font size=3 color=white><strong>" . $langvars['l_tdr_totalcost'] . " : <font style='color:#f00;'><strong>" . number_format (abs($total_profit), 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong></font><br>\n";
    }
}

function traderoute_results_display_summary ($db, $lang, $langvars, $tdr_display_creds)
{
    global $dist, $playerinfo;
    $langvars = BntTranslate::load ($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer'));

    echo "\n<font size='3' color='white'><strong>" . $langvars['l_tdr_turnsused'] . " : <font style='color:#f00;'>$dist[triptime]</font></strong></font><br>";
    echo "\n<font size='3' color='white'><strong>" . $langvars['l_tdr_turnsleft'] . " : <font style='color:#0f0;'>$playerinfo[turns]</font></strong></font><br>";

    echo "\n<font size='3' color='white'><strong>" . $langvars['l_tdr_credits'] . " : <font style='color:#0f0;'> $tdr_display_creds\n</font></strong></font><br> </strong></font></center>\n";
    //echo "<font size='2'>\n";
}

function traderoute_results_show_repeat ($engage, $level_factor)
{
    echo "<form action='traderoute.php?engage=".$engage."' method='post'>\n";
    echo "<br>Enter times to repeat <input type='TEXT' name='tr_repeat' value='1' size='5'> <input type='SUBMIT' value='SUBMIT'>\n";
    echo "</form>\n";
    // echo "<p>";
}
?>
