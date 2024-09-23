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

use App\Models\Ship;
use App\Helpers\CalcLevels;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ship
 */
class ShipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // TODO: Add different ship hulls that have different maximums of the below levels to balance things out a bit
        $fittingLvs = [
            'hull' => $this->hull,
            'engines' => $this->engines,
            'power' => $this->power,
            'computer' => $this->computer,
            'sensors' => $this->sensors,
            'armor' => $this->armor,
            'shields' => $this->shields,
            'beams' => $this->beams,
            'torp_launchers' => $this->torp_launchers,
            'cloak' => $this->cloak,
        ];

        $avgFittingLv = $this->avgTechLevel();

        if ($avgFittingLv < 8) {
            $shipLv = 0;
        } else if ($avgFittingLv < 12) {
            $shipLv = 1;
        } else if ($avgFittingLv < 16) {
            $shipLv = 2;
        } else if ($avgFittingLv < 20) {
            $shipLv = 3;
        } else {
            $shipLv = 4;
        }

        return [
            'name' => $this->name,
            'system_id' => $this->system_id,
            // TODO, $this->whenLoaded(System)
            'level' => $shipLv,

            'armor' => [
                'current' => $this->armor_pts,
                'max' => CalcLevels::maxLevels($this->armor),
            ],

            'weapons' => [
                'fighters' => [
                    'current' => $this->ship_fighters,
                    'max' => CalcLevels::maxLevels($this->computer),
                ],
                'torpedoes' => [
                    'current' => $this->torps,
                    'max' => CalcLevels::maxLevels($this->torp_launchers),
                ],
            ],

            // TODO: Add Inventory mechanic, different ship hulls can specialise between the four resource types e.g mining barge gets a 200% bonus to ore stored per hold
            'cargo_holds' => [
                'ore' => $this->ship_ore,
                'organics' => $this->ship_organics,
                'goods' => $this->ship_goods,
                'colonists' => $this->ship_colonists,
                'holds_used' => $this->ship_ore +$this->ship_organics + $this->ship_goods + $this->ship_colonists,
                'holds_max' => CalcLevels::maxLevels($this->hull),
            ],

            'energy' => [
                'current' => $this->ship_energy,
                'max' => CalcLevels::energy($this->power),
            ],

            // TODO: Add ship hard/soft points, for installing modules
            'devices' => [
                'beacon' => $this->dev_beacon,
                'warp_edit' => $this->dev_warpedit,
                'genesis' => $this->dev_genesis,
                'mine_deflector' => $this->dev_minedeflector,
                'emergency_warp' => $this->dev_emerwarp,
                'lssd_installed' => $this->dev_lssd, // last ship seen device
                'escape_pod_installed' => $this->dev_escapepod,
                'fuel_scoop_installed' => $this->dev_fuelscoop,
            ],

            'fitting' => $fittingLvs,
            'avg_fitting_level' => $avgFittingLv,
        ];
    }
}
