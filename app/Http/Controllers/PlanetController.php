<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Waypoint;
use Illuminate\Http\Request;
use App\Models\Waypoints\Planet;

class PlanetController extends Controller
{
    // If the player is landed on a planet they will be redirected here from the ship dashboard, attempting
    // to visit this route will redirect back to the ship dashboard if the player is not landed on a planet.
    public function dashboard()
    {
        // TODO: lookup planet that player is currently landed on and display management dashboard
    }

    /**
     * Navigation Computers Planet detail view:
     *
     * @param Request $request
     * @param Planet $planet
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function show (Request $request, Planet $planet) {
        /* @var User $user */
        $user = $request->user();

        // If the player is in orbit or landed on a planet, then redirect to the planet dashboard
        if (($user->ship->inOrbit() || $user->ship->onPlanet()) && $user->ship->planet_id === $planet->id) {
            return redirect()->route('planet.dashboard');
        }

        // Else return planet details that are available to the player:
        //
        // HIGH DETAIL:
        // If the player is in the current system, or has visited this system before we can display
        // all the planets celestial properties. If the player owns this planet we can display all
        // its production properties and inventory. If the player has scanned the planet we can
        // display all its production properties and inventory at the time of the scan.
        //
        // MEDIUM DETAIL:
        // If the player isn't in the current system but has visited before we can display all the
        // celestial properties. If the player owns the planet and is within range they can see the
        // current inventory, they will always be able to see their current production properties
        // however can only edit them within range. Range is determined via ship equipment, I'd like
        // to also allow players to construct repeaters to extend their control range. If the player
        // isn't in the current system, but one connected to it, they can scan the planet to obtain
        // its celestial properties. Depending on their scan strength they might also be able to
        // determine ownership remotely.
        //
        // LOW DETAIL:
        // If the player isn't in the current system but one connected to it, they can do a low level
        // scan of the system which will list waypoints that aren't "cloaked" but in order to obtain
        // celestial properties they will need to do a high level scan. Some planets might have a scan
        // level requirement higher than the players and they will remain unable to be revealed via
        // remote scans. Players might also install cloaking infrastructure to make scanning of a planet
        // difficult/near-impossible.
        //
        // NO DETAIL:
        // Player hasn't visited the system and are not in one connected to it. Navigation Computer to
        // display "no known details".
        //

        return Inertia::render('NaviCom/Planet', [
            'planet' => $planet, // TODO Planet Resource
        ]);
    }
}
