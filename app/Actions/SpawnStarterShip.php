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

namespace App\Actions;

use App\Models\User;
use App\Models\Ship;

class SpawnStarterShip
{

    public function __construct(private User $user) {}

    public function spawn(string $name): Ship
    {
        // TODO: Create Ship Enum and have this largely placed there
        $ship = new Ship();

        $ship->owner_id = $this->user->id;
        $ship->name = $name;
        $ship->ship_destroyed = false;
        $ship->armor_pts = config('game.start_armor');
        $ship->ship_energy = config('game.start_energy');
        $ship->ship_fighters = config('game.start_fighters');
        $ship->on_planet = false;

        $ship->dev_warpedit = config('game.start_editors');
        $ship->dev_genesis = config('game.start_genesis');
        $ship->dev_beacon = config('game.start_beacon');
        $ship->dev_emerwarp = config('game.start_emerwarp');
        $ship->dev_escapepod = config('game.start_escape_pod');
        $ship->dev_fuelscoop = config('game.start_scoop');
        $ship->dev_lssd = config('game.start_lssd');
        $ship->dev_minedeflector = config('game.start_minedeflectors');

        $ship->trade_colonists = true;
        $ship->trade_fighters = false;
        $ship->trade_torps = false;
        $ship->trade_energy = true;
        $ship->cleared_defenses = null;
        $ship->system_id = 1;
        $ship->save();

        $this->user->ship()->associate($ship);

        $this->user->ship_id = $ship->id;
        $this->user->save();

        return $ship;
    }
}
