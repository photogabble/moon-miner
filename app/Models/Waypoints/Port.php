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
use App\Casts\PortProperties;
use App\Models\Properties\PortProperties as PortPropertiesModel;
use Parental\HasParent;

/**
 * Port Waypoint:
 *
 * As per the classic game, some systems can contain a single port of one type: Special, Energy, Goods
 * Ore and Organics. Organics is important for colonising planets as they are needed to feed the growing
 * population while all trade goods are useful for generating profit when a buyer can be found nearby
 * asking for a good price.
 *
 * My plan for ports is to diversify the game economy beyond four trade goods and to balance ship
 * upgrades through introduction of different ship hulls and installable modules. Then the different
 * hulls and modules may be bought and sold at different ports. For now however, systems with a port
 * will only have one.
 *
 * @property PortPropertiesModel $properties
 */
final class Port extends Waypoint
{
    use HasParent;

    protected $attributes = [
        'name' => 'Port',
    ];

    protected $casts = [
        'properties' => PortProperties::class
    ];
}
