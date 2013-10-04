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

BntLogin::checkLogin ($db, $lang, $langvars, $bntreg);

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('rsmove'));
$title = $langvars['l_rs_title'];
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('rsmove', 'common', 'global_funcs', 'global_includes', 'combat', 'footer', 'news'));

// Get the Players Information.
$res = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
DbOp::dbResult ($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;

echo "<h1>" . $title . "</h1>\n";

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

/* TMD version 
 * Returns null if it doesn't have it set, boolean false if its set but fails to validate and the actual value if it all passes.
 */
$destination  = filter_input (INPUT_GET, 'destination', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, 'max_range'=>$sector_max)));
if ( is_null($destination) )
{
    $destination  = filter_input (INPUT_POST, 'destination', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1, 'max_range'=>$sector_max)));
}

$engage  = filter_input (INPUT_GET, 'engage', FILTER_VALIDATE_INT, array('options'=>array('min_range'=>0, 'max_range'=>2)));

if ($destination === false || $engage === false)
{
    // Either the destination or the engage variables have failed validation.
    /* Output:
     * Invalid Destination
     */

    echo $langvars['l_rs_invalid'] . ".<br><br>";
    $resx = $db->Execute ("UPDATE {$db->prefix}ships SET cleared_defences=' ' WHERE ship_id = ?;", array ($playerinfo['ship_id']));
    DbOp::dbResult ($db, $resx, __LINE__, __FILE__);
}
else
{
    // Check if we haven't been sent the destination.
    if (is_null($destination) === true)
    {
        // Nope, destination not sent, so show Web Form for destination.
        /* Output:
         * <form action='rsmove.php' method='post'>
         * You are presently in sector X - and there are sectors available from 1 to Y.
         * Which sector would you like to reach through Realspace? :  <input type='text' name='destination' size='10' maxlength='10'>
         * <input type='submit' value='Compute'>
         * </form>
         */
        echo "<form action='rsmove.php' method='post'>\n";
        $langvars['l_rs_insector'] = str_replace ("[sector]", $playerinfo['sector'], $langvars['l_rs_insector']);
        $langvars['l_rs_insector'] = str_replace ("[sector_max]", $sector_max - 1, $langvars['l_rs_insector']);
        echo $langvars['l_rs_insector'] . "<br><br>\n";
        echo $langvars['l_rs_whichsector'] . ":  <input type='text' name='destination' size='10' maxlength='10'><br><br>\n";
        echo "<input type='submit' value='" . $langvars['l_rs_submit'] . "'><br><br>\n";
        echo "</form>\n";
    }
    else
    {
        // Ok, we have been given the destination value...
        // Get the players current sector information.
        $result2 = $db->Execute ("SELECT angle1, angle2, distance FROM {$db->prefix}universe WHERE sector_id = ?;", array ($playerinfo['sector']));
        DbOp::dbResult ($db, $result2, __LINE__, __FILE__);
        $start = $result2->fields;

        // Get the destination sector information.
        $result3 = $db->Execute ("SELECT angle1, angle2, distance FROM {$db->prefix}universe WHERE sector_id = ?;", array ($destination));
        DbOp::dbResult ($db, $result3, __LINE__, __FILE__);
        $finish = $result3->fields;

        // Calculate the Math for the Distance.
        $deg = pi () / 180;

        $sa1 = $start['angle1'] * $deg;
        $sa2 = $start['angle2'] * $deg;
        $fa1 = $finish['angle1'] * $deg;
        $fa2 = $finish['angle2'] * $deg;

        $x = ($start['distance'] * sin ($sa1) * cos ($sa2)) - ($finish['distance'] * sin ($fa1) * cos ($fa2));
        $y = ($start['distance'] * sin ($sa1) * sin ($sa2)) - ($finish['distance'] * sin ($fa1) * sin ($fa2));
        $z = ($start['distance'] * cos ($sa1)) - ($finish['distance'] * cos ($fa1));

        $distance = round (sqrt (pow ($x, 2) + pow ($y, 2) + pow ($z, 2)));

        // Calculate the Speed of the Ship.
        $shipspeed = pow ($level_factor, $playerinfo['engines']);

        // Calculate the Trip time.
        $triptime = round ($distance / $shipspeed);

        if ($destination == $playerinfo['sector'])
        {
            $triptime = 0;
            $energyscooped = 0;
        }
        else if ($triptime == 0 && $destination != $playerinfo['sector'])
        {
            $triptime = 1;
        }

        // Check to see if engage isn't set or if triptime is larger than 100 and engage is set to 1
        if ( is_null ($engage) || ($triptime > 100 && $engage == 1))
        {
            // Handle the Fuel Scoop.
            $energyscooped = HandleFuelScoop($playerinfo, $distance, $triptime, $bntreg);

            /* Output:
             * With your engines, it will take X turns to complete the journey.
             * You would gather Y units of energy
             */
            $langvars['l_rs_movetime'] = str_replace ("[triptime]", number_format ($triptime, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_movetime']);
            $langvars['l_rs_energy'] = str_replace ("[energy]", number_format ($energyscooped, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_energy']);
            echo $langvars['l_rs_movetime'] . "<br>" . $langvars['l_rs_energy'] . "<br><br>";

            if ($triptime > $playerinfo['turns'])
            {
                // Player doesn't have enough turns.
                /* Output:
                 * You do not have enough turns left, and cannot embark on this journey.
                 */
                echo $langvars['l_rs_noturns'];
            }
            else
            {
                // Player has enough turns.
                /* Output:
                 * You have X turns. <a href=rsmove.php?engage=2&destination=$destination>Engage</a> engines?
                 */
                $langvars['l_rs_engage_link'] = "<a href=rsmove.php?engage=2&destination=$destination>" . $langvars['l_rs_engage_link'] . "</a>";
                $langvars['l_rs_engage'] = str_replace ("[turns]", number_format ($playerinfo['turns'], 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_engage']);
                $langvars['l_rs_engage'] = str_replace ("[engage]", $langvars['l_rs_engage_link'], $langvars['l_rs_engage']);
                echo $langvars['l_rs_engage'] . "<br><br>";
            }

        }
        elseif ($engage > 0)
        {
            // Handle the Fuel Scoop.
            $energyscooped = HandleFuelScoop($playerinfo, $distance, $triptime, $bntreg);

            if ($triptime > $playerinfo['turns'])
            {
                /* Output:
                 * With your engines, it will take X turns to complete the journey.
                 * You do not have enough turns left, and cannot embark on this journey.
                 */

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
                    /* Output:
                     * You are now in sector X. You used Y turns, and gained Z energy units.
                     */

                    $langvars = BntTranslate::load ($db, $lang, array ('rsmove', 'common', 'global_funcs', 'global_includes', 'combat', 'footer', 'news'));
                    $stamp = date ("Y-m-d H:i:s");
                    $update = $db->Execute ("UPDATE {$db->prefix}ships SET last_login = ?, sector = ?, ship_energy = ship_energy + ?, turns = turns - ?, turns_used = turns_used + ? WHERE ship_id = ?;", array ($stamp, $destination, $energyscooped, $triptime, $triptime, $playerinfo['ship_id']));
                    DbOp::dbResult ($db, $update, __LINE__, __FILE__);
                    BntLogMove::writeLog ($db, $playerinfo['ship_id'], $destination);
                    $langvars['l_rs_ready'] = str_replace ("[sector]", $destination, $langvars['l_rs_ready']);
                    $langvars['l_rs_ready'] = str_replace ("[triptime]", number_format ($triptime, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_ready']);
                    $langvars['l_rs_ready'] = str_replace ("[energy]", number_format ($energyscooped, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_rs_ready']);
                    echo $langvars['l_rs_ready'] . "<br><br>";
                    include_once './check_mines.php';
                }
            }
        }

    }

}
BntText::gotoMain ($db, $lang, $langvars);
include './footer.php';

// Now for our functions.
function HandleFuelScoop($playerinfo, $distance, $triptime, $bntreg)
{
    // Check if we have a FuelScoop
    if ($playerinfo['dev_fuelscoop'] == "Y")
    {
        // WE have a FuelScoop, no calculate the amount of Energy Scooped.
        $energyscooped = $distance * 100;
    }
    else
    {
        // Nope, the FuelScoop won't be installed until next Tuesday (StarTrek Quote) :P
        $energyscooped = 0;
    }

// Seems this will never happen ?
    if ($playerinfo['dev_fuelscoop'] == "Y" && $energyscooped == 0 && $triptime == 1)
    {
        $energyscooped = 100;
    }

    // Calculate the Ship Free Power.
    $free_power = BntCalcLevels::Energy ($playerinfo['power'], $bntreg->get('level_factor') ) - $playerinfo['ship_energy'];
    if ($free_power < $energyscooped)
    {
        // Clip the Energy Scooped to the maximum Free Power available.
        $energyscooped = $free_power;
    }

// Not too sure what this line is doing, may need to add debugging code.
// Could be checking for a negitive scoop value.
    if ($energyscooped < 1)
    {
        $energyscooped = 0;
    }

    return $energyscooped;
}

?>
