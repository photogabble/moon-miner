<?php

use App\Models\Sector;
use App\Models\Waypoints\Star;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::middleware('guest')->get('/', function () {
    return view('index');
})->name('home');

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

    /** @var \App\Models\System $startingSystem */
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

        if ($current['jumps'] - 1 <=0) continue;

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
    imageline($image, 0, $size/2, $size, $size/2, $red);
    imageline($image, $size/2, 0, $size/2, $size, $red);

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
     * @var \App\Models\Link[] $links
     */
    $links = \App\Models\Link::with(['leftSystem', 'rightSystem'])->get();

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

Route::view('ranking', 'ranking')->name('ranking');

Route::middleware('auth')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});
