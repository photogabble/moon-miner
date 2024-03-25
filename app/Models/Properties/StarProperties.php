<?php declare(strict_types=1);
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

namespace App\Models\Properties;

use App\Helpers\Range;
use App\Types\SpectralType;

/**
 * Star Properties
 *
 * @see http://spiff.rit.edu/classes/phys230/lectures/hr/hr.html
 * @see https://www.astronomynotes.com/starprop/s5.htm
 * @see http://astronomy.nmsu.edu/geas/lectures/lecture19/slide06.html
 * @see https://en.wikipedia.org/wiki/Color_index
 * @see https://www.astro.ucla.edu/~wright/magcolor.htm
 * @see http://www.handprint.com/ASTRO/specclass.html
 *
 * @method static StarProperties make(array $attributes = [])
 */
final class StarProperties extends ModelProperties
{
    /**
     * Stars Spectral Type
     * @var SpectralType
     */
    public SpectralType $spectralType = SpectralType::M;

    /**
     * Solar Masses (M⊙)
     * Multiply by 1.989E+30 to get kg
     *
     * @var float
     */
    public float $mass = 0.0;

    /**
     * Solar luminosity (L⊙)
     * Multiply by 3.828E+26 to get Watts
     *
     * @var float
     */
    public float $luminosity = 0.0;

    /**
     * Max age of star in billions of years (Gyr)
     *
     * @var float
     */
    public float $lifetime = 0.0;

    /**
     * Current age in billions of years (Gyr)
     *
     * @var float
     */
    public float $age = 0.0;

    /**
     * Solar Radius (R⊙)
     * Multiply by 696340 to get km
     * @var float
     */
    public float $radius = 0.0;

    /**
     * Solar Density (D⊙)
     * Multiply by 1.408 to get g/cm³
     * @var float
     */
    public float $density = 0.0;

    /**
     * Surface Temperature in degrees Kelvin.
     * @var float
     */
    public float $temperatureK = 0.0;

    public function classification(): string
    {
        // TODO: Include Luminosity class
        return $this->spectralType->value;
    }

    /**
     * Colour of this star based upon its surface temperature in degrees Kelvin.
     *
     * @see http://www.vendian.org/mncharity/dir3/starcolor/details.html
     * @see https://tannerhelland.com/2012/09/18/convert-temperature-rgb-algorithm-code.html
     * @return int[] [r,g,b]
     */
    public function colour(): array // [r,g,b]
    {
        // All calculations require temperature in kelvin divided by 100
        $tmpKelvin = $this->temperatureK / 100;
        $rgb = [];

        if ($tmpKelvin <= 66) {
            $rgb['r'] = 255;

            // Note: the R-squared value for this approximation is .996
            $rgb['g'] = 99.4708025861 * log($tmpKelvin) - 161.1195681661;
        } else {
            // 'Note: the R-squared value for this approximation is .988
            $rgb['r'] = 329.698727446 * (($tmpKelvin - 60) ^-0.1332047592);

            // Note: the R-squared value for this approximation is .987
            $rgb['g'] = 288.1221695283 * (($tmpKelvin - 60) ^ -0.0755148492);
        }

        if ($tmpKelvin >= 66) {
            $rgb['b'] = 255;
        } else if ($tmpKelvin <= 19) {
            $rgb['b'] = 0;
        } else {
            // Note: the R-squared value for this approximation is .998
            $rgb['b'] = 138.5177312231 * log($tmpKelvin - 10) - 305.0447927307;
        }

        return array_map(function($channel) {
            if ($channel > 255) return 255;
            if ($channel < 0) return 0;

            return $channel;
        }, $rgb);
    }

    /**
     * Returns habitable zone in AU from Star; planets within this zone will
     * be warm enough to have liquid water.
     *
     * Multiply by 149.6 to get km
     * @return Range
     */
    public function habitableZone(): Range
    {
        return new Range(
            round(sqrt($this->luminosity/1.1), 3),
            round(sqrt($this->luminosity/0.53), 3)
        );
    }

    /**
     * Frost Line / Snow Line
     *
     * The distance (in AU) from a central proto-star at which ice grains can form. Gas planets typically
     * only exist beyond the frost line.
     *
     * @see https://ay201b.wordpress.com/the-snow-line-in-protoplanetary-disks/
     * @return float
     */
    public function frostLine(): float
    {
        return 4.85 * sqrt($this->luminosity);
    }

    /**
     * Orbital Range
     *
     * The min/max distance (in AU) from a central proto-star at which planets can form.
     * @return Range
     */
    public function orbitalRange(): Range
    {
        return new Range(
            sqrt($this->luminosity) / 10,
            400 * $this->mass
        );
    }

    /**
     * Only stars with an M⊙ between 0.5 and 1.4 are thought to support
     * "Earth like" life and only then if they are more than 3.5 billion
     * years old.
     *
     * @return bool
     */
    public function earthLikeLife(): bool
    {
        if ($this->mass >=0.5 && $this->mass <=1.4) {
            return $this->age >= 3.5;
        }

        return false;
    }

    public function description(): string
    {
        return $this->spectralType->description();
    }

    /**
     * These attributes are saved, they don't have to be recalculated each time.
     * @param array $attributes
     * @return void
     */
    public function fill(array $attributes = []): void
    {
        $this->spectralType = isset($attributes['spectralType']) ? SpectralType::from($attributes['spectralType']) : SpectralType::M;
        $this->mass = $attributes['mass'] ?? 0;
        $this->luminosity = $attributes['luminosity'] ?? 0.00;
        $this->lifetime = $attributes['maxAge'] ?? 0;
        $this->age = $attributes['age'] ?? 0;
        $this->radius = $attributes['radius'] ?? 0;
        $this->density = $attributes['density'] ?? 0;
        $this->temperatureK = $attributes['temperatureK'] ?? 0;
    }

    /**
     * Generate new star properties from a given solar mass. These calculations where largely taken
     * from Artifexian's "The Worldsmith" spreadsheet.
     *
     * @see https://www.youtube.com/watch?v=N40f1Sn4bDU
     * @see https://www.youtube.com/watch?v=x55nxxaWXAM
     * @param SpectralType $classification
     * @return void
     */
    public function generate(SpectralType $classification): void
    {
        $this->spectralType = $classification;
        $this->mass = $classification->massRange()->rand();

        if ($this->mass < 0.43) {
            $this->luminosity = 0.23*pow($this->mass, 2.3);
        } else if ($this->mass < 2) {
            $this->luminosity = pow($this->mass, 4);
        } else {
            $this->luminosity = 1.4*pow($this->mass,3.5);
        }

        $this->lifetime = (float) ($this->mass / $this->luminosity) * 10;

        $this->age = (float) (new Range(0.0, $this->lifetime))->rand();

        // White Dwarfs are a special case
        $this->radius = $this->spectralType === SpectralType::D
            ? pow($this->mass,-(1/3))
            : ($this->mass < 1.0 ? pow($this->mass,0.8) : pow($this->mass,0.57));

        $this->density = $this->mass / pow($this->radius,3);

        // TODO White dwarf types:
        // https://www.frontiersin.org/articles/10.3389/fspas.2021.799210/full#h6
        // https://www.tat.physik.uni-tuebingen.de/~kley/lehre/studsemi/WZ/rpv53i7p837.pdf
        // https://www.aanda.org/articles/aa/pdf/2005/38/aa2996-05.pdf
        $this->temperatureK = pow($this->luminosity/pow($this->radius,2),0.25)*5776;
    }
}
