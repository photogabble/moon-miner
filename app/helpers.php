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

use App\Models\GameConfig;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    function setting(string $key, $default = null) {
        $cacheKey = "game-setting.$key";
        if (Cache::has($cacheKey)) return Cache::get($cacheKey);

        if (!$value = GameConfig::findByKey($key)) {
            $value = config($key, $default);
        }

        Cache::forever($cacheKey, $value);
        return $value;
    }
}

if (! function_exists('render_time_seconds')) {
    function render_time_seconds(): float
    {
        return round(microtime(true) - LARAVEL_START, 2);
    }
}

if (! function_exists('mem_peak_usage')) {
    function mem_peak_usage(): float
    {
        return floor(memory_get_peak_usage() / 1024);
    }
}

if (!function_exists('array_from_enum')) {
    /**
     * @param UnitEnum[] $cases
     * @return array
     */
    function array_from_enum(array $cases) : array {
        return array_map(fn($e) => $e->value, $cases);
    }
}
