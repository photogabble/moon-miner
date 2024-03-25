<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Types\Geometry\Point;
use App\Types\Geometry\PolarPoint;

class PointTest extends TestCase
{
    public function test_point_to_sector_hash(): void
    {
        cache()->set('game-setting.game.map_size', 1000);
        cache()->set('game-setting.game.sector_size', 100);

        $point = new Point(517, 187);
        $this->assertEquals('5.1', $point->toSectorHash());

        $point = new Point(87, 712);
        $this->assertEquals('0.7', $point->toSectorHash());
    }

    public function test_polar_point_to_cartesian_normalises(): void
    {
        cache()->set('game-setting.game.map_size', 1000);
        cache()->set('game-setting.game.sector_size', 100);

        // Middle Left should be (0, 500)
        $polar = new PolarPoint(M_PI, 1.0);
        $cords = $polar->toCartesian()->normalised();

        $this->assertEquals(0, floor($cords->x));
        $this->assertEquals(500, floor($cords->y));

        // Middle Right should be (1000, 500)
        $polar = new PolarPoint(0, 1.0);
        $cords = $polar->toCartesian()->normalised();

        $this->assertEquals(1000, floor($cords->x));
        $this->assertEquals(500, floor($cords->y));

        // Middle should therefore be (500, 500)
        $polar = new PolarPoint(0, 0.0);
        $cords = $polar->toCartesian()->normalised();

        $this->assertEquals(500, floor($cords->x));
        $this->assertEquals(500, floor($cords->y));
    }

    public function test_point_normal(): void
    {
        cache()->set('game-setting.game.map_size', 50);
        cache()->set('game-setting.game.sector_size', 10);

        $point = new Point(0.0, 1.0);
        $cords = $point->normalised();

        $this->assertEquals(0, floor($cords->x));
        $this->assertEquals(50, floor($cords->y));
    }
}
