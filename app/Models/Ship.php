<?php declare(strict_types=1);
/**
 * Blacknova Traders, a Free & Opensource (FOSS), web-based 4X space/strategy game.
 *
 * @copyright 2024 Simon Dann, Ron Harwood and the BNT development team
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

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * This User class has been refactored from the legacy
 * classes/Ship.php file.
 *
 * @property bool $trade_colonists
 * @property bool $trade_fighters
 * @property bool $trade_torps
 * @property bool $trade_energy
 * @property bool $cleared_defenses
 * @property int|null $planet_id
 * @property bool $ship_destroyed
 *
 * @property string $name
 * @property Carbon $destroyed_at
 * @property int $hull
 * @property int $engines
 * @property int $power
 * @property int $computer
 * @property int $sensors
 * @property int $beams
 * @property int $torp_launchers
 * @property int $torps
 * @property int $shields
 * @property int $armor
 * @property int $armor_pts
 * @property int $cloak
 * @property int $credits
 * @property int $system_id
 * @property int $ship_ore
 * @property int $ship_organics
 * @property int $ship_goods
 * @property int $ship_energy
 * @property int $ship_colonists
 * @property int $ship_fighters
 * @property int $ship_damage
 * @property int $turns
 *
 * @property bool $on_planet
 *
 * @property int $dev_warpedit
 * @property int $dev_genesis
 * @property int $dev_beacon
 * @property int $dev_emerwarp
 * @property bool $dev_escapepod
 * @property bool $dev_fuelscoop
 * @property bool $dev_lssd
 * @property int $dev_minedeflector
 *
 * @property int $owner_id
 * @property-read System $sector
 * @property-read User|null $owner
 */
class Ship extends Model
{
    /**
     * The legacy game code only allowed for players to operate
     * a single ship however, games that BNT where based upon do
     * allow for players to operate more than one ship and so
     * that is a feature I would like to add eventually.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

}
