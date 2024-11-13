<?php declare(strict_types=1);
/**
 * Moon Miner, a Free & Opensource (FOSS), web-based 4X space/strategy game forked
 * and based upon Black Nova Traders.
 *
 * @copyright 2024 Simon Dann
 * @copyright 2001-2014 Ron Harwood and the BNT development team
 *
 * @license GNU AGPL version 3.0 or (at your option) any later version.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Bnt;

use App\Helpers\CalcLevels;

class Move
{
    public static function calcFuelScooped(\App\Models\Ship $ship, $distance, int $triptime) : int
    {
        // Check if we have a fuel scoop
        if ($ship->dev_fuelscoop) {
            // We have a fuel scoop, now calculate the amount of energy scooped.
            $energyScooped = $distance * 100;
        } else {
            // Nope, the FuelScoop won't be installed until next Tuesday (Star Trek quote) :P
            $energyScooped = 0;
        }

        // Seems this will never happen ?
        if ($ship->dev_fuelscoop && $energyScooped == 0 && $triptime == 1) {
            $energyScooped = 100;
        }

        // Calculate the free power for the ship.
        $freePower = CalcLevels::energy($ship->power) - $ship->ship_energy;
        if ($freePower < $energyScooped) {
            // Limit the energy scooped to the maximum free power available.
            $energyScooped = $freePower;
        }

        // Not too sure what this line is doing, may need to add debugging code.
        // Could be checking for a negitive scoop value.
        if ($energyScooped < 1) {
            $energyScooped = 0;
        }

        return (int) $energyScooped;
    }
}
