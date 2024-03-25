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

namespace App\Types\Geometry;

use App\Types\QuadTree\Insertable;

/**
 * A point in 2D Space (Polar Coordinate system)
 */
final class PolarPoint implements Insertable
{
    public function __construct(public float $angle, public float $radius) {}

    /**
     * @return Point
     */
    public function toCartesian(): Point
    {
        return new Point(
            cos($this->angle) * $this->radius,
            sin($this->angle) * $this->radius,
        );
    }

    /**
     * Used for adding to QuadTree
     * @return Bounds
     */
    public function toBounds(): Bounds
    {
        $point = $this->toCartesian()
            ->normalised(setting('game.map_size'));

        // Increase width + height to increase zone around star that will be cleared of neighbours
        return new Bounds($point, 10, 10);
    }

    /**
     * Calculate distance between two polar coordinates
     * @param PolarPoint $destination
     * @return float
     */
    public function distanceTo(PolarPoint $destination): float
    {
        return sqrt((pow($this->radius, 2) + pow($destination->radius, 2)) - ((2 * $this->radius * $destination->radius) * cos($this->angle - $destination->angle)));
    }

    /**
     * Returns true if this PolarPoint is identical to the other
     * @param PolarPoint $other
     * @return bool
     */
    public function is(PolarPoint $other): bool
    {
        return $this->angle === $other->angle && $this->radius === $other->radius;
    }
}
