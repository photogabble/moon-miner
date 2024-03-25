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

namespace App\Models\Traits;

use Exception;
use App\Models\Ship;

/**
 * Some entities can be docked/landed on by Ships. For planets this provides
 * the planet with an offensive edge in planetary battle, but also provides
 * the ship some protection.
 *
 * With Space Ports, docking not only hides the Ship from other players but
 * also provides the player with access to additional services.
 */
trait ShipsCanDock
{
    public function dock(Ship $ship): bool
    {
        if ($ship->system_id !== $this->system_id) {
            throw new Exception('Can not dock at a waypoint in a different system.');
        }

        if (!is_null($ship->waypoint_id)) {
            throw new Exception('Unable to dock somewhere else while still docked.');
        }

        $ship->waypoint_id = $this->id;
        return $ship->save();
    }
}
