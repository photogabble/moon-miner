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
use App\Helpers\Range;
use App\Types\WaypointType;
use App\Types\InstallConfig;
use App\Models\Waypoints\Star;
use App\Models\Waypoints\Planet;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Eloquent\Collection;

// 70.php

class CreatePlanets extends Step implements InstallStep
{
    public function execute(OutputStyle $output, InstallConfig $config): int
    {
        $this->timer->start();

        // Get the sectors belonging to zones that allows planets
        /** @var System[]|Collection<System> $sectors */
        $sectors = System::query()
            ->inRandomOrder()
            ->join('zones', 'zone_id', '=', 'zones.id')
            ->where('zones.allow_planet', true)
            ->select('systems.*')
            ->get();

        $output->writeln($sectors->count() . ' sectors');

        $added = 0;
        $populatedSectors = 0;
        $default_prod_ore = config('game.default_prod_ore');
        $default_prod_organics = config('game.default_prod_organics');
        $default_prod_goods = config('game.default_prod_goods');
        $default_prod_energy = config('game.default_prod_energy');
        $default_prod_fighters = config('game.default_prod_fighters');
        $default_prod_torp = config('game.default_prod_torp');

        // Insert all planets within one transaction
        DB::beginTransaction();

        $angleRange = new Range((12 * pi()) / 6, 2 * pi());

        while ($added < $config->unownedPlanets) {
            /** @var System $sector */
            if (!$sector = $sectors->pop()) break; // Run out of sectors... this shouldn't happen but this break stops the infinite loop

            $adding = random_int(1, config('game.max_planets_sector'));

            // Ensure we don't add more than the total amount needed, if we are on the last loop, add enough
            // to complete the list.
            if ($adding + $added > $config->unownedPlanets) $adding = $config->unownedPlanets - $added;

            // Systems are generated with a single Waypoint: their Star.
            // Binary systems may be generated in the future, in which case this will need to be a loop.
            /** @var Star $star */
            $star = $sector->waypointsOfType(WaypointType::Star)->first();
            $orbits = $star->properties->generateOrbits($adding);

            foreach ($orbits as $orbit) {
                $planet = new Planet();
                $planet->primary_id = $star->id;
                $planet->distance = $orbit;
                $planet->angle = $angleRange->rand();
                $planet->type = WaypointType::Planet;

                $planet->properties->generate($star, $planet->distance);

                // TODO (#2): rework planet mechanic so that production is based upon available resources and player constructed buildings
                $planet->prod_ore = $default_prod_ore;
                $planet->prod_organics = $default_prod_organics;
                $planet->prod_goods = $default_prod_goods;
                $planet->prod_energy = $default_prod_energy;
                $planet->prod_fighters = $default_prod_fighters;
                $planet->prod_torp = $default_prod_torp;

                $planet->save();

                $added++;
            }

            $populatedSectors++;
        }

        DB::commit();

        $this->logger->info(__('create_universe.l_cu_setup_unowned_planets', ['elapsed' => $this->timer->sample(), 'nump' => $populatedSectors]));

        return 0;
    }
}
