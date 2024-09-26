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

namespace App\Types;

use App\Models\Waypoints\Star;
use App\Models\Waypoints\Port;
use App\Models\Waypoints\Planet;
use App\Models\Waypoints\WarpGate;

/**
 * Waypoint Types.
 *
 * At the moment only four of these are implemented: Star, Port, WarpGate and Planet.
 *
 * I foresee Asteroid Fields becoming the new source for Ore, with Ore itself being
 * split into multiple resources. Both Gas and Ice waypoints will provide new
 * commodities for trade with all three being finite resource with optional respawning.
 *
 * Extending ports to have more to them than acting as simple trade posts would add
 * a new dynamic to the game.
 *
 */
enum WaypointType: string
{
    case Star = Star::class;

    // OrbitalStation (Port's in the classic BNT game) can orbit Stars, Planets or Moons
    // TODO: implement orbital station mechanic to replace ports
    case Port = Port::class;

    // Planets orbit Stars
    case Planet = Planet::class;

    // Moons orbit Planets
    case Moon = 'Moon';

    // Asteroid fields can orbit either a Star or a Planet
    case AsteroidField = 'AsteroidField';

    // Gas clouds can orbit a Star
    case Gas = 'Gas';

    // Ice fields can orbit a Star
    case Ice = 'Ice';

    // Warp gates can orbit a Star or a Planet
    case WarpGate = WarpGate::class;
}
