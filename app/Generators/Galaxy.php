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

namespace App\Generators;

use App\Models\System;
use App\Types\SpectralType;
use App\Types\Geometry\Point;
use App\Models\Waypoints\Star;
use App\Types\Geometry\Bounds;
use App\Types\QuadTree\QuadTree;
use App\Types\Geometry\PolarPoint;
use App\Helpers\PerlinNoiseGenerator;

/**
 * 2D Procedural Galaxy in PHP
 * https://itinerantgames.tumblr.com/post/78592276402/a-2d-procedural-galaxy-with-c
 *
 * Research:
 * @see https://www.reddit.com/r/gamedev/comments/20ach8/how_to_generate_star_positions_in_a_2d_procedural/
 * @see https://martindevans.me/game-development/2016/01/14/Procedural-Generation-For-Dummies-Galaxies/
 *
 * @see https://www.reddit.com/r/proceduralgeneration/comments/boxzd9/procedural_star_system_maps/
 */
readonly class Galaxy
{
    public function __construct(
        private PerlinNoiseGenerator $generator,
        private int                  $maxStars = 4700,
        private int                  $numArms = 3,
        private int                  $rotationFactor = 4, //6,
        private float                $randomOffsetXY = 0.06
    )
    {
    }

    /**
     * @return PolarPoint[]
     */
    public function generate(): array
    {
        $armSeparationDistance = 2 * M_PI / $this->numArms;
        $armOffsetMax = 0.9;

        /** @var PolarPoint[] $points */
        $points = [];

        $minDistance = 100;
        $maxDistance = 0;

        for ($i = 0; $i < $this->maxStars; $i++) {
            // Choose a distance from the center of the galaxy.
            $distance = 0.07 + pow($this->randFloat(), 2);

            // Choose an angle between 0 and 2 * PI.
            $angle = $this->randFloat() * 2 * M_PI;

            // Make the arms of the Galaxy structure
            $armOffset = $this->randFloat() * $armOffsetMax;
            $armOffset = $armOffset - $armOffsetMax / 2;

            // Widen the arms so they don't pinch in at the centre.
            $armOffset = $armOffset * (1 / $distance);

            // Draw stars into the central arc of each arm.
            $squaredArmOffset = pow($armOffset, 2);
            if ($armOffset < 0) {
                $squaredArmOffset = $squaredArmOffset * -1;
            }
            $armOffset = $squaredArmOffset;

            // Add some spin
            $rotation = $distance * $this->rotationFactor;

            // Add random offset to make arm density more diffuse.
            $offset = $this->randFloat() * $this->randomOffsetXY;
            if ($this->randFloat() > .5) {
                $offset *= -1;
            }

            $distance += $offset;
            $angle = (int)($angle / $armSeparationDistance) * $armSeparationDistance + $armOffset + $rotation + $offset;

            // Due to how the Galaxy is formed the angle ends up somewhere in the range of -45 to +45
            // the simplest method I can think of to normalise this to +/- PI is converting to
            // cartesian and back to polar.
            //
            // polar to cartesian: x = r × cos( θ ) and y = r × sin( θ )
            // cartesian to polar: r = √(x² + y²) and θ = atan2(y, x)

            $x = $distance * cos($angle);
            $y = $distance * sin($angle);

            $distance = sqrt(pow($x, 2) + pow($y, 2));
            $angle = atan2($y, $x);

            $points[] = new PolarPoint($angle, $distance);

            if ($distance < $minDistance) $minDistance = $distance;
            if ($distance > $maxDistance) $maxDistance = $distance;
        }

        // Normalise all distances to range between $minDistance and 1.0. The reason why its min distance and
        // not zero is because stars have been moved away from the center where a galactic black hole would
        // exist and ignoring that ends up with all those stars getting bunched at zero.
        foreach ($points as $point) {
            $point->radius = scale($minDistance, $maxDistance, $minDistance, 1.0, $point->radius);
        }

        return $points;

        // NOTE: The QuadTree doesn't work correctly, and I am thinking of using the sector hash map to cull
        // systems anyway.

        // Using a QuadTree, cull any systems too close to one another.
//        $tree = new QuadTree(new Bounds(new Point(0, 0,), setting('game.map_size'), setting('game.map_size')));
//        foreach ($points as $point) {
//            $tree->insert($point);
//        }
//
//        return $tree->getItems();
    }

    /**
     * Return a number between 0 and 1;
     * @return float
     */
    private function randFloat(): float
    {
        return ((float)mt_rand() / (float)mt_getrandmax());
    }
}
