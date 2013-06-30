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
// File: rsmove.php
// External variables: $destination (from get or post), int, range 1-$sector_max)
// $engage (from get), int, range 0-2)

include './global_includes.php';

if (check_login ($db, $lang, $langvars, $bntreg))
{
    die ();
}

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('rsmove'));
$title = $langvars['l_rs_title'];
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('rsmove', 'common', 'global_funcs', 'global_includes', 'combat', 'footer', 'news'));

$res = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
DbOp::dbResult ($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;

echo "<h1>" . $title . "</h1>\n";

$deg = pi () / 180;

/* TK version
$destination = null;
if (isset ($_GET['destination']))
{
    $destination  = (int) filter_input (INPUT_GET, 'destination', FILTER_SANITIZE_NUMBER_INT);
}

if (isset ($_POST['destination']))
{
    $destination  = (int) filter_input (INPUT_POST, 'destination', FILTER_SANITIZE_NUMBER_INT);
}

if ($destination < 1 || $destination > $sector_max)
{
    $destination = null;
}
*/

/* TMD version */
$destination  = filter_input (INPUT_GET, 'destination', FILTER_SANITIZE_NUMBER_INT);
if ( is_null($destination) )
{
    $destination  = filter_input (INPUT_POST, 'destination', FILTER_SANITIZE_NUMBER_INT);
}

if ( !is_null($destination) && strlen(trim($destination)) <=1)
{
    $destination = null;
}

if ( !is_null($destination) )
{
    settype($destination, "integer");
}

$engage  = (int) filter_input (INPUT_GET, 'engage', FILTER_SANITIZE_NUMBER_INT);

if (isset ($destination))
{
    $destination = round (abs ($destination));
    $result2 = $db->Execute ("SELECT angle1, angle2, distance FROM {$db->prefix}universe WHERE sector_id = ?;", array ($playerinfo['sector']));
    DbOp::dbResult ($db, $result2, __LINE__, __FILE__);
    $start = $result2->fields;
    $result3 = $db->Execute ("SELECT angle1, angle2, distance FROM {$db->prefix}universe WHERE sector_id = ?;", array ($destination));
    DbOp::dbResult ($db, $result3, __LINE__, __FILE__);
    $finish = $result3->fields;
    $sa1 = $start['angle1'] * $deg;
    $sa2 = $start['angle2'] * $deg;
    $fa1 = $finish['angle1'] * $deg;
    $fa2 = $finish['angle2'] * $deg;
    $x = ($start['distance'] * sin ($sa1) * cos ($sa2)) - ($finish['distance'] * sin ($fa1) * cos ($fa2));
    $y = ($start['distance'] * sin ($sa1) * sin ($sa2)) - ($finish['distance'] * sin ($fa1) * sin ($fa2));
    $z = ($start['distance'] * cos ($sa1)) - ($finish['distance'] * cos ($fa1));
    $distance = round (sqrt (pow ($x, 2) + pow ($y, 2) + pow ($z, 2)));
    $shipspeed = pow ($level_factor, $playerinfo['engines']);
    $triptime = round ($distance / $shipspeed);

    if ($triptime == 0 && $destination != $playerinfo['sector'])
    {
        $triptime = 1;
    }

    if ($destination == $playerinfo['sector'])
    {
        $triptime = 0;
        $energyscooped = 0;
    }
}

if (!isset ($destination))
{
    echo "<form action=rsmove.php method=post>";
    $langvars['l_rs_insector'] = str_replace ("[sector]", $playerinfo['sector'], $langvars['l_rs_insector']);
    $langvars['l_rs_insector'] = str_replace ("[sector_max]", $sector_max - 1, $langvars['l_rs_insector']);
    echo $langvars['l_rs_insector'] . "<br><br>";
    echo $langvars['l_rs_whichsector'] . ":  <input type=text name=destination size=10 maxlength=10><br><br>";
    echo "<input type=submit value='" . $langvars['l_rs_submit'] . "'><br><br>";
    echo "</form>";
}
elseif (($destination < $sector_max && empty ($engage)) || ($destination < $sector_max && $triptime > 100 && $engage == 1))
{
    if ($playerinfo['dev_fuelscoop'] == "Y")
    {
        $energyscooped = $distance * 100;
    }
    else
    {
        $energyscooped = 0;
    }

    if ($playerinfo['dev_fuelscoop'] == "Y" && $energyscooped == 0 && $triptime == 1)
    {
        $energyscooped = 100;
    }

    $free_power = CalcLevels::Energy ($playerinfo['power'], $level_factor) - $playerinfo['ship_energy'];
    if ($free_power < $energyscooped)
    {
        $energyscooped = $free_power;
    }

    if ($energyscooped < 1)
    {
        $energyscooped = 0;
    }

    $langvars['l_rs_movetime'] = str_replace ("[triptime]", number_format ($triptime, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_movetime']);
    $langvars['l_rs_energy'] = str_replace ("[energy]", number_format ($energyscooped, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_energy']);
    echo $langvars['l_rs_movetime'] . " " . $langvars['l_rs_energy'] . "<br><br>";

    if ($triptime > $playerinfo['turns'])
    {
        echo $langvars['l_rs_noturns'];
    }
    else
    {
        $langvars['l_rs_engage_link'] = "<a href=rsmove.php?engage=2&destination=$destination>" . $langvars['l_rs_engage_link'] . "</a>";
        $langvars['l_rs_engage'] = str_replace ("[turns]", number_format ($playerinfo['turns'], 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_engage']);
        $langvars['l_rs_engage'] = str_replace ("[engage]", $langvars['l_rs_engage_link'], $langvars['l_rs_engage']);
        echo $langvars['l_rs_engage'] . "<br><br>";
    }
}
elseif ($destination < $sector_max && $engage > 0)
{
    if ($playerinfo['dev_fuelscoop'] == "Y")
    {
        $energyscooped = $distance * 100;
    }
    else
    {
        $energyscooped = 0;
    }

    if ($playerinfo['dev_fuelscoop'] == "Y" && $energyscooped == 0 && $triptime == 1)
    {
        $energyscooped = 100;
    }

    $free_power = CalcLevels::Energy ($playerinfo['power'], $level_factor) - $playerinfo['ship_energy'];
    if ($free_power < $energyscooped)
    {
        $energyscooped = $free_power;
    }

    if (!isset ($energyscooped) || $energyscooped < 1)
    {
        $energyscooped = 0;
    }

    if ($triptime > $playerinfo['turns'])
    {
        $langvars['l_rs_movetime'] = str_replace ("[triptime]", number_format ($triptime, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_movetime']);
        echo $langvars['l_rs_movetime'] . "<br><br>";
        echo $langvars['l_rs_noturns'] . "<br><br>";
        $resx = $db->Execute ("UPDATE {$db->prefix}ships SET cleared_defences=' ' WHERE ship_id = ?;", array ($playerinfo['ship_id']));
        DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
    }
    else
    {
        $ok=1;
        $sector = $destination;
        $calledfrom = "rsmove.php";
        include_once './check_fighters.php';
        if ($ok > 0)
        {
            $langvars = BntTranslate::load ($db, $lang, array ('rsmove', 'common', 'global_funcs', 'global_includes', 'combat', 'footer', 'news'));
            $stamp = date ("Y-m-d H:i:s");
            $update = $db->Execute ("UPDATE {$db->prefix}ships SET last_login = ?, sector = ?, ship_energy = ship_energy + ?, turns = turns - ?, turns_used = turns_used + ? WHERE ship_id = ?;", array ($stamp, $destination, $energyscooped, $triptime, $triptime, $playerinfo['ship_id']));
            DbOp::dbResult ($db, $update, __LINE__, __FILE__);
            LogMove::writeLog ($db, $playerinfo['ship_id'], $destination);
            $langvars['l_rs_ready'] = str_replace ("[sector]", $destination, $langvars['l_rs_ready']);
            $langvars['l_rs_ready'] = str_replace ("[triptime]", number_format ($triptime, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_ready']);
            $langvars['l_rs_ready'] = str_replace ("[energy]", number_format ($energyscooped, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_ready']);
            echo $langvars['l_rs_ready'] . "<br><br>";
            include_once './check_mines.php';
        }
    }
}
else
{
    echo $langvars['l_rs_invalid'] . ".<br><br>";
    $resx = $db->Execute ("UPDATE {$db->prefix}ships SET cleared_defences=' ' WHERE ship_id = ?;", array ($playerinfo['ship_id']));
    DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
}

$langvars = BntTranslate::load ($db, $lang, array ('global_funcs'));
BntText::gotoMain ($db, $lang, $langvars);
include './footer.php';
?>
