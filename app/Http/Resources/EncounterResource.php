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

use App\Models\Encounter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Encounter
 */
class EncounterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $options = $this->options();

        return [
            'id' => $this->id,
            'type' => basename(str_replace('\\', '/', $this->type->value)),
            ...$this->state,
            'options' => array_reduce(array_keys($options), function($carry, $key) use ($options) {
                $option = $options[$key];
                $carry[$key] = [
                    ...array_filter($option, fn($key) => $key !== 'class', ARRAY_FILTER_USE_KEY),
                    'link' => route('encounter.execute', ['encounter' => $this, 'action' => $key]),
                ];
                return $carry;
            }, []),
        ];
    }

}
