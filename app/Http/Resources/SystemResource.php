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

namespace App\Http\Resources;

use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin System
 */
class SystemResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sector_id' => $this->sector_id,

            // TODO: implement a method of waypoint discovery, player should be able to see
            //       all public waypoints for the system they are currently in but must scan
            //       down and discover distant systems. For systems the player hasn't
            //       visited and isn't within scan range, the waypoints returned
            //       must be an empty array.
            'waypoints' => WaypointResource::collection($this->whenLoaded('waypoints')),

            // Player meta, this is based upon the players relationship to this system when
            // loaded via System::queryForUser
            'is_current_sector' => $this->is_current_sector,
            'has_visited' => $this->has_visited,
            'has_danger' => $this->has_danger, // TODO: implement danger mechanic
        ];
    }

}
