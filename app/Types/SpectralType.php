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

namespace App\Types;

use App\Helpers\Range;

/**
 * Extracted from The Morgan-Keenan Spectral Types (Main Sequence, Extended)
 * @see http://www.handprint.com/ASTRO/specclass.html
 */
enum SpectralType: string
{
    case O = 'O';
    case B = 'B';
    case A = 'A';
    case F = 'F';
    case G = 'G';
    case K = 'K';
    case M = 'M';
    case C = 'C';
    case S = 'S';
    case D = 'D';

    /**
     * @todo Use $distance to return a correct star type for distance from galactic center
     * @todo currently does not take into account galactic habitable zone
     * @todo add black holes, neutron stars
     * @param float|null $distance
     * @return SpectralType
     */
    public static function fromGalacticDistance(?float $distance = null): SpectralType
    {
        $options = [
            [
                'star' => self::O,
                'prevalence' => 0.00005,
            ],
            [
                'star' => self::B,
                'prevalence' => 0.039,
            ],
            [
                'star' => self::A,
                'prevalence' => 0.6
            ],
            [
                'star' => self::F,
                'prevalence' => 2.9
            ],
            [
                'star' => self::G,
                'prevalence' => 4.1
            ],
            [
                'star' => self::K,
                'prevalence' => 12.9
            ],
            [
                'star' => self::M,
                'prevalence' => 72.5
            ],
            [
                'star' => self::C,
                'prevalence' => 0.92
            ],
            [
                'star' => self::S,
                'prevalence' => 0.14
            ],
            [
                'star' => self::D,
                'prevalence' => 5.9,
            ]
        ];

        $weights = [];

        for ($i = 0; $i < count($options); $i++) {
            $weights[$i] = $options[$i]['prevalence'] + ($weights[$i - 1] ?? 0);
        }

        $random = ((float)mt_rand() / (float)mt_getrandmax()) * $weights[count($weights) - 1];

        for ($i = 0; $i < count($weights); $i++) {
            if ($weights[$i] > $random)
                break;
        }

        return $options[$i]['star'];
    }

    public function description(): string
    {
        return match ($this) {
            self::O => 'Super Massive',
            self::B => 'Massive',
            self::A => 'Large',
            self::F, self::G, self::K => 'Solar',
            self::M => 'Sub Solar',
            self::C => 'Carbon',
            self::S => 'Sub Carbon',
            self::D => 'White Dwarf',
        };
    }

    /**
     * Returns values as multiples of M⊙, where M⊙ is the mass of the Sun.
     * @return Range<float>
     */
    public function massRange(): Range // M⊙
    {
        return match ($this) {
            self::O => new Range(18.0, 150.0),
            self::B => new Range(2.9, 18.0),
            self::A => new Range(1.6, 2.9),
            self::F => new Range(1.05, 1.60),
            self::G => new Range(0.8, 1.05),
            self::K => new Range(0.5, 0.8),
            self::M => new Range(0.07, 0.5),
            self::C => new Range(0.8, 1.1),
            self::S => new Range(0.5, 0.8),
            self::D => new Range(0.17, 1.3),
        };
    }

    /**
     * Returns a range of Kelvin values for this star classification.
     * @return Range
     */
    public function temperatureRange(): Range // Kelvin
    {
        return match ($this) {
            self::O => new Range(30000, 50000),
            self::B => new Range(10000, 30000),
            self::A => new Range(7300, 10000),
            self::F => new Range(6000, 7300),
            self::G => new Range(5300, 6000),
            self::K => new Range(3800, 5300),
            self::M => new Range(2500, 3800),
            self::C => new Range(2400, 3200),
            self::S => new Range(2400, 3500),
            self::D => new Range(80000, 100000),
        };
    }

    /**
     * Returns values as multiples of R⊙, where R⊙ is the radius of the Sun.
     * @return Range<float>
     */
    public function radiusRange(): Range // R⊙
    {
        return match ($this) {
            self::O => new Range(6.6, 7),
            self::B => new Range(1.8, 6.6),
            self::A => new Range(1.4, 1.8),
            self::F => new Range(1.15, 1.4),
            self::G => new Range(0.96, 1.15),
            self::K => new Range(0.7, 0.96),
            self::M => new Range(0.5, 0.7),
            self::C => new Range(220, 550), // giant and supergiant stars
            self::S => new Range(0.5, 0.7),
            self::D => new Range(0.008, 0.02),
        };
    }
}
