<?php
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

use App\Models\Link;
use App\Models\User;
use App\Models\Sector;
use App\Models\System;
use App\Models\Waypoint;
use App\Models\Encounter;
use App\Types\Math\Vector2;
use App\Types\Math\Vector3;
use App\Types\Math\Vector4;
use Illuminate\Http\Request;
use App\Models\Waypoints\Star;
use App\Models\Waypoints\WarpGate;
use App\Models\Encounters\Dialogue;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ShipController;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\NaviComController;
use App\Http\Controllers\WaypointController;

Route::middleware('guest')->get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/explore', function () {
    return Inertia::render('Explore');
})->name('explore');

Route::get('/map', function () {
    $size = setting('game.map_size');
    $image = imagecreatetruecolor($size, $size);
    $black = imagecolorallocate($image, 0, 0, 0);
    $red100 = imagecolorallocatealpha($image, 255, 0, 0, 100);
    $red50 = imagecolorallocatealpha($image, 255, 0, 0, 110);
    $red = imagecolorallocate($image, 255, 0, 0);
    $green = imagecolorallocate($image, 0, 255, 0);
    $orange = imagecolorallocate($image, 255, 102, 0);
    imagefill($image, 0, 0, $black);

    /**
     * Random Sector
     * @var Sector $rSector
     */
    $rSector = Sector::query()->inRandomOrder()->where('system_count', '>', '4')->first();

    /** @var System $startingSystem */
    $startingSystem = $rSector->systems()->inRandomOrder()->first();

    $linkQueue = [];
    $travelledLinkIds = [];

    foreach ($startingSystem->links as $link) {
        $linkQueue[] = [
            'id' => $link->id,
            'system' => $link->rightSystem,
            'jumps' => 25,
        ];
    }

    while (count($linkQueue) > 0) {
        $current = array_pop($linkQueue);
        if (in_array($current['id'], $travelledLinkIds)) continue;

        $travelledLinkIds[] = $current['id'];

        if ($current['jumps'] - 1 <= 0) continue;

        foreach ($current['system']->links as $link) {
            $linkQueue[] = [
                'id' => $link->id,
                'system' => $link->rightSystem,
                'jumps' => $current['jumps'] - 1,
            ];
        }
    }

    // Draw Sector info
    foreach (Sector::all() as $sector) {
        $position = $sector->position();

        if ($sector->system_count === 0) {
            imagefilledrectangle($image, $position->x, $position->y, $position->x + setting('game.sector_size'), $position->y + setting('game.sector_size'), $red50);
        } else if ($sector->id === $rSector->id) {
            imagerectangle($image, $position->x, $position->y, $position->x + setting('game.sector_size'), $position->y + setting('game.sector_size'), $orange);
        } else {
            imagerectangle($image, $position->x, $position->y, $position->x + setting('game.sector_size'), $position->y + setting('game.sector_size'), $red100);
        }
    }

    // Draw (x,y) mid-point
    imageline($image, 0, $size / 2, $size, $size / 2, $red);
    imageline($image, $size / 2, 0, $size / 2, $size, $red);

    $origin = setting('game.map_size') / 2;

    /**
     * Draw Stars
     * @var Star $star
     */
    foreach (Star::with('system')->get() as $star) {
        $position = $star->system->toCartesian();

        $x = $origin + ($position->x * $origin);
        $y = $origin + ($position->y * $origin);

        $rgb = $star->properties->colour();

        imagefilledellipse($image, $x, $y, 4, 4, imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']));
    }

    /**
     * Draw WarpGates
     * @var Link[] $links
     */
    $links = Link::with(['leftSystem', 'rightSystem'])->get();

    foreach ($links as $link) {
        $start = $link->leftSystem->toCartesian();
        $dest = $link->rightSystem->toCartesian();

        $x1 = $origin + ($start->x * $origin);
        $y1 = $origin + ($start->y * $origin);

        $x2 = $origin + ($dest->x * $origin);
        $y2 = $origin + ($dest->y * $origin);

        imageline($image, $x1, $y1, $x2, $y2, in_array($link->id, $travelledLinkIds) ? $orange : $green);
    }

    ob_start();
    imagepng($image);
    $buffer = ob_get_contents();
    ob_end_clean();
    imagedestroy($image);

    return response($buffer, 200)->header('Content-type', 'image/png');
});

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'navicom'], function () {
        Route::get('/', [NaviComController::class, 'system'])
            ->middleware(['is-in-space'])
            ->name('navicom');

        Route::patch('set-view-mode', [NaviComController::class, 'setViewMode'])
            ->name('navicom.set-view-mode');

        Route::get('system/{system}', [NaviComController::class, 'system'])
            ->name('navicom.system');

        Route::get('planet/{planet}', [NaviComController::class, 'planet'])
            ->name('navicom.planet');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('sector-map/plan-route/{system}', [MapController::class, 'planRoute'])
        ->name('navicom.plan-route');

    Route::get('sector-map/clear-planned-route', [MapController::class, 'clearRoute'])
        ->name('navicom.clear-planned-route');

    Route::get('sector-map/autopilot-planned-route', [MapController::class, 'runRoute'])
        ->name('navicom.autopilot-planned-route');

    Route::get('sector-map/{sector?}', [MapController::class, 'sector'])
        ->name('sector-map');

    Route::group(['prefix' => '/ship/{ship}'], function () {
        Route::get('/', [ShipController::class, 'view'])->name('ship.view');
        Route::post('/jump-through/{gate}', [ShipController::class, 'travelThrough'])->name('ship.travel-through.gate');
        Route::post('/travel-to/{system}', [ShipController::class, 'travelTo'])->name('ship.travel-to');
        Route::post('/travel-to/{system}/plan', [ShipController::class, 'planTravelTo'])->name('ship.travel-to.plan');
        Route::post('/land-on/{planet}', [ShipController::class, 'landOn'])->name('ship.land-on.planet');
        Route::post('/dock-with/{port}', [ShipController::class, 'dockWith'])->name('ship.dock-with.port');
    });

    Route::get('planet', [PlanetController::class, 'dashboard'])
        ->middleware(['is-on-planet'])
        ->name('planet.dashboard');

    Route::get('planet/{planet}', [PlanetController::class, 'show'])
        ->name('planet.show');
});

Route::post('debug/spawn-encounter', function (Request $request) {
    $encounter = new Dialogue([
        'state' => [
            'title' => 'New Spawn ' . App\Models\Encounter::count(),
            'messages' => ['Hello world', 'This is <white>another</white> paragraph...'],
        ],
        'system_id' => 1,
    ]);

    $request->user()->encounters()->save($encounter);

    return redirect()->back();
})->name('debug.spawn-encounter');

Route::get('debug/p', function () {
    $size = 512;
    $hSize = $size / 2;

    $im = new Imagick();
    $im->newImage($size, $size, 'black');

    $pixel = new Vector3(0,0,0);
    $buffer = array_fill(0, $size * $size * 3, 0);

    $i = 0;
    for ($x = 0; $x < $size; $x++) {
        for($y = 0; $y < $size; $y++) {
            $xx = ($x - $hSize) / $hSize;
            $yy = ($y - $hSize) / $hSize;

            drawSphere($xx, $yy, 2, $pixel);

            $buffer[$i++] = $pixel->x;
            $buffer[$i++] = $pixel->y;
            $buffer[$i++] = $pixel->z;

//            imagesetpixel($image, $x, $y, imagecolorallocate($image, $pixel->x, $pixel->y, $pixel->z));
        }
    }

    $im->importImagePixels(0, 0, $size, $size, "RGB", Imagick::PIXEL_INTEGER, $buffer);

//    $radius = 100;
//    $atmosphereAmount = $radius * 0.05;
//    $atmosphereRadius = $radius + $atmosphereAmount;
//
//    for ($y = -500; $y < 500; $y++) {
//        for ($x = -500; $x < 500; $x++) {
//
//            $pixelCoords = new Vector2($x, $y);
//            $d = $pixelCoords->length() - $radius;
//            if ($d <= 0.0) {
//                // Inside circle
//                imagesetpixel($image, 250 + $pixelCoords->x, 250+ $pixelCoords->y, $white);
//            }
//
//            if ($d < $atmosphereAmount + 24.0 && $d >= -1.0) {
//                $normal = new Vector2($pixelCoords->x / $atmosphereRadius, $pixelCoords->y / $atmosphereRadius);
//                imagesetpixel($image, 250 + $normal->x, 250 + $normal->y, $red);
//            }
//        }
//    }

    // Draw a single line (vertex?) in the middle of the canvas, for each position shade the pixel
    // using a "shader"
//    $vertex = new Vector2(50, 250);
//    $color = new Vector3(255,0,0);
//
//    for ($x = 0; $x < 400; $x++) {
//        $colorShift = cos($x / 100);
//
//        // vec4(clamp(color - colorShift, 0.0, 1.0), 1.0);
//
//        $color->x = clamp($color->x - $colorShift, 0, 255);
//        $color->y = clamp($color->y - $colorShift, 0, 255);
//        $color->z = clamp($color->z - $colorShift, 0, 255);
//
//        imagesetpixel($image, $vertex->x, $vertex->y, imagecolorallocate($image, $color->x, $color->y, $color->z));
//        $vertex->x++;
//    }

    ob_start();

    $im->setImageFormat('jpg');
    echo $im;

    $buffer = ob_get_contents();
    ob_end_clean();

    return response($buffer, 200)->header('Content-type', 'image/png');
});

Route::post('debug/randomise-system', function (Request $request) {
    /** @var User $user */
    $user = $request->user();

    $waypoint = Waypoint::query()
        ->inRandomOrder()
        ->whereNotIn('type', [Star::class, WarpGate::class])
        ->first();

    $user->ship->moveTo($waypoint->system_id, \App\Types\MovementMode::Spawn);

    return redirect()->back();
})->name('debug.randomise-system');

Route::post('encounter/{encounter}/{action}', function (Encounter $encounter, string $action) {
    $encounter->do($action);
    return redirect()->back();
})->name('encounter.execute');

Route::get('ranking', [\App\Http\Controllers\RankingController::class, 'index'])->name('ranking');

require __DIR__ . '/auth.php';
