<?php

namespace App\Http\Controllers;

use App\Models\Waypoint;

class WaypointController extends Controller
{
    public function show(Waypoint $waypoint) {
        // TODO: return waypoint detail view
        // TODO: depending if the waypoint is within the same system as the players ship
        //       we will return different amounts of information (fog of war style)
        //       also depending on the players ship level, scanning for waypoint information
        //       discovery will depend if the ship is in range (e.g can only scan planets in
        //       systems that are one jump away from their current system) to begin with
        //       discovery will be basic: have I visited the system this waypoint exists within
        //       if so return all information.
    }
}
