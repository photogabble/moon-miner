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

namespace App\Types;

/**
 * @author Milad <https://github.com/miladev95>
 * @see https://medium.com/@miladev95/prims-algorithm-for-minimum-spanning-trees-explained-with-php-example-783974a75235
 */
class Graph
{
    public int $vertices;
    public array $nodes;

    public function __construct(int $vertices) {
        $this->vertices = $vertices;
        $this->nodes = array_fill(0, $vertices, array_fill(0, $vertices, 0.0));
    }

    public function addEdge(int $start, int $end, float $weight): void
    {
        $this->nodes[$start][$end] = $weight;
        $this->nodes[$end][$start] = $weight;
    }

    public function prim(): array
    {
        $parent = array_fill(0, $this->vertices, -1);
        $key = array_fill(0, $this->vertices, PHP_INT_MAX);
        $inMST = array_fill(0, $this->vertices, false);

        $key[0] = 0;

        for ($count = 0; $count < $this->vertices - 1; $count++) {
            $u = $this->minKey($key, $inMST);

            $inMST[$u] = true;

            for ($v = 0; $v < $this->vertices; $v++) {
                if ($this->nodes[$u][$v] != 0 && !$inMST[$v] && $this->nodes[$u][$v] < $key[$v]) {
                    $parent[$v] = $u;
                    $key[$v] = $this->nodes[$u][$v];
                }
            }
        }

        return $parent;
    }

    private function minKey($key, $inMST): int
    {
        $min = PHP_INT_MAX;
        $minIndex = -1;

        for ($v = 0; $v < $this->vertices; $v++) {
            if (!$inMST[$v] && $key[$v] < $min) {
                $min = $key[$v];
                $minIndex = $v;
            }
        }

        return $minIndex;
    }
}
