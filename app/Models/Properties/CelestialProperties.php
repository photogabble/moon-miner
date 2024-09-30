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

use App\Models\Waypoints\Star;
use App\Models\Waypoints\Planet;

/**
 * Celestial Properties
 *
 * These are borrowed from Eve Online and are currently more for
 * lore than used by any game mechanics.
 *
 * Eventually I would like planet resources to be determined by their
 * properties.
 *
 * @method static CelestialProperties make(array $attributes = [])
 */
final class CelestialProperties extends ModelProperties
{
    // Celestial Attributes, borrowed from Eve Online.
    // These are more for lore than used by any game mechanics.
    public float $density; // g/cm³
    public float $eccentricity;
    public float $escapeVelocity; // km/s
    public float $massDust; // kg
    public float $massGas; //kg
    public float $orbitPeriod; // days
    public int $orbitRadius; // AU
    public string $pressure; // Very low, low, medium, high, very high ???
    public int $radius; // km
    public float $surfaceGravity; // m/s²
    public float $temperatureK; // K

    public function fill(array $attributes = []): void
    {
        $this->density = $attributes['density'] ?? 0.0;
        $this->eccentricity = $attributes['eccentricity'] ?? 0.0;
        $this->escapeVelocity = $attributes['escapeVelocity'] ?? 0.0;
        $this->massDust = $attributes['massDust'] ?? 0.0;
        $this->massGas = $attributes['massGas'] ?? 0.0;
        $this->orbitPeriod = $attributes['orbitPeriod'] ?? 0.0;
        $this->orbitRadius = $attributes['orbitRadius'] ?? 0;
        $this->pressure = $attributes['pressure'] ?? 'Unknown';
        $this->radius = $attributes['radius'] ?? 0;
        $this->surfaceGravity = $attributes['surfaceGravity'] ?? 0.0;
        $this->temperatureK = $attributes['temperatureK'] ?? 0.0;
    }

    /**
     * Generate new celestials properties. If the primary is a Star then this will generate
     * a planet celestial, otherwise it will generate a moon celestial.
     *
     * @see https://www.youtube.com/watch?v=J5xU-8Kb63Y
     * @param Star|Planet $primary
     * @param float $distance
     * @return void
     */
    public function generate(Star|Planet $primary, float $distance): void
    {
        if ($primary instanceof Star) {
            $this->generateAsPlanet($primary, $distance);
            return;
        }

        $this->generateAsMoon($primary, $distance);
    }

    /**
     * Generates Planet properties as a function of the Star and its distance from it.
     * @param Star $primary
     * @param float $distance
     * @return void
     */
    private function generateAsPlanet(Star $primary, float $distance): void
    {
        // Star Frost line to be used as dividing line between "inner" and "outer"
        // TODO: need to identify what kind of planet this is going to be based upon where in the
        //       solar system its orbit lies. The Sol solar system seems to be quite unique, in
        //       the real world we have hot jupiter systems and failed sun systems to name but
        //       two in a growing list of odd solar systems. If a solar system has two or fewer
        //       planets and one of the orbits is inner, we could place a hot jupiter on a dice roll.
        //

        // Use https://www.1728.org/kepler3a.htm for calculating orbital period of planets
        // based upon distance from star.
        // $this->orbitPeriod = ...

        // eccentricity for inner system planets should be kept to 0.0x to 0.00x, can be larger for far out planets
        // From https://youtu.be/VGyk3ftyr_c?si=dOFAkHuldzAIPpJf&t=257 eccentricity is calculated
        // as 0.584 * $numberOfPlanets^-1.2

        // For Gas Giants use Sudarsky Classification System to identify cloud type based upon
        // surface temperature. See https://youtu.be/VGyk3ftyr_c?si=xlyA_BCEprHi5IV_&t=404

        // inclination for inner system planets should be kept < 15, can be larger for far out planets

        // Gas giants form near but not on the frost line. 1 - 1.2AU away is a good approximation
    }

    private function generateAsMoon(Planet $primary, float $distance): void
    {
        // Calculate Hill Sphere, the space around a Planet where its gravity will keep something in orbit
        // Inner: 2.44 x Planet Radius x ( PlanetDensity / MoonDensity)^0.33
        // Outer: DistanceFromStar x ( ( PlanetMass / SolarMass)^0.33 ) x 235
        // The unit of measure of the above is radius relative to the planet.
        // TODO
    }
}
