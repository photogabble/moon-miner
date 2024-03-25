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

namespace App\Models\Traits;

use App\Types\Geometry\Point;
use App\Types\Geometry\Bounds;

/**
 * @property float $angle
 * @property float $distance
 */
trait HasPolarCoordinates {
    /**
     * Return waypoints radial co-ordinates as cartesian (x,y)
     * @return Point
     */
    public function toCartesian(): Point
    {
        return new Point(
            cos($this->angle) * $this->distance,
            sin($this->angle) * $this->distance
        );
    }

    /**
     * Used for adding to QuadTree
     * @return Bounds
     */
    public function toBounds(): Bounds
    {
        $size = setting('game.map_size');

        $position = $this->toCartesian();

        $x = $size / 2 + $position->x * ($size / 2);
        $y = $size / 2 + $position->y * ($size / 2);

        // Increase width + height to increase zone around star that will be cleared of neighbours
        return new Bounds(new Point($x, $y), 10, 10);
    }

    /**
     * Returns distance to polar coordinates from this, without needing to first convert into cartesian.
     *
     * @see https://www.kristakingmath.com/blog/distance-between-polar-points
     * @param float $angle
     * @param float $distance
     * @return float
     */
    public function distanceToPolar(float $angle, float $distance): float
    {
        $a1 = $this->angle;
        $a2 = $angle;
        $r1 = $this->distance;
        $r2 = $distance;

        return sqrt((pow($r1, 2) + pow($r2, 2)) - ((2 * $r1 * $r2) * cos($a1 - $a2)));
    }
}
