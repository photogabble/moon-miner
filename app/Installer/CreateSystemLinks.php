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


namespace App\Installer;

use Exception;
use App\Models\Link;
use App\Types\Graph;
use App\Models\Sector;
use App\Models\System;
use App\Models\Waypoint;
use App\Types\WaypointType;
use App\Types\InstallConfig;
use App\Models\Waypoints\Planet;
use App\Models\Waypoints\WarpGate;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Eloquent\Collection;

// Last half of 70.php

class CreateSystemLinks extends Step implements InstallStep
{

    /**
     * @throws Exception
     */
    public function execute(OutputStyle $output, InstallConfig $config): int
    {
        $this->timer->start();

        // Begin by looping through every sector with at least one system and connecting those
        // stars closest to its four edges, with the stars closest to them in adjoining edges.
        // This will connect sectors together.

        $sectors = Sector::hashMap();
        $progressBar = $output->createProgressBar(count($sectors));
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

                try {
                    Link::createBetween(
                        $system->id,
                        $destinationSystem->id,
                        $system->distanceToPolar($destinationSystem->angle, $destinationSystem->distance)
                    );
                } catch (Exception $e) {
                    // Will throw an exception if trying to create two identical links. This can be
                    // ignored. However, it shouldn't really happen, some investigation needed.
                }
            }

            // Build a graph of all the systems within this sector and use Prim’s Algorithm to
            // get the Minimum Spanning Tree. Then interconnect them so that all systems within
            // a sector are linked by at least one path.

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
        $output->getOutput()->writeln('');

        // With all the system links generated WarpGate structures can be spawned around planets to allow
        // navigation between systems. If a System has no planets, the WarpGate can be spawned in orbit
        // around the Star.

        /** @var Collection<System> $systems */
        $systems = System::with(['links', 'links.rightSystem', 'waypoints'])->get();

        // 15 degrees to radians
        $thresholdRadians = 15 * (pi() / 180);

        foreach ($systems as $system) {
            $star = $system->waypointsOfType(WaypointType::Star)->first();
            $planets = $system->waypointsOfType(WaypointType::Planet);
            $systemCoords = $system->toCartesian();

            // Get all links within this system and calculate the angle from system centre to the destination
            // system. The planet closest within fifteen degrees will be what the warp gate orbits, otherwise
            // we will spawn them orbiting the star.
            foreach ($system->links as $link) {
                // Easiest to calculate angle between system and link destination is to convert both to
                // the cartesian coordinate system...
                $angle = $systemCoords->angleBetween($link->rightSystem->toCartesian());

                $warpGate = new WarpGate();
                $warpGate->system_id = $system->id;
                $warpGate->properties->destination_system_id = $link->right_system_id;
                $warpGate->eccentricity = 0.0;
                $warpGate->inclination = 0.0;

                $warpGate->angle = $angle;

                // When orbiting a star, distance is in AU, when orbiting a planet its in km
                $warpGate->distance = 1; // TODO: whats the min/max of this?

                // Find a planet within +/- 15 degrees of this angle in order for the warp gate to be placed into
                // orbit, else use the angle to
                if ($planet = $planets->filter(function(Planet $planet) use ($angle, $thresholdRadians){
                    return
                        $planet->angle <= ($angle + $thresholdRadians) &&
                        $planet->angle >= ($angle - $thresholdRadians);
                })->first()) {
                    $warpGate->primary_id = $planet->id;
                } else {
                    $warpGate->primary_id = $star->id;
                }

                $warpGate->save();
            }
        }

        return 0;
    }
}
