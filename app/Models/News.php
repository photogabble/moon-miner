<?php declare(strict_types=1);
/**
 * Blacknova Traders, a Free & Opensource (FOSS), web-based 4X space/strategy game.
 *
 * @copyright 2024 Simon Dann, Ron Harwood and the BNT development team
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $headline
 * @property string $body
 * @property int|null $user_id
 * @property string $type
 */
class News extends Model
{
    public static function alreadyPublished(int $user_id, string $type): bool
    {
        return News::query()
                ->where('user_id', $user_id)
                ->where('news_type', $type)
                ->count() > 0;
    }

    /**
     * @param Carbon $day
     * @return Collection<News>
     */
    public static function selectNewsByDay(Carbon $day): Collection
    {
        // SQL call that selects all of the news items between the start date beginning of day, and the end of day.
        return News::query()
            ->whereBetween('created_at', [$day->startOfDay(), $day->endOfDay()])
            ->get();
    }
}
