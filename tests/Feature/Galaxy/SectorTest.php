<?php

namespace Tests\Feature\Galaxy;

use App\Models\System;
use App\Models\Sector;
use App\Types\WaypointType;
use App\Types\Geometry\Point;
use App\Models\Waypoints\Star;
use App\Types\SpectralType;
use App\Models\Waypoints\WarpGate;
use Database\Seeders\SectorSeeder;
use App\Types\Geometry\PolarPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_sector_seeder_seeds_grid(): void
    {
        // Seed a 5x5 grid of sectors
        // [-2,  2] [-1,  2] [0,  2], [1,  2], [2,  2]
        // [-2,  1] [-1,  1] [0,  1], [1,  1], [2,  1]
        // [-2,  0] [-1,  0] [0,  0], [1,  0], [2,  0]
        // [-2, -1] [-1, -1] [0, -1], [1, -1], [2, -1]
        // [-2, -2] [-1, -2] [0, -2], [1, -2], [2, -2]
        config()->set('game.map_size', 50);
        config()->set('game.sector_size', 10);
        $this->seed(SectorSeeder::class);

        $this->assertNotNull(Sector::findByXY(-2, -2));
        $this->assertNotNull(Sector::findByXY(-1, -2));
        $this->assertNotNull(Sector::findByXY(0, -2));
        $this->assertNotNull(Sector::findByXY(1, -2));
        $this->assertNotNull(Sector::findByXY(2, -2));

        $this->assertNotNull(Sector::findByXY(-2, -1));
        $this->assertNotNull(Sector::findByXY(-1, -1));
        $this->assertNotNull(Sector::findByXY(0, -1));
        $this->assertNotNull(Sector::findByXY(1, -1));
        $this->assertNotNull(Sector::findByXY(2, -1));

        $this->assertNotNull(Sector::findByXY(-2, 0));
        $this->assertNotNull(Sector::findByXY(-1, 0));
        $this->assertNotNull(Sector::findByXY(0, 0));
        $this->assertNotNull(Sector::findByXY(1, 0));
        $this->assertNotNull(Sector::findByXY(2, 0));

        $this->assertNotNull(Sector::findByXY(-2, 1));
        $this->assertNotNull(Sector::findByXY(-1, 1));
        $this->assertNotNull(Sector::findByXY(0, 1));
        $this->assertNotNull(Sector::findByXY(1, 1));
        $this->assertNotNull(Sector::findByXY(2, 1));

        $this->assertNotNull(Sector::findByXY(-2, 2));
        $this->assertNotNull(Sector::findByXY(-1, 2));
        $this->assertNotNull(Sector::findByXY(0, 2));
        $this->assertNotNull(Sector::findByXY(1, 2));
        $this->assertNotNull(Sector::findByXY(2, 2));
    }

    public function test_point_scales_to_sector_hash(): void
    {
        config()->set('game.map_size', 4000);
        config()->set('game.sector_size', 100);

        // Taken from a seeded database
        $polar = new PolarPoint(2.3297509982115, 0.99713251202271);

        // (-0.6861..., 0.7234...)
        $point = $polar->toCartesian();

        // Scale by half map width as zero is in the absolute center.
        // (-1372.38, 1446.95)
        $scaled = $point->scale(2000);
        $this->assertEquals('-13.14', $scaled->toSectorHash());
    }

    public function test_point_to_sector_hash(): void
    {
        // For a map size of 50 the polar coordinate max radius
        // of 1.0 will be 25 (half the map.)
        config()->set('game.map_size', 50);
        config()->set('game.sector_size', 10);

        // For a map size of 50 sectors will go from -3 to 3 ?

        // (-25, 0) will be in the middle of the left side of the map
        $this->assertEquals('-2.0', (new Point(-25,0))->toSectorHash());

        // (25, 0) will be in the middle of the right side of the map
        $this->assertEquals('2.0', (new Point(25,0))->toSectorHash());

        // (0,0) will be in the center of the map
        $this->assertEquals('0.0', (new Point(0,0))->toSectorHash());
    }

    public function test_sector_can_see_cardinal_neighbours(): void
    {
        config()->set('game.map_size', 50);
        config()->set('game.sector_size', 10);
        $this->seed(SectorSeeder::class);

        $this->assertDatabaseCount( 'sectors', 25);

        $s = Sector::all()->toArray();

        $sector = Sector::findByXY(-2,-2);
        $this->assertNotNull($sector);

        $this->assertNull($sector->north());
        $this->assertNull($sector->west());
        $this->assertInstanceOf(Sector::class, $sector->east());
        $this->assertInstanceOf(Sector::class, $sector->south());

        $sector = Sector::findByXY(0,0);
        $this->assertInstanceOf(Sector::class, $sector->north());
        $this->assertInstanceOf(Sector::class, $sector->east());
        $this->assertInstanceOf(Sector::class, $sector->south());
        $this->assertInstanceOf(Sector::class, $sector->west());

        $this->assertCount(8, $sector->neighbours());
    }

    public function test_sector_can_see_correct_neighbours(): void
    {
        // Seed a 10x10 grid of sectors
        config()->set('game.map_size', 100);
        config()->set('game.sector_size', 10);
        $this->seed(SectorSeeder::class);

        $this->assertDatabaseCount( 'sectors', 100);

        // Pick two sectors with overlapping neighbours (1,1) and (3,3)

        $first = Sector::findByXY(1,1);
        $this->assertTrue(Sector::findByXY(0,0)->is($first->northWest()));
        $this->assertTrue(Sector::findByXY(1,0)->is($first->north()));
        $this->assertTrue(Sector::findByXY(2,0)->is($first->northEast()));
        $this->assertTrue(Sector::findByXY(2,1)->is($first->east()));
        $this->assertTrue(Sector::findByXY(2,2)->is($first->southEast()));
        $this->assertTrue(Sector::findByXY(1,2)->is($first->south()));
        $this->assertTrue(Sector::findByXY(0,2)->is($first->southWest()));
        $this->assertTrue(Sector::findByXY(0,1)->is($first->west()));

        // (3,3) should share north-west neighbour with (1,1)
        $second = Sector::findByXY(3,3);
        $this->assertTrue($second->northWest()->is($first->southEast()));
    }
}
