<?php

namespace App\Console\Commands;

use Imagick;
use App\Helpers\Timer;
use App\Types\Math\Vector3;
use Illuminate\Console\Command;

function drawSphere(float $x, float $y, float $z, Vector3 &$rgb): void {
    $xx = $x * $x * $z;
    $yy = $y * $y * $z;

    if ($xx + $yy > 1) {
        $rgb->x = 0;
        $rgb->y = 0;
        $rgb->z = 0;
        return;
    }

    // Get spherical coordinate angles from the cartesian values.
    $vec = new Vector3($x, $y, sqrt(1 - $xx - $yy));
    $vec->rotate(new Vector3(1.5, 0, 0.5));

    $phi = atan2($vec->x, $vec->y) + M_PI;    // 0 < phi < 2pi
    $theta = atan2($vec->z, 1) + M_PI / 4; // 0 < theta < pi/2

    // Transform the angles in to texture coordinates in [0,255].
    $u = floor(256 * $phi / M_PI / 2);
    $v = floor(256 * $theta / M_PI * 2);

    $rgb->x = $u ^ $v;
    $rgb->y = 2 * abs(128 - ($u ^ $v));
    $rgb->z = 255-($u ^ $v);

    $ldx = $x + 0.5;
    $ldy = $y + 0.5;
    $ld = sqrt( $ldx*$ldx + $ldy*$ldy ) + 0.4; // Constant added here for anti-glare effect. 0 = glare, 1 = no glare.
    $ld *= $ld;

    $rgb->x /= $ld;
    $rgb->y /= $ld;
    $rgb->z /= $ld;

    $rgb->clamp(0, 255);
}

class DrawPlanet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:draw-planet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timer = new Timer();
        $timer->start();

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

                $buffer[$i++] = (int) $pixel->x;
                $buffer[$i++] = (int) $pixel->y;
                $buffer[$i++] = (int) $pixel->z;
            }
        }

        $bufferSize = count($buffer);
        if($bufferSize === $size * $size * strlen("RGB")) {
            $this->line('<info>[OK]</info> Buffer size: <comment>'. $bufferSize .'</comment>');
        } else {
            $this->error('Incorrect buffer size');
            return 1;
        }

        $this->line('Generated planet in <info>'. $timer->sample() .' seconds</info>');

        $ok = $im->importImagePixels(0, 0, $size, $size, "RGB", Imagick::PIXEL_CHAR, $buffer);

        $this->line('['. ($ok ? '<info>OK</info>' : '<error>ERR</error>') .'] Imported Image Pixels in <info>'. $timer->sample() .' seconds</info>');

        $pathname = storage_path(time() . '.png');

        $im->setImageFormat('png');
        $im->writeImage($pathname);

        $this->line('Wrote to [<comment>'. $pathname .'</comment>] in <info>'. $timer->stop() .' seconds</info>');
    }
}
