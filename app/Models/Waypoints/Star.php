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
use App\Types\WaypointType;
use App\Casts\StarProperties;
use App\Models\Properties\StarProperties as StarPropertiesModel;
use Parental\HasParent;

/**
 * Star Waypoint:
 *
 * This is intended more for story telling than as a gameplay mechanic. Each system having one or more
 * stars with each star having its own attributes aims to give the game map some variety and a feel of
 * adventure. Eventually I foresee stars being anchor points for player owned structures and maybe
 * even a source of trade goods.
 *
 * @property StarPropertiesModel $properties
 */
final class Star extends Waypoint
{
    use HasParent;

    protected $attributes = [
        'name' => 'Star',
    ];

    protected function casts(): array
    {
        return [
            ...parent::casts(),
            'properties' => StarProperties::class
        ];
    }
}
