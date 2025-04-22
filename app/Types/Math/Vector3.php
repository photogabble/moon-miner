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

namespace App\Types\Math;

class Vector3 {
    public function __construct(public int|float $x, public int|float $y, public int|float $z) {}

    public function rotate(Vector3 $vector3): Vector3 {
        // Rotate X: $vector3->x is phi
        if ($vector3->x !== 0) {
            $newY = $this->y * cos($vector3->x) - $this->z * sin($vector3->x);
            $this->z = $this->y * sin($vector3->x) + $this->z * cos($vector3->x);
            $this->y = $newY;
        }

        // Rotate Y: $vector3->y is theta
        if ($vector3->y !== 0) {
            $newX = $this->x * cos($vector3->y) + $this->z * sin($vector3->y);
            $this->z = -$this->x * sin($vector3->y) + $this->z * cos($vector3->y);
            $this->x = $newX;
        }

        // Rotate Z: $vector3->z = psi
        if ($vector3->z !== 0) {
            $newX = $this->x * cos($vector3->z) - $this->y * sin($vector3->z);
            $this->y = $this->x * sin($vector3->z) + $this->y * cos($vector3->z);
            $this->x = $newX;
        }

        return $this;
    }

    public function clamp(float $min, float $max): Vector3
    {
        $this->x = clamp($this->x, $min, $max);
        $this->y = clamp($this->y, $min, $max);
        $this->z = clamp($this->z, $min, $max);

        return $this;
    }
}
