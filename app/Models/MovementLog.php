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


use Carbon\Carbon;
use App\Types\MovementMode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $previous_id
 * @property Carbon $created_at
 * @property int $system_id
 * @property MovementMode $mode
 * @property-read System $sector
 * @property-read MovementLog $previous
 * @property-read Encounter|null $encounter
 */
class MovementLog extends Model
{
    protected $fillable = [
        'previous_id', 'user_id', 'system_id', 'turns_used', 'energy_scooped', 'mode'
    ];

    protected $casts = [
        'mode' => MovementMode::class,
    ];

    public function encounter(): HasOne
    {
        return $this->hasOne(Encounter::class, 'movement_id');
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(System::class, 'sector_id');
    }

    public function previous(): BelongsTo
    {
        return $this->belongsTo(MovementLog::class, 'previous_id');
    }

    public static function writeLog(int $userId, int $systemId, MovementMode $mode = MovementMode::RealSpace, int $turnsUsed = 0, int $energyScooped = 0): MovementLog
    {
        /** @var MovementLog|null $previous */
        $previous = static::query()
            ->select('id')
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->first();

        if ($previous) $previous = $previous->id;

        $movement = static::query()
            ->create([
                'previous_id' => $previous,
                'user_id' => $userId,
                'system_id' => $systemId,
                'mode' => $mode,
                'turns_used' => $turnsUsed,
                'energy_scooped' => $energyScooped,
            ]);

        // Clear Response cache for map pages
        if (Cache::supportsTags()) Cache::tags('galaxy-' . $userId)->flush();

        return $movement;
    }
}
