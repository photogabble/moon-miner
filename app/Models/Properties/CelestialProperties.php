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
    public float $temperature; // K

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
        $this->radius = $attributes['radius'] ?? 0.0;
        $this->surfaceGravity = $attributes['surfaceGravity'] ?? 0.0;
        $this->temperature = $attributes['temperature'] ?? 0.0;
    }
}
