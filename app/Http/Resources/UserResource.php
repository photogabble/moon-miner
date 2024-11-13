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

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lang' => $this->lang,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,

            'name' => $this->name,
            'turns' => $this->turns,
            'turns_used' => $this->turns_used,
            'credits' => $this->wallet()->balance,
            'score' => $this->score,

            'ship_id' => $this->ship_id, // Will be null if player isn't occupying a ship
            'ship' => new ShipResource($this->whenLoaded('ship')),
            'current_encounter' => new EncounterResource($this->whenLoaded('currentEncounter')),
            // 'presets' => UserPresetResource::collection($this->whenLoaded('presets')),
            // 'movement_log' => MovementLogResource::collection($this->whenLoaded('movementLog')),
        ];
    }
}
