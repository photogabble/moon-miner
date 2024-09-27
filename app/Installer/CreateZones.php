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

use App\Models\Zone;
use App\Types\InstallConfig;
use App\Types\ZonePermission;
use Illuminate\Console\OutputStyle;

class CreateZones extends Step implements InstallStep
{
    public function execute(OutputStyle $output, InstallConfig $config): int
    {
        $this->timer->start();

        // Default permission is Allow, only need to set the denies

        // Insert Uncharted Zone
        $zone = new Zone();
        $zone->name = 'Uncharted space';
        $zone->save();

        $this->logger->info(__('create_universe.l_cu_setup_unchartered', ['elapsed' => $this->timer->sample()]));

        // Insert Federation Zone
        $zone = new Zone();
        $zone->name = 'Federation space';
        $zone->allow_beacon = ZonePermission::Deny;
        $zone->allow_attack = ZonePermission::Deny;
        $zone->allow_planetattack = ZonePermission::Deny;
        $zone->allow_warpedit = ZonePermission::Deny;
        $zone->allow_planet = ZonePermission::Deny;
        $zone->allow_defenses = ZonePermission::Deny;
        $zone->max_hull = config('game.fed_max_hull');
        $zone->save();

        $this->logger->info(__('create_universe.l_cu_setup_fedspace', ['elapsed' => $this->timer->sample()]));

        // Insert Free Trade Zone
        $zone = new Zone();
        $zone->name = 'Free-Trade space';
        $zone->allow_beacon = ZonePermission::Deny;
        $zone->allow_planetattack = ZonePermission::Deny;
        $zone->allow_warpedit = ZonePermission::Deny;
        $zone->allow_planet = ZonePermission::Deny;
        $zone->allow_defenses = ZonePermission::Deny;
        $zone->save();

        $this->logger->info(__('create_universe.l_cu_setup_free', ['elapsed' => $this->timer->sample()]));

        // Insert War Zone
        $zone = new Zone();
        $zone->name = 'War Zone';
        $zone->allow_trade = ZonePermission::Deny;
        $zone->save();

        $this->logger->info(__('create_universe.l_cu_setup_warzone', ['elapsed' => $this->timer->sample()]));

        return 0;
    }
}
