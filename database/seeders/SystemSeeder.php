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


namespace Database\Seeders;

use App\Models\System;
use App\Models\Sector;
use App\Generators\Galaxy;
use App\Types\SpectralType;
use App\Models\Waypoints\Star;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Generate and Persist Solar Systems
 *
 * To begin with all systems will be assigned a name based upon the quadrant of the galaxy it belongs, how
 * far it is from the galactic core and an incrementing ordinal displayed as hex.
 * Once the map has been persisted and jump gates seeded between systems it will then be possible to
 * seed several empire home worlds and begin naming systems as they drunk-walk from system to system.
 */
class SystemSeeder extends Seeder
{
    public function run(Galaxy $galaxy): void
    {
        /** @var []Sector $sectorMap */
        $sectorMap = Sector::all()->reduce(function(array $map, Sector $sector) {
            $map[$sector->hash] = $sector;
            return $map;
        }, []);

        $stars = $galaxy->generate();
        $starOrdinal = 0;

        DB::beginTransaction();
        foreach ($stars as $star) {
            // By default, the game map is 4000 units wide this means we go from -2000 through zero to +2000 on the
            // (x,y) axis. The coordinates that come out of the Galaxy generator class will always be between -1.0
            // and +1.0 therefore they need scaling by half the map size to get their position relative to the
            // sector grid.
            $sectorHash = $star->toCartesian()
                ->scale(setting('game.map_size') / 2)
                ->toSectorHash();

            if (!isset($sectorMap[$sectorHash])){
                $this->command->getOutput()->writeLn('Unable to find sector for (' . $star->toCartesian()->x. ',' . $star->toCartesian()->y .') with hash ['.$sectorHash.']');
                continue;
            }

            /** @var Sector $sector */
            $sector = $sectorMap[$sectorHash];

            $system = new System();
            $system->name = polar_to_name($star->angle, $star->radius) . '-' . strtoupper(dechex($starOrdinal));
            $system->angle = $star->angle;
            $system->distance = $star->radius;

            $sector->systems()->save($system);
            $this->generateSolarSystem($system);

            $starOrdinal++;
        }
        DB::commit();

        DB::beginTransaction();
        foreach ($sectorMap as $sector) {
            $sector->updateSystemCount();
        }
        DB::commit();
    }

    private function generateSolarSystem(System $system): void
    {
        $type = SpectralType::fromGalacticDistance($system->distance);

        $star = new Star();

        $star->name = $system->name;
        $star->distance = 0.0; // Star is at the center of the system
        $star->angle = 0.0;
        $star->inclination = 0.0;
        $star->eccentricity = 0.0;

        $star->properties->generate($type);

        $system->waypoints()->save($star);

        // Use https://www.1728.org/kepler3a.htm for calculating orbital period of planets
        // based upon distance from star.

        // 1. Begin at the frost line
        // 2. Select a random number between 1.4 and 2.0, multiply previous planets distance by that, this is the
        //    next stable orbit from the star.
        // 3. Continue until you have orbits all the way out to the outer limit
        // 4. Do the same for inner orbits, but dividing by between 1.4 and 2.0 down to the inner limit.
        // 5. loop through the list of orbital shells and populate them at random with a new Planet, the
        //    type of planet will be determined by how far away it is from the star


        // eccentricity for inner system planets should be kept to 0.0x to 0.00x, can be larger for far out planets
        // inclination for inner system planets should be kept < 15, can be larger for far out planets

    }
}
