<?php

namespace Tests\Feature;

use App\Models\System;
use App\Types\WaypointType;
use App\Models\Waypoints\Star;
use App\Types\SpectralType;
use App\Models\Waypoints\WarpGate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaypointTest extends TestCase
{
    use RefreshDatabase;

    public function test_star_waypoint_returns_correct_model(): void
    {
        $system = System::factory()->create();

        $star = new Star();
        $star->name = 'Sol';
        $star->angle = 1;
        $star->distance = 2;
        $star->inclination = 3;
        $star->eccentricity = 0;

        $star->properties->age = 12345678;
        $star->properties->radius = 10000;
        $star->properties->luminosity = 4.5;
        $star->properties->spectralType = SpectralType::A;

        $system->waypoints()->save($star);

        $warpGate = new WarpGate();
        $warpGate->primary_id = $star->id;
        $warpGate->name = 'Gate';
        $warpGate->angle = 1;
        $warpGate->distance = 20;
        $warpGate->inclination = 3;
        $warpGate->eccentricity = 0;

        $warpGate->properties->destination_system_id = null;

        $system->waypoints()->save($warpGate);

        /** @var Star $found */
        $found = $system->waypoints()->first();
        $this->assertInstanceOf(Star::class, $found);
        $this->assertEquals(12345678, $found->properties->age);
        $this->assertEquals(10000, $found->properties->radius);
        $this->assertEquals(4.5, $found->properties->luminosity);
        $this->assertEquals(SpectralType::A, $found->properties->spectralType);

        $child = $found->orbitals()->first();
        $this->assertInstanceOf(WarpGate::class, $child);
    }
}
