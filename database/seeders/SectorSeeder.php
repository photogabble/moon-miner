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


namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        // Total sectors is this value squared.
        $sectorsInAxis = setting('game.map_size') / setting('game.sector_size');

        // Both Axis have a negative axis with zero point being center of map. For example
        // if $sectorsInAxis is 40 then we will go from -19 through zero to 19 giving a
        // total of 1,600 sectors (40x40).

        // A 5x5 grid will look like the following:
        //        0        1       2        3        4
        // 0  [-2,  2] [-1,  2] [0,  2], [1,  2], [2,  2]
        // 1  [-2,  1] [-1,  1] [0,  1], [1,  1], [2,  1]
        // 2  [-2,  0] [-1,  0] [0,  0], [1,  0], [2,  0]
        // 3  [-2, -1] [-1, -1] [0, -1], [1, -1], [2, -1]
        // 4  [-2, -2] [-1, -2] [0, -2], [1, -2], [2, -2]

        DB::beginTransaction();

        $half = intval(floor($sectorsInAxis / 2));

        for ($row = 0; $row < $sectorsInAxis; $row++) {
            for ($column = 0; $column < $sectorsInAxis; $column++) {
                Sector::create([
                    'x' => $sectorsInAxis - ($half + $row) - 1,
                    'y' => $sectorsInAxis - ($half + $column) - 1,
                ]);
            }
        }

        DB::commit();
    }
}
