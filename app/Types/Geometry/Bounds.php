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

class Bounds
{
    public function __construct(
        public Point $point,
        public float $width,
        public float $height
    ) {
        //..
    }

    /**
     * Returns true if the given Cartesian is within this bounds.
     * @param Point $point
     * @return bool
     */
    public function containsPoint(Point $point): bool
    {
        return
            $point->x >= $this->point->x && $point->x < ($this->point->x + $this->width)
            && $point->y >= $this->point->y && $point->y < ($this->point->y + $this->height);
    }

    /**
     * Returns true if this bounds contains any points with the other bounds
     * @param Bounds $other
     * @return bool
     */
    public function intersects(Bounds $other): bool
    {
        return $this->point->x <= $other->point->x + $other->width
            && $other->point->x <= $this->point->x + $this->width
            && $this->point->y <= $other->point->y + $other->height
            && $other->point->y <= $this->point->y + $this->height;
    }

    /**
     * Returns true if this bounds is identical to the other bounds.
     * @param Bounds $other
     * @return bool
     */
    public function is(Bounds $other): bool
    {
        return $this->point->is($other->point)
            && $this->width === $other->width
            && $this->height === $other->height;
    }

    /**
     * Returns the center coordinate of this bounds.
     * @return Point
     */
    public function getCenter(): Point
    {
        $x = $this->point->x + ($this->width / 2);
        $y = $this->point->y + ($this->height / 2);
        return new Point($x, $y);
    }
}
