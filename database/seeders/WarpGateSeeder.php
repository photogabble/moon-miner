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

use App\Models\Link;
use App\Types\Graph;
use App\Models\Sector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class WarpGateSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('links')->truncate();

        // Begin by looping through every sector with at least one system and connecting those
        // stars closest to its four edges, with the stars closest to them in adjoining edges.

        $sectors = Sector::hashMap();
        $progressBar = $this->command->getOutput()->createProgressBar(count($sectors));
        $progressBar->start();

        foreach ($sectors as $sector) {
            $edges = $sector->edgeSystems();
            /** @var Sector[] $neighbours */
            $neighbours = [];
            foreach ($sector->neighbourHashes() as $direction => $hash) {
                if (isset($sectors[$hash])) $neighbours[$direction] = $sectors[$hash];
            }

            foreach ($edges as $direction => $system) {
                if (is_null($system)) continue;

                // If I am the easternmost system I want to connect with
                // my neighbours westernmost system.
                $position = opposite_cardinal_direction($direction);

                if (!isset($neighbours[$position])) continue;
                $neighbour = $neighbours[$position];
                $neighbourEdges = $neighbour->edgeSystems();

                if (!isset($neighbourEdges[$position])) continue;
                $destinationSystem = $neighbourEdges[$position];

                Link::createBetween(
                    $system->id,
                    $destinationSystem->id,
                    $system->distanceToPolar($destinationSystem->angle, $destinationSystem->distance)
                );
            }

            // Build a graph of all the systems within this sector and use Prim’s Algorithm to
            // get the Minimum Spanning Tree.

            if ($sector->systems->count() > 1) {
                $edges = [];
                foreach ($sector->systems as $system) {
                    $edges[] = $system;
                }

                $graph = new Graph($sector->systems->count());
                foreach ($edges as $startKey => $start) {
                    foreach ($edges as $endKey => $end) {
                        $graph->addEdge(
                            $startKey,
                            $endKey,
                            $start->distanceToPolar($end->angle, $end->distance)
                        );
                    }
                }

                $parent = $graph->prim();

                for ($i = 1; $i < $graph->vertices; $i++) {
                    $start = $edges[$parent[$i]];
                    $end = $edges[$i];
                    Link::createBetween(
                        $start->id,
                        $end->id,
                        $graph->nodes[$i][$parent[$i]]
                    );
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->command->getOutput()->writeln('');
    }
}
