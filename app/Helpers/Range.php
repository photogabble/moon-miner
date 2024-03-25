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

namespace App\Helpers;

use InvalidArgumentException;

final class Range
{
    public function __construct(public int|float $min, public int|float $max)
    {
        if ($min > $max) throw new InvalidArgumentException('Min value must not be greater than Max value in range');
    }

    public function sum(): int|float
    {
        return $this->min + $this->max;
    }

    public function rand(): int|float
    {
        // mt_rand expects integer input, if we are dealing with floats then they need converting into
        // integers for mt_rand and then back to floats for return.
        if (gettype($this->min) === 'double' || gettype($this->max) === 'double') {
            return mt_rand((int)abs($this->min * 100), (int)abs($this->max * 100)) / 100;
        }

        return mt_rand($this->min, $this->max);
    }
}
