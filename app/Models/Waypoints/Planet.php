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

namespace App\Models\Waypoints;

use App\Models\Waypoint;
use App\Casts\CelestialProperties;
use App\Models\Traits\ShipsCanDock;
use App\Models\Properties\CelestialProperties as CelestialPropertiesModel;
use Parental\HasParent;

/**
 * Planet Waypoint:
 *
 * Systems can be seeded with a number of planets, in the classic game there
 * was no differentiation between planets beyond the production values set
 * by a player upon capture.
 *
 * I have extended planets with CelestialProperties to give them some variety,
 * my intention is to diversify the games economy with additional trade goods
 * obtained from planetary industry; this will then give the different planet
 * types, different products to manufacture.
 *
 * Unowned planets can be "captured" by players, while owned planets may be
 * attacked by players.
 *
 * With enough resources a base can be built on a planet; this allows for
 * remote modification of that planets production without needing to be
 * within the same system.
 *
 * In the legacy game a planet can be toggled as selling commodities, this
 * allows for trading Ore, Organics, Goods and Energy between the planet
 * and a port. This does however also allow other players to trade with
 * the planet and in the classic game this allows for the tactic where
 * an attacking player can buy all the energy from a planet, disabling
 * its defenses and then attack.
 *
 * My intention for planets is to allow players to build structures, those
 * structures will determine the production of a planet with different
 * planet types being better or worse at production of certain commodities.
 *
 * - Base, provides additional defensive capabilities. Having control of >=
 *   50% of the planets within a system and a base on each will give
 *   the player control over that system.
 * - Market Hub, enables buying and selling commodities unlike with the
 *   classic game the player can place buy/sell limits
 * - Space Dock, enables construction and maintenance of additional ships
 *
 * @property int $prod_ore
 * @property int $prod_organics
 * @property int $prod_goods
 * @property int $prod_energy
 * @property int $prod_fighters
 * @property int $prod_torp
 * @property CelestialPropertiesModel $properties
 */
final class Planet extends Waypoint
{
    use HasParent, ShipsCanDock;

    protected $attributes = [
        'name' => 'Planet',
    ];

    protected $casts = [
        'properties' => CelestialProperties::class
    ];
}
