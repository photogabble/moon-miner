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

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\System;
use App\Models\Sector;
use App\Types\WaypointType;
use Illuminate\Http\Request;
use App\Models\Waypoints\WarpGate;
use Illuminate\Support\Collection;
use App\Actions\Movement\WarpJump;
use App\Actions\Movement\WarpRoute;
use Illuminate\Http\RedirectResponse;

class MapController extends Controller
{
    /**
     * GET: /sector-map/{?sector:id}
     *
     * When no sector id is provided this returns a grid of sectors, with information varying
     * on how much the player has explored: sectors are unlocked by visiting systems within
     * them.
     *
     * When a sector is provided, if the player hasn't visited a system within it then this
     * will return a 404. Otherwise, it will return details of the systems to be found
     * within the given sector.
     *
     * An idea could be to use sectors as "zoom levels", the game map could be split into four
     * and then each division subdivided to a max level akin to a quad tree.
     *
     * @param int|null $sector
     * @return Response
     */
    public function sector(?int $sector = null): Response
    {
        $id = $sector ?? $this->user->ship->system->sector_id;

        $visitedSystems = $this->user->visitedSystems();
        $knownSystems = $this->user->knownSystems();

        $visited = count($visitedSystems);
        $known = count($knownSystems);
        $discoveredPercentage = round($known / System::count() * 100, 2);

        /** @var Sector $sector */
        if (!$sector = Sector::find($id)) abort(404);

        $sector->load('systems', 'systems.waypoints');

        $systemsInSector = $sector->systems->reduce(function ($systems, System $system) {
            $systems[] = $system->id;
            return $systems;
        }, []);

        // Sector Position is used to get system and path (x,y) coordinates relative to the top left
        // of the sector square plane, as opposed to the galactic square plane.
        $sectorPosition = $sector->position();

        // Find Links that go outside this sector
        // Knowing the $systemsInSector we can create a list of external systems that systems within this
        // sector link to. They will then be displayed as links to their sector.
        $externalSystems = System::query()
            ->whereIn('id',
                $sector->systems->reduce(function ($links, System $system) use ($systemsInSector, $sectorPosition) {
                    /** @var WarpGate $waypoint */
                    foreach ($system->waypointsOfType(WaypointType::WarpGate) as $waypoint) {
                        if (!$waypoint->properties->destination_system_id) continue;
                        if (!in_array($waypoint->properties->destination_system_id, $systemsInSector)) {
                            $links[] = $waypoint->properties->destination_system_id;
                        }
                    }
                    return $links;
                }, [])
            )->get();

        $systemsPlayerIsAwareOf = [...array_keys($visitedSystems), ...$knownSystems];

        // Systems that can be reached via WarpGates in the system the player resides.
        $navigableSystems = $this->user->ship->system
            ->waypointsOfType(WaypointType::WarpGate)
            ->reduce(function (array $carry, WarpGate $waypoint) {
                $carry[$waypoint->properties->destination_system_id] = $waypoint;
                return $carry;
            }, []);

        $autopilot = null;
        $route = new WarpRoute($this->user->ship);
        if ($route->load()) {

            $systems = System::query()
                ->whereIn('id', $route->systemIds)
                ->get()
                ->reduce(function(array $carry, System $system) {
                    $carry[$system->id] = $system;
                    return $carry;
                }, []);

            $path = [];
            for ($i = 0; $i < count($route->systemIds); $i++) {
                /** @var System $system */
                $system = $systems[$route->systemIds[$i]];

                if ($system->id === $this->user->ship->system_id) {
                    $label = "[$system->name]";
                } else {
                    $label = $system->name;
                }

                if ($previous = $route->systemIds[$i - 1] ?? null) {
                    $previous = $systems[$previous];
                    if ($system->sector_id !== $previous->sector_id) {
                        $label .= " ($system->sector_id)";
                    }
                }

                $path[] = $label;
            }

            $autopilot = [
                'on_route' => $route->contains(),
                'remaining_systems' => $route->remaining(),
                'next_system_id' => $route->next(),
                'path' => $path,
            ];
        }

        return Inertia::render('NaviCom/SectorMap', [
            'stats' => [
                'visited' => $visited,
                'known' => $known,
                'discovery_percentage' => $discoveredPercentage,
            ],
            'autopilot' => $autopilot,
            'sector' => $sector,
            'systems' => $externalSystems
                ->merge($sector->systems)
                ->map(function (System $system) use ($sectorPosition, $systemsInSector, $visitedSystems, $knownSystems, $navigableSystems) {
                    $actions = [
                        [
                            'title' => 'Info',
                            'href' => route('navicom.system', $system),
                        ]
                    ];
                    if (isset($navigableSystems[$system->id])) {
                        $actions[] = [
                            'title' => 'Jump',
                            'method' => 'post',
                            'href' => route('ship.travel-through.gate', [
                                'ship' => $this->user->ship,
                                'gate' => $navigableSystems[$system->id],
                            ]),
                        ];
                    } else if ($system->id !== $this->user->ship->system_id) {
                        $actions[] = [
                            'title' => 'Plot Course',
                            'href' => route('navicom.plan-route', $system),
                        ];
                    }

                    if ($system->sector_id !== $this->user->ship->system->sector_id) {
                        $actions[] = [
                            'title' => 'View Sector',
                            'href' => route('sector-map', $system->sector_id),
                        ];
                    }

                    return [
                        'id' => $system->id,
                        'name' => $system->name,
                        'sector_id' => $system->sector_id,
                        'coords' => $system->position()->subtract($sectorPosition),
                        'is_internal' => in_array($system->id, $systemsInSector),

                        'is_next_door' => isset($navigableSystems[$system->id]),
                        'is_current_system' => $system->id === $this->user->ship->system_id,
                        'has_visited' => isset($visitedSystems[$system->id]),
                        'has_knowledge' => in_array($system->id, $knownSystems),

                        'actions' => $actions,
                    ];
                }),
            'links' => $sector->systems
                ->filter(function (System $system) use ($systemsPlayerIsAwareOf) {
                    // Only provide links for systems the player is aware of
                    return in_array($system->id, $systemsPlayerIsAwareOf);
                })
                ->reduce(function (Collection $links, System $system) use ($systemsInSector, $sectorPosition, $visitedSystems, $route) {
                    /** @var WarpGate $waypoint */
                    foreach ($system->waypointsOfType(WaypointType::WarpGate) as $waypoint) {
                        if (!$waypoint->properties->destination_system_id) continue;

                        $hash = [$system->id, $waypoint->properties->destination_system_id];
                        sort($hash);
                        $hash = implode('-', $hash);

                        if (isset($links[$hash])) continue;

                        // TODO preload linked systems

                        $destination = System::find($waypoint->properties->destination_system_id);

                        $links[$hash] = [
                            'from' => $system->position()->subtract($sectorPosition),
                            'to' => $destination->position()->subtract($sectorPosition),
                            'is_internal' => in_array($waypoint->properties->destination_system_id, $systemsInSector),
                            'is_route' => in_array($hash, $route->linkHashes),
                            'has_visited' => isset($visitedSystems[$system->id]) || isset($visitedSystems[$destination->id]),
                        ];
                    }

                    return $links;
                }, new Collection())->values(),
        ]);
    }

    /**
     * GET: /sector-map/plot-course/{system:id}
     *
     * This is a refactoring of navcomp.php from BNT, it plots a course between the players current system and
     * destination. The length of route that can be planned depends upon ship computer level, in a future
     * expansion this will be defined by the kind of navigation computer installed into the players ship; or
     * potentially their personal cartography level if I go down the route of EVE Online type skills system.
     *
     * @param System $system
     * @param Request $request
     * @return RedirectResponse
     */
    public function planRoute(System $system, Request $request): RedirectResponse
    {
        $router = new WarpRoute($this->user->ship);
        if ($router->calculateTo($system)) $router->save();

        return redirect()->back();
    }

    public function clearRoute(): RedirectResponse
    {
        $router = new WarpRoute($this->user->ship);
        $router->clear();

        return redirect()->back();
    }

    public function runRoute(): RedirectResponse
    {
        $router = new WarpRoute($this->user->ship);
        if (!$router->load()) return redirect()->back(); // TODO with warning

        // Obtain systems from WarpRoute and identify the gates needed

        dd($router);

        // Store navigation report and redirect back
        return redirect()->back();
    }
}
