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

use App\Types\LogType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'data',
    ];

    protected $casts = [
        'type' => LogType::class,
    ];

    public function selectLogsInfo(int $user_id, string $startdate): Collection
    {
        return PlayerLog::where('user_id', $user_id)
            ->where('created_at', 'LIKE', "$startdate%")
            ->get();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function writeLog(int $user_id, LogType $log_type, ?string $data = null): void
    {
        static::query()
            ->create([
                'user_id' => $user_id,
                'type' => $log_type,
                'data' => (!is_null($data)) ? addslashes($data) : '',
            ]);
    }
}
