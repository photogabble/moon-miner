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

namespace App\Actions\Movement;

use Exception;
use App\Models\Ship;
use App\Models\MovementLog;
use App\Types\MovementMode;
use App\Models\Waypoints\WarpGate;

class WarpJump {
    public function __construct(private Ship $ship) {}

    public function jump(WarpGate $gate): MovementLog
    {
        // Check if there was a valid warp link to move to
        if ($gate->system_id !== $this->ship->system_id) {
            throw new Exception('Unable to make use of warp gate outside of occupied system.');
        }

        if (!isset($gate->properties->destination_system_id)) {
            throw new Exception("The warp gate is not online");
        }

        // Check to see if the player has less than one turn available
        if ($this->ship->owner->turns < $this->ship->warpTravelTurnCost()) {
            throw new Exception('You do not have enough turns to use this warp gate');
        }

        if (!is_null($this->ship->owner->currentEncounter)) {
            throw new Exception("You can't move yet, you are in the middle of something!");
        }

        return $this->ship->travelTo(
            $gate->properties->destination_system_id,
            MovementMode::Warp,
            $this->ship->warpTravelTurnCost()
        );
    }
}
