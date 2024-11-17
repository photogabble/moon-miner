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

namespace App\Actions\Movement;

use App\Models\Ship;
use App\Models\System;
use App\Types\WaypointType;
use Illuminate\Support\Facades\DB;
use App\Models\Waypoints\WarpGate;
use Illuminate\Support\Facades\Cache;

class WarpRoute
{
    /**
     * Ordered list of System ids.
     * @var array<int>
     */
    public array $systemIds = [];

    /**
     * List of WarpGate hashes, this is used by the MapController for identifying links that are on route and
     * for applying the warp route using the WarpJump action.
     *
     * A WarpGate hash is the warp gates system id and its destination id sorted by numeric order, this means that a
     * warp gate from 1234 to 4321 will have the same hash as the warp gate from 4321 to 1234.
     *
     * @var array<string>
     */
    public array $linkHashes = [];

    /**
     * Ordered list of WarpGate ids, this is used when auto-piloting the route as we can
     * loop through this list against the WarpJump action.
     * @var array<int>
     */
    public array $warpGateIds = [];

    public int $startSystemId;

    private int $maxSearchDepth;

    public function __construct(private readonly Ship $ship)
    {
        // TODO: work out how to balance this with $this->ship->computer
        // This acts as a max query limit, because each search layer is an additional query.
        $this->maxSearchDepth = 6;
    }

    /**
     * Refactored query code from BNT:
     * This builds and runs a series of queries that search for a route between `a1.left_system_id` and `a{n}.right_system_id`
     * with `n` being the max depth for each query. These are run in a loop until a solution is found or n becomes equal to
     * max search depth.
     *
     * ```
     * select distinct
     *  "a1"."left_system_id" as "start",
     *  "a1"."right_system_id" as "dest_1",
     *  "a2"."right_system_id" as "dest_2",
     *  "a3"."right_system_id" as "dest_3"
     * from
     *  links as a1,
     *  links as a2,
     *  links as a3
     * where
     *  "a1"."left_system_id" = ? and
     *  "a1"."right_system_id" = a2.left_system_id and
     *  "a2"."right_system_id" = a3.left_system_id and
     *  "a3"."right_system_id" = ? and
     *  "a1"."right_system_id" != a1.left_system_id and
     *  "a2"."right_system_id" not in (a1.right_system_id, a1.left_system_id) and
     *  "a3"."right_system_id" not in (a1.right_system_id, a1.left_system_id, a2.right_system_id)
     * order by
     *  "a1"."left_system_id" desc,
     *  "a1"."right_system_id" desc,
     *  "a2"."right_system_id" desc,
     *  "a3"."right_system_id" desc
     * limit 1
     * ```
     *
     * @param System $system
     * @return void
     */
    public function calculateTo(System $system): bool
    {
        for ($searchDepth = 2; $searchDepth <= $this->maxSearchDepth; $searchDepth++) {
            $select = ['a1.left_system_id as start', 'a1.right_system_id as dest_1'];
            $from = ['links as a1'];

            for ($i = 2; $i <= $searchDepth; $i++) {
                $select[] = "a$i.right_system_id as dest_$i";
                $from[] = "links as a$i";
            }

            $query = DB::table(DB::raw(implode(',', $from)))
                ->select($select)
                ->where('a1.left_system_id', $this->ship->system_id);

            for ($i = 2; $i <= $searchDepth; $i++) {
                $t = $i - 1;
                $query->where("a$t.right_system_id", '=', DB::raw("a$i.left_system_id"));
            }

            $query->where("a$searchDepth.right_system_id", '=', $system->id);
            $query->where("a1.right_system_id", '!=', DB::raw('a1.left_system_id'));

            for ($i = 2; $i <= $searchDepth; $i++) {
                $notIn = [DB::raw('a1.right_system_id'), DB::raw('a1.left_system_id')];

                for ($temp2 = 2; $temp2 < $i; $temp2++) {
                    $notIn[] = DB::raw("a$temp2.right_system_id");
                }

                $query->whereNotIn("a$i.right_system_id", $notIn);
            }

            $query->orderBy('a1.left_system_id', 'desc');
            $query->orderBy('a1.right_system_id', 'desc');

            for ($i = 2; $i <= $searchDepth; $i++) {
                $query->orderBy("a$i.right_system_id", 'desc');
            }

            $result = $query
                ->distinct()
                ->limit(1)
                ->first();

            if ($result) {
                $this->startSystemId = $this->ship->system_id;
                $path = [];
                $result = get_object_vars($result);
                foreach ($result as $key => $value) {
                    if ($key === 'start') continue;
                    $ord = explode('_', $key)[1];
                    $path[$ord] = $value;
                }

                $this->systemIds = [$this->ship->system_id, ...array_values($path)];


                /**
                 * Reduce gateways down to a nested map that can be quickly looked up to
                 * find the warp gate id for a path between systems.
                 * @var array<array<int>> $gateways [system_id => [system_id => warpgate_id]]
                 */
                $gateways = System::with('waypoints')
                    ->whereIn('id', $this->systemIds)
                    ->get()
                    ->reduce(function(array $carry, System $system) {
                        $carry[$system->id] = $system
                            ->waypointsOfType(WaypointType::WarpGate)
                            ->reduce(function(array $carry, WarpGate $warpGate) {
                                $carry[$warpGate->properties->destination_system_id] = $warpGate->id;
                                return $carry;
                            }, []);

                        return $carry;
                    }, []);

                // Identify links so that MapController can mark links as part of a route,
                // this is done via sorting the two system id's of a link and combining into
                // a string "hash" that's unique per link; a link will have two WayPoints but
                // always the same hash value.
                for ($n = 0; $n < count($this->systemIds); $n++) {
                    $left = $this->systemIds[$n];
                    $right = $this->systemIds[$n + 1] ?? null;
                    if (is_null($right)) break;

                    $hash = [$left, $right];
                    sort($hash);

                    $this->linkHashes[] = implode('-', $hash);
                    $this->warpGateIds[$n] = $gateways[$left][$right] ?? null;
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Load route from cache, returns false if no route was loaded.
     * @return bool
     */
    public function load(): bool
    {
        if ($found = Cache::get($this->cacheKey())) {
            $this->startSystemId = $found['start_system_id'];
            $this->systemIds = $found['system_ids'];
            $this->linkHashes = $found['link_hashes'];
            $this->warpGateIds = $found['warp_gate_ids'];

            return true;
        }

        return false;
    }

    /**
     * Persist route to cache
     * @return void
     */
    public function save(): void
    {
        Cache::forever($this->cacheKey(), [
            'start_system_id' => $this->startSystemId,
            'system_ids' => $this->systemIds,
            'link_hashes' => $this->linkHashes,
            'warp_gate_ids' => $this->warpGateIds,
        ]);
    }

    /**
     * Clear cached route
     * @return void
     */
    public function clear(): void
    {
        Cache::forget($this->cacheKey());
    }

    /**
     * Returns true if the input System is within this warp route.
     * @param int|System|null $system
     * @return bool
     */
    public function contains (int|System|null $system = null): bool
    {
        if (is_null($system)) $system = $this->ship->system_id;
        else if ($system instanceof System) $system = $system->id;

        return in_array($system, $this->systemIds);
    }

    /**
     * Returns count of remaining jumps from the current system to the destination.
     * @param int|System|null $system
     * @return int
     */
    public function remaining(int|System|null $system = null): int
    {
        if (is_null($system)) $system = $this->ship->system_id;
        else if ($system instanceof System) $system = $system->id;

        $key = array_search($system, $this->systemIds);
        if ($key === false) return 0;

        return count($this->systemIds) - ($key + 1);
    }

    /**
     * Returns id of the next system in the route.
     * @param int|System|null $system
     * @return int|null
     */
    public function next(int|System|null $system = null): ?int
    {
        if (is_null($system)) $system = $this->ship->system_id;
        else if ($system instanceof System) $system = $system->id;

        $key = array_search($system, $this->systemIds);
        if ($key === false) return null;

        return $this->systemIds[$key+1] ?? null;
    }

    /**
     * Key for storing warp route in cache.
     * @return string
     */
    private function cacheKey(): string
    {
        return $this->ship->id . '_navicom_warp_route';
    }
}
