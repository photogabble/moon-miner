<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\PerlinNoiseGenerator;

class PerlinNoiseTest extends TestCase
{
    public function test_perlin_1d_noise(): void
    {
        $noise = new PerlinNoiseGenerator(2,2,3, 123);
        $this->assertEquals(0.0, $noise->Noise1D(10));
    }

    public function test_perlin_2d_noise(): void
    {
        $noise = new PerlinNoiseGenerator(2,2,3, 123);
        $this->assertEquals(0.0, $noise->Noise2D(10,10));
    }

    public function test_perlin_3d_noise(): void
    {
        $noise = new PerlinNoiseGenerator(2,2,3, 123);
        $this->assertEquals(0.0, $noise->Noise3D(10, 10, 10));
    }
}
