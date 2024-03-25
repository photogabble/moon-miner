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

use App\Models\Sector;

/**
 * A point in 2D Space (Cartesian Coordinate system)
 * This is essentially a 2D Vector...
 */
final class Point
{

    public float $scale = 1.0;

    public function __construct(public float $x, public float $y) {}

    /**
     * Returns the point "normalised" to have (0,0) be the top left of space with the dimensions being
     * set to $size. This is useful when drawing points on an image, or when using with a QuadTree.
     *
     * @param int|float $size
     * @return Point
     */
    public function normalised(int|float $size): Point
    {
        return new Point(
            $this->x > 0 ? ($size / 2) + $this->x * ($size / 2) : 0,
            $this->y > 0 ? ($size / 2) + $this->y * ($size / 2) : 0
        );
    }

    public function subtract(Point $point): Point
    {
        return new Point(
            $this->x - $point->x,
            $this->y - $point->y
        );
    }

    /**
     * Scales the (x,y) values by a given factor.
     * @param float $factor
     * @return $this
     */
    public function scale(float $factor): self
    {
        $this->scale = $factor;
        $this->x *= $factor;
        $this->y *= $factor;

        return $this;
    }

    /**
     * Calculate distance between two Cartesian coordinates using Pythagorean theorem
     * @param Point $destination
     * @return float
     */
    public function distanceTo(Point $destination): float
    {
        return sqrt(pow(($destination->x - $this->x), 2) + pow(($destination->y - $this->y), 2));
    }

    /**
     * Returns the coordinate converted to a Sector hash, this allows for quick look up of which Sector this point
     * is within.
     *
     * The game map is a square plane of dimension `n` (game.map_size), sectors are a square plane of dimension `s`
     * (game.sector_size). This makes the number of sectors in a given axis as n / s. The default map size is 4000
     * and the default sector size is 100, this gives 40 sectors along each axis.
     *
     * The Galaxy generator produces polar coordinates with the radius normalised to be between -1.0 and 1.0, with
     * the origin being in the centre, this is carried over when converting the polar coordinates into the cartesian
     * system giving a (x,y) in the range of -1.0 to 1.0.
     *
     * To obtain the correct sector hash for the game map each point needs scaling by `n` otherwise you will just
     * end up with four sectors for the four quadrants of the circle.
     *
     * @return string
     */
    public function toSectorHash(): string
    {
        $sectorSize = setting('game.sector_size');

        // x.5 needs to become x.0 as we are still within sector x
        $col = intval(floor($this->x / $sectorSize));
        $row = intval(floor($this->y / $sectorSize));

        return Sector::makeHash($col, $row);
    }

    /**
     * Returns true if this Point is identical to the other
     * @param Point $other
     * @return bool
     */
    public function is(Point $other): bool
    {
        return $this->x === $other->x && $this->y === $other->y;
    }
}
