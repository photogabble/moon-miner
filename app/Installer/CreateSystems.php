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

namespace App\Installer;

use App\Models\System;
use App\Models\Sector;
use App\Generators\Galaxy;
use App\Types\SpectralType;
use App\Types\InstallConfig;
use App\Models\Waypoints\Star;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\OutputStyle;

/**
 * Generate and Persist Solar Systems
 *
 * To begin with all systems will be assigned a name based upon the quadrant of the galaxy it belongs, how
 * far it is from the galactic core and an incrementing ordinal displayed as hex.
 * Once the map has been persisted and jump gates seeded between systems it will then be possible to
 * seed several empire home worlds and begin naming systems as they drunk-walk from system to system.
 */
class CreateSystems extends Step implements InstallStep
{

    /**
     * @throws \Exception
     */
    public function execute(OutputStyle $output, InstallConfig $config): int
    {
        $this->timer->start();

        $galaxy = app(Galaxy::class);

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
                $output->writeLn('<error>[!]</error> Unable to find sector for (' . $star->toCartesian()->x. ',' . $star->toCartesian()->y .') with hash ['.$sectorHash.']');
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

        return 0;
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
    }
}
