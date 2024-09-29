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

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $link_hash
 * @property int $left_system_id
 * @property int $right_system_id
 * @property float $distance
 *
 * @property-read System $leftSystem
 * @property-read System $rightSystem
 */
class Link extends Model
{
    public function leftSystem(): BelongsTo
    {
        return $this->belongsTo(System::class, 'left_system_id');
    }

    public function rightSystem(): BelongsTo
    {
        return $this->belongsTo(System::class, 'right_system_id');
    }

    /**
     * Create a bilateral link between two systems.
     *
     * @param int $startId
     * @param int $endId
     * @param float $distance
     * @return bool
     */
    public static function createBetween(int $startId, int $endId, float $distance): bool
    {
        $left = new Link();
        $left->left_system_id = $startId;
        $left->right_system_id = $endId;
        $left->distance = $distance;
        $left->link_hash = "$startId-$endId";

        $right = new Link();
        $right->left_system_id = $endId;
        $right->right_system_id = $startId;
        $right->distance = $distance;
        $right->link_hash = "$endId-$startId";

        return $left->save() && $right->save();
    }
}
