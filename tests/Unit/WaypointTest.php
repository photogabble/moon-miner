<?php declare(strict_types=1);

namespace Tests\Unit;
use App\Models\Properties\StarProperties;
use App\Models\Waypoints\Star;
use PHPUnit\Framework\TestCase;

class WaypointTest extends TestCase
{
    public function test_star_properties_cast(): void
    {
        $star = new Star();
        $this->assertInstanceOf(StarProperties::class, $star->properties);
    }
}
