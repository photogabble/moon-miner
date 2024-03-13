<?php declare(strict_types=1);

namespace App\Helpers;

/**
 * PHP Port of a Golang Port of the original C source.
 *
 * @author Ken Perlin (original copyright)
 * @see https://gitlab.gnome.org/GNOME/gegl/-/blob/master/operations/common/perlin/perlin.c
 *
 * @author Evgeniy Vasilev (Golang Port)
 * @see https://github.com/aquilax/go-perlin/blob/master/perlin.go
 *
 * @author Simon Dann (PHP Port)
 *
 * Alternative PHP Perlin Noise Generator can be found here:
 * https://github.com/A1essandro/perlin-noise-generator
 *
 * Eevee has written a very nice detailed article on Perlin Noise:
 * @see https://eev.ee/blog/2016/05/29/perlin-noise/
 *
 * Another good read:
 * @see https://adrianb.io/2014/08/09/perlinnoise.html
 */
class PerlinNoiseGenerator
{
    const int B = 0x100;
    const int N = 0x1000;
    const int BM = 0xff;

    private float $alpha;
    private float $beta;
    private int $n;
    private array $p;
    private array $g3;
    private array $g2;
    private array $g1;

    public function __construct(float $alpha, float $beta, int $n, ?int $seed = null)
    {
        if ($seed) mt_srand($seed);

        $this->alpha = $alpha;
        $this->beta = $beta;
        $this->n = $n;

        $this->p = $this->init1DArray(self::B + self::B + 2, 0);
        $this->g1 = $this->init1DArray(self::B + self::B + 2, 0.0);
        $this->g2 = $this->init2DArray(self::B + self::B + 2, 2);
        $this->g3 = $this->init2DArray(self::B + self::B + 2, 3);

        for ($i = 0; $i < self::B; $i++) {
            $this->p[$i] = $i;
            $this->g1[$i] = ((mt_rand() % (self::B + self::B)) - self::B) / self::B;

            for ($j = 0; $j < 2; $j++) {
                $this->g2[$i][$j] = ((mt_rand() % (self::B + self::B)) - self::B) / self::B;
            }

            $this->normalize2($this->g2[$i]);

            for ($j = 0; $j < 3; $j++) {
                $this->g3[$i][$j] = ((mt_rand() % (self::B + self::B)) - self::B) / self::B;
            }

            $this->normalize3($this->g3[$i]);
        }

        for (; $i > 0; $i--) {
            $j = mt_rand() % self::B;
            $tmp = $this->p[$i];
            $this->p[$i] = $this->p[$j];
            $this->p[$j] = $tmp;
        }

        for ($i = 0; $i < self::B + 2; $i++) {
            $this->p[self::B + $i] = $this->p[$i];
            $this->g1[self::B + $i] = $this->g1[$i];
            for ($j = 0; $j < 2; $j++) {
                $this->g2[self::B + $i][$j] = $this->g2[$i][$j];
            }
            for ($j = 0; $j < 3; $j++) {
                $this->g3[self::B + $i][$j] = $this->g3[$i][$j];
            }
        }
    }

    // Noise1D generates 1-dimensional Perlin Noise value
    public function Noise1D(float $px): float {
        $scale = 1.0;
        $sum = 0.0;

        for ($i = 0; $i < $this->n; $i++) {
            $val = $this->noise1($px);
            $sum += $val / $scale;
            $scale *= $this->alpha;
            $px *= $this->beta;
        }

        return $sum;
    }

    // Noise2D Generates 2-dimensional Perlin Noise value
    public function Noise2D(float $x, float $y): float
    {
        $scale = 1.0;
        $sum = 0.0;

        $px = [$x, $y];

        for ($i = 0; $i < $this->n; $i++) {
            $val = $this->noise2($px);
            $sum += $val / $scale;
            $scale *= $this->alpha;
            $px[0] *= $this->beta;
            $px[1] *= $this->beta;
        }

        return $sum;

    }

    // Noise3D Generates 3-dimensional Perlin Noise value
    public function Noise3D(float $x, float $y, float $z): float
    {
        if ($z < 0) {
            return $this->Noise2D($x, $y);
        }

        $scale = 1.0;
        $sum = 0.0;

        $px = [$x, $y, $z];

        for ($i = 0; $i < $this->n; $i++) {
            $val = $this->noise3($px);
            $sum += $val / $scale;
            $scale *= $this->alpha;
            $px[0] *= $this->beta;
            $px[1] *= $this->beta;
            $px[2] *= $this->beta;
        }

        return $sum;
    }

    private function init1DArray(int $count, int|float $default): array
    {
        $array = [];

        for ($i = 0; $i < $count; $i++) {
            $array[$i] = $default;
        }

        return $array;
    }

    private function init2DArray(int $xCount, int $yCount): array
    {
        $array = [];

        for ($x = 0; $x < $xCount; $x++) {
            $yArray = [];
            for ($y = 0; $y < $yCount; $y++) {
                $yArray[$y] = 0.0;
            }
            $array[$x] = $yArray;
        }
        return $array;
    }

    private function noise1(float $arg): float
    {
        $t = $arg + self::N;
        $bx0 = ((int) $t) & self::BM;
        $bx1 = ($bx0 + 1) & self::BM;
        $rx0 = $t - (int) $t;
        $rx1 = $rx0 - 1.0;

        $sx = $this->sCurve($rx0);
        $u = $rx0 * $this->g1[$this->p[$bx0]];
        $v = $rx1 * $this->g1[$this->p[$bx1]];

        return $this->lerp($sx, $u, $v);
    }

    private function noise2(array $vec): float
    {
        $t = $vec[0] + self::N;
        $bx0 = ((int) $t) & self::BM;
        $bx1 = ($bx0 + 1) & self::BM;
        $rx0 = $t - (int) $t;
        $rx1 = $rx0 - 1.0;

        $t = $vec[1] + self::N;
        $by0 = ((int) $t) & self::BM;
        $by1 = ($by0 + 1) & self::BM;
        $ry0 = $t - (int) $t;
        $ry1 = $ry0 - 1.0;

        $i = $this->p[$bx0];
        $j = $this->p[$bx1];

        $b00 = $this->p[$i+$by0];
        $b10 = $this->p[$j+$by0];
        $b01 = $this->p[$i+$by1];
        $b11 = $this->p[$j+$by1];

        $sx = $this->sCurve($rx0);
        $sy = $this->sCurve($ry0);

        $q = $this->g2[$b00];
        $u = $this->at2($rx0, $ry0, $q);
        $q = $this->g2[$b10];
        $v = $this->at2($rx1, $ry0, $q);
        $a = $this->lerp($sx, $u, $v);

        $q = $this->g2[$b01];
        $u = $this->at2($rx0, $ry1, $q);
        $q = $this->g2[$b11];
        $v = $this->at2($rx1, $ry1, $q);
        $b = $this->lerp($sx, $u, $v);

        return $this->lerp($sy, $a, $b);
    }

    private function noise3(array $vec): float
    {
        // X
        $t = $vec[0] + self::N;
        $bx0 = ((int) $t) & self::BM;
        $bx1 = ($bx0 + 1) & self::BM;
        $rx0 = $t - (int) $t;
        $rx1 = $rx0 - 1.0;

        // Y
        $t  = $vec[1] + self::N;
        $by0 = ((int) $t) & self::BM;
        $by1 = ($by0 + 1) & self::BM;
        $ry0 = $t - (int) $t;
        $ry1 = $ry0 - 1.0;

        // Z
        $t  = $vec[2] + self::N;
        $bz0 = ((int) $t) & self::BM;
        $bz1 = ($bz0 + 1) & self::BM;
        $rz0 = $t - (int) $t;
        $rz1 = $rz0 - 1.0;

        $i = $this->p[$bx0];
        $j = $this->p[$bx1];

        $b00 = $this->p[$i+$by0];
        $b10 = $this->p[$j+$by0];
        $b01 = $this->p[$i+$by1];
        $b11 = $this->p[$j+$by1];

        $t = $this->sCurve($rx0);
        $sy = $this->sCurve($ry0);
        $sz = $this->sCurve($rz0);

        $q = $this->g3[$b00+$bz0];
        $u = $this->at3($rx0, $ry0, $rz0, $q);
        $q = $this->g3[$b10+$bz0];
        $v = $this->at3($rx1, $ry0, $rz0, $q);
        $a = $this->lerp($t, $u, $v);

        $q = $this->g3[$b01+$bz0];
        $u = $this->at3($rx0, $ry1, $rz0, $q);
        $q = $this->g3[$b11+$bz0];
        $v = $this->at3($rx1, $ry1, $rz0, $q);
        $b = $this->lerp($t, $u, $v);

        $c = $this->lerp($sy, $a, $b);

        $q = $this->g3[$b00+$bz1];
        $u = $this->at3($rx0, $ry0, $rz1, $q);
        $q = $this->g3[$b10+$bz1];
        $v = $this->at3($rx1, $ry0, $rz1, $q);
        $a = $this->lerp($t, $u, $v);

        $q = $this->g3[$b01+$bz1];
        $u = $this->at3($rx0, $ry1, $rz1, $q);
        $q = $this->g3[$b11+$bz1];
        $v = $this->at3($rx1, $ry1, $rz1, $q);
        $b = $this->lerp($t, $u, $v);

        $d = $this->lerp($sy, $a, $b);

        return $this->lerp($sz, $c, $d);
    }

    private function normalize2(array &$v): void
    {
        $s = sqrt($v[0] * $v[0] + $v[1] * $v[1]);
        $v[0] = $v[0] / $s;
        $v[1] = $v[1] / $s;
    }

    private function normalize3(array &$v): void
    {
        $s = sqrt($v[0] * $v[0] + $v[1] * $v[1] + $v[2] * $v[2]);
        $v[0] = $v[0] / $s;
        $v[1] = $v[1] / $s;
        $v[2] = $v[2] / $s;
    }

    private function at2(float $rx, float $ry, array $q): float
    {
        return $rx * $q[0] + $ry * $q[1];
    }

    private function at3(float $rx, float $ry, float $rz, array $q): float
    {
        return $rx * $q[0] + $ry * $q[1] + $rz * $q[2];
    }

    private function sCurve(float $t): float
    {
        return $t * $t * (3.0 - (2.0 * $t));
    }

    private function lerp(float $t, float $a, float $b): float
    {
        return $a + $t * ($b - $a);
    }

}
