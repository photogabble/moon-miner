<?php

namespace Tests\Unit;

use App\Models\Waypoint;
use PHPUnit\Framework\TestCase;

class CoordinateSystemTest extends TestCase
{
    public function test_polar_coordinate_distance_function(): void
    {
        $waypointA = new Waypoint();
        $waypointA->distance = 1;
        $waypointA->angle = 2 * M_PI;

        $waypointB = new Waypoint();
        $waypointB->distance = 2;
        $waypointB->angle = M_PI;

        $this->assertEquals(3.0, $waypointB->distanceTo($waypointA));
        $this->assertEquals(3.0, $waypointA->distanceTo($waypointB));
    }
}
