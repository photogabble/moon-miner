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

use App\Models\GameConfig;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        $cacheKey = "game-setting.$key";
        if (Cache::has($cacheKey)) return Cache::get($cacheKey);

        if (!$value = GameConfig::findByKey($key)) {
            $value = config($key, $default);
        }

        Cache::forever($cacheKey, $value);
        return $value;
    }
}

if (!function_exists('render_time_seconds')) {
    function render_time_seconds(): float
    {
        return round(microtime(true) - LARAVEL_START, 2);
    }
}

if (!function_exists('mem_peak_usage')) {
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
    function array_from_enum(array $cases): array
    {
        return array_map(fn($e) => $e->value, $cases);
    }
}

if (!function_exists('scale')) {
    function scale(int|float $minFrom, int|float $maxFrom, int|float $minTo, int|float $maxTo, int|float $value): int|float
    {
        return $minTo + ($maxTo - $minTo) * (($value - $minFrom) / ($maxFrom - $minFrom));
    }
}

function opposite_cardinal_direction(string $direction): string
{
    if ($direction === 'n') return 's';
    if ($direction === 'ne') return 'sw';
    if ($direction === 'e') return 'w';
    if ($direction === 'se') return 'nw';
    if ($direction === 's') return 'n';
    if ($direction === 'sw') return 'ne';
    if ($direction === 'w') return 'e';
    if ($direction === 'nw') return 'se';
}

/**
 * Assuming that the game map is 100,000ly across, and knowing that distances calculated from the polar coordinates
 * will be between 0.0-1.0 on the same scale as the map, therefore they can be used as a fraction of 100,000ly.
 *
 * @param float $distance
 * @return float
 */
function distance_to_ly(float $distance): float
{
    return 100000 * $distance;
}

function polar_to_name(float $angle, float $distance): string
{
    if ($angle < -M_PI || $angle > M_PI) throw new InvalidArgumentException('Angle must be between π and -π');

    $angle = ($angle + M_PI) * (180 / M_PI);

    if ($angle >= 0 && $angle <= 30) {
        $ident = 'A';
    } else if ($angle > 30 && $angle <= 60) {
        $ident = 'B';
    } else if ($angle > 60 && $angle <= 90) {
        $ident = 'C';
    } else if ($angle > 90 && $angle <= 120) {
        $ident = 'D';
    } else if ($angle > 120 && $angle <= 150) {
        $ident = 'E';
    } else if ($angle > 150 && $angle <= 180) {
        $ident = 'F';
    } else if ($angle > 180 && $angle <= 210) {
        $ident = 'G';
    } else if ($angle > 210 && $angle <= 240) {
        $ident = 'H';
    } else if ($angle > 240 && $angle <= 270) {
        $ident = 'I';
    } else if ($angle > 270 && $angle <= 300) {
        $ident = 'J';
    } else if ($angle > 300 && $angle <= 330) {
        $ident = 'J';
    } else if ($angle > 330 && $angle <= 360) {
        $ident = 'K';
    } else {
        $ident = 'X';
    }

    if ($distance >= 0.8) {
        $ident = $ident . '2';
    } else if ($distance < 0.8 && $distance > 0.3) {
        $ident = $ident . '0';
    } else {
        $ident = $ident . '1';
    }

    return $ident;
}
