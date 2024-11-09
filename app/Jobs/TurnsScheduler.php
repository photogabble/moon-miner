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

namespace App\Jobs;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class TurnsScheduler extends ScheduledTask
{
    public function periodMinutes(): int
    {
        return 2;
    }

    public function maxCatchup(): int
    {
        return 1;
    }

    protected function run(): void
    {
        Log::info(__('scheduler.l_sched_turns_title'));

        User::query()
            ->where('turns', '<', config('game.max_turns'))
            ->update([
                'turns' => DB::connection()->getDriverName() === 'sqlite'
                    ? DB::raw('MIN(turns + '. config('game.turns_per_tick') .', '.config('game.max_turns').')')
                    : DB::raw('LEAST(turns + '. config('game.turns_per_tick') .', '.config('game.max_turns').')')
            ]);
    }
}
