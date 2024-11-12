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
use App\Models\Waypoint;
use App\Types\WaypointType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Waypoint
 */
class WaypointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $properties = $this->properties->toArray();

        if ($this->type === WaypointType::WarpGate && isset($this->properties->destination_system_id) && $dest = System::find($this->properties->destination_system_id)) {
            $properties['destination_system_name'] = $dest->name;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => basename(str_replace('\\', '/', $this->type->value)),
            'primary_id' => $this->primary_id,
            'properties' => $properties,

            // Waypoints can orbit other waypoints, this will normally only be loaded when
            // viewing a waypoint in detail, for example when viewing a planet.
            'waypoints' => WaypointResource::collection($this->whenLoaded('orbitals')),
            'system' => new SystemResource($this->whenLoaded('system')),

            // A Waypoints orbit is within context of it's primary. At time of writing all
            // systems will have a single star with a null primary_id, around which planets
            // will orbit, each planet will then have a chance of spawning a station and
            // one or more moons in orbit. I have included both eccentricity and inclination
            // however they are more for story telling, not used for displaying or
            // calculating distance.
            'orbit' => [
                'angle' => $this->angle,
                'distance' => $this->distance,
                'eccentricity' => $this->eccentricity,
                'inclination' => $this->inclination,
            ],
        ];
    }
}
