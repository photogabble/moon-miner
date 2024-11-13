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

use Carbon\Carbon;
use App\Models\Scheduler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class ScheduledTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Scheduler $schedule;

    protected float $multiplier = 1;

    public function handle(): void
    {
        $this->schedule = Scheduler::firstOrCreate(
            ['class_name' => static::class],
            ['last_run_at' => Carbon::now()]
        );

        if ($this->schedule->wasRecentlyCreated === false) {
            $diff = Carbon::now()->diffInSeconds($this->schedule->next_run_afterm, true);

            if (isset($this->schedule->next_run_after) && $this->schedule->next_run_after->isPast()) {
                // If running very late, alert.
                if ($diff > $this->periodSeconds() * 2) $this->alert();

                // Work out how many ticks we have missed and set as the multiplier.
                $this->multiplier = 1 + $diff / $this->periodSeconds();
            }
        }

        $runs = ($this->multiplier > 1 && $this->maxCatchup() >= 0)
            ? $this->maxCatchup()
            : floor($this->multiplier);

        while ($runs > 0) {
            $this->run();
            $runs--;
        }

        $this->schedule->update([
            'last_run_at' => Carbon::now(),
            'next_run_after' => Carbon::now()->addMinutes($this->periodMinutes()),
            'multiplier' => $this->multiplier,
        ]);
    }

    protected function alert(): void
    {
        // TODO: log and alert admin
    }

    /**
     * How many minutes should be between runs. This should be identical to what
     * the Scheduler is configured in App\Console\Kernel.
     *
     * @return int
     */
    abstract public function periodMinutes(): int;

    public function periodSeconds(): int
    {
        return $this->periodMinutes() * 60;
    }

    /**
     * If running late, how many times should this task be run to catch up.
     * Set to -1 to be number of runs that can fit within diff.
     *
     * @return int
     */
    public function maxCatchup(): int
    {
        return -1;
    }

    abstract protected function run(): void;
}