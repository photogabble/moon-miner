<?php

namespace App\Http\Controllers;

use App\Models\Ship;
use App\Models\System;
use Illuminate\Http\Request;
use App\Models\Waypoints\Port;
use App\Models\Waypoints\Planet;
use App\Models\Waypoints\WarpGate;
use App\Actions\Movement\WarpJump;
use App\Actions\Movement\RealSpace;
use Illuminate\Http\RedirectResponse;

class ShipController extends Controller
{
    public function dashboard(Request $request)
    {
        // Display all ships owned by player
    }

    public function view(Ship $ship, Request $request)
    {
        // Display ship details
    }

    public function travelThrough(Ship $ship, WarpGate $gate): RedirectResponse
    {
        $action = new WarpJump($ship);
        $movement = $action->jump($gate);

        session()->flash('alert', [
            'type' => 'info',
            'title' => 'Warp Jump Report',
            'message' => [ // TODO complete
                'You have now entered system <white>' . $movement->system->name . '</white>, initial scans indicate there are no other players in range.',
            ],
        ]);

        return redirect()->back();
    }

    public function planTravelTo(Ship $ship, System $system): RedirectResponse
    {
        $action = new RealSpace($ship);
        $plan = $action->calculateMoveTo($system);

        return redirect()->back()->with([
            'alert' => [
                'type' => 'info',
                'title' => 'Real Space Travel Report',
                'message' => [
                    'With your engines, it will take <white>' . $plan['turns'] . '</white> turns to complete the journey.',
                    'You would gather <white>' . $plan['energyScooped'] . '</white> units of energy.'
                ],
            ],
        ]);
    }

    public function travelTo(Ship $ship, System $system): RedirectResponse
    {
        $action = new RealSpace($ship);
        $action->moveTo($system);

        return redirect()->back()->with([
            'alert' => [
                'type' => 'info',
                'title' => 'Real Space Travel Report',
                'message' => '...', // TODO fill in message
            ],
        ]);
    }

    public function landOn(Ship $ship, Planet $planet)
    {

    }

    public function dockWith(Ship $ship, Port $port)
    {

    }
}
