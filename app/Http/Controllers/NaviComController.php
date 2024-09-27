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
use App\Models\Waypoints\Planet;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Resources\SystemResource;
use App\Http\Resources\WaypointResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NaviComController extends Controller
{
    /**
     * GET: /navicom
     * GET: /navicom/system/{system:id}
     *
     * Display System details, if none passed return the players current system. If a player hasn't
     * visited a requested system provide limited to no details. E.g if they have never visited
     * and not visited any systems that link INTO this one return nothing. If they have never visited
     * but have visited a system that links INTO this one then return the link back if it exists (some
     * systems can have one way warp gates, where the other side is offline, or non-existent.)
     *
     * @param int|null $system
     * @return Response
     */
    public function system(?int $system = null): Response
    {
        $this->user->load(['ship', 'ship.system' => function (BelongsTo $relationship) {
            $relationship->addSelect('systems.*');
            $relationship->addSelect(DB::raw("(SELECT COUNT(id) FROM movement_logs WHERE `movement_logs`.`system_id` = `systems`.`id` AND `movement_logs`.`user_id` = {$this->user->id}) > 0 as has_visited"));
            $relationship->addSelect(DB::raw("(SELECT COUNT(id) FROM ships WHERE `ships`.`system_id` = `systems`.`id` AND `ships`.`id` = {$this->user->ship_id}) > 0 as is_current_sector"));
        }, 'ship.system.waypoints', 'currentEncounter']);

        if (!is_null($system)) {
            $system = System::queryForUser($this->user)
                ->with(['waypoints'])
                ->find($system);
        }

        $props = [
            'system' => new SystemResource($system ?? $this->user->ship->system),
        ];

        // If player is following an autopilot route then waypoints will be set containing the
        // route as computed by the NavCom:
        // TODO ^^^ from TKI

        return Inertia::render('Dashboard', $props);
    }

    /**
     * GET: /navicom/planet/{planet:id}
     *
     * Display planet details, if the players ship is currently in orbit around or landed on the planet
     * in question then redirect them to the planet dashboard page. Provide details matching the
     * following criteria:
     *
     * HIGH DETAIL:
     * If the player is in the current system, or has visited this system before we can display
     * all the planets celestial properties. If the player owns this planet we can display all
     * its production properties and inventory. If the player has scanned the planet we can
     * display all its production properties and inventory at the time of the scan.
     *
     * MEDIUM DETAIL:
     * If the player isn't in the current system but has visited before we can display all the
     * celestial properties. If the player owns the planet and is within range they can see the
     * current inventory, they will always be able to see their current production properties
     * however can only edit them within range. Range is determined via ship equipment, I'd like
     * to also allow players to construct repeaters to extend their control range. If the player
     * isn't in the current system, but one connected to it, they can scan the planet to obtain
     * its celestial properties. Depending on their scan strength they might also be able to
     * determine ownership remotely.
     *
     * LOW DETAIL:
     * If the player isn't in the current system but one connected to it, they can do a low level
     * scan of the system which will list waypoints that aren't "cloaked" but in order to obtain
     * celestial properties they will need to do a high level scan. Some planets might have a scan
     * level requirement higher than the players and they will remain unable to be revealed via
     * remote scans. Players might also install cloaking infrastructure to make scanning of a planet
     * difficult/near-impossible.
     *
     * NO DETAIL:
     * Player hasn't visited the system and are not in one connected to it. Navigation Computer to
     * display "no known details".
     *
     * @param Planet $planet
     * @return Response|RedirectResponse
     */
    public function planet(Planet $planet): Response|RedirectResponse
    {
        // If the player is landed on a planet, then redirect to the planet dashboard
        if ($this->user->ship->onPlanet() && $this->user->ship->planet_id === $planet->id) {
            return redirect()->route('planet.dashboard');
        }

        return Inertia::render('NaviCom/Planet', [
            'planet' => new WaypointResource($planet),
        ]);
    }
}
