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

namespace App\View\Components;

use Closure;
use Carbon\Carbon;
use App\Models\User;
use App\Models\News;
use App\Models\Scheduler;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Refactored from the legacy footer_t.php file.
 */
class Footer extends Component
{
    public function render(): View|Closure|string
    {
        $secondsLeft = 0;
        if ($lastRun = Scheduler::selectSchedulerLastRun()) {
            $secondsLeft = floor($lastRun->diffInSeconds(Carbon::now()) - (config('scheduler.sched_ticks') * 60));
        }

        $newsTicker = [];

        $news = News::selectNewsByDay(Carbon::now());

        if ($news->count() === 0) {
            $newsTicker[] = [
                'url' => null,
                'text' => __('news.l_news_none'),
                'type' => null,
                'delay' => 5,
            ];
        } else {
            foreach($news as $item) {
                $newsTicker[] = [
                    'url' => '#', // TODO: route('news');
                    'text' => $item->headline,
                    'type' => $item->type,
                    'delay' => 5,
                ];
            }

            $newsTicker[] = [
                'url' => null,
                'text' => 'End of News', // TODO: add to lang files
                'type' => null,
                'delay' => 5,
            ];
        }

        return view('components.footer', [
            'news' => $newsTicker,
            'suppress_logo' => false,
            'footer_show_debug' => true,
            'update_ticker' => [
                'display' => !is_null($lastRun),
                'seconds_left' => $secondsLeft,
                'sched_ticks' => config('scheduler.sched_ticks')
            ],
            'players_online' => User::activePlayerCount(),
        ]);
    }
}
