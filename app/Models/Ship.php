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

namespace App\Models;

use Exception;
use App\Types\Value;
use App\Helpers\CalcLevels;
use App\Types\MovementMode;
use App\Models\MovementLog;
use App\Types\EncounterType;
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
 *
 * @property int $system_id
 * @property int|null $waypoint_id
 *
 * @property int $ship_ore
 * @property int $ship_organics
 * @property int $ship_goods
 * @property int $ship_energy
 * @property int $ship_colonists
 * @property int $ship_fighters
 *
 * @property int $ship_damage
 * @property int $turns
 *
 * @property bool $on_planet
 * @property bool $is_docked replaces on_planet
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
 * @property-read System $system
 * @property-read User|null $owner
 */
class Ship extends Model
{

    /**
     * Is this ship in space or docked at port, landed on planet, etc.
     * @return bool
     */
    public function inSpace(): bool
    {
        return is_null($this->planet_id);
    }

    public function onPlanet(): bool
    {
        return !is_null($this->planet_id) && $this->on_planet === true;
    }

    public function inOrbit(): bool
    {
        return !is_null($this->planet_id) && $this->on_planet === false;
    }

    /**
     * @todo future mechanic where players can dock with stations to explore and/or run missions
     * @return bool
     */
    public function isDocked(): bool
    {
        return false;
    }

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

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    /**
     * Refactored from CalcLevels::avgTech
     * @return float
     */
    public function avgTechLevel(): float {
        $values = array_map(function(string $key){
            return $this->{$key};
        }, ['hull', 'engines', 'computer', 'armor', 'shields', 'beams', 'torp_launchers']);

        return array_sum($values) / count($values);
    }

    /**
     * Returns the ships integer level based upon its avg tech level.
     * Refactored from main.php.
     * @return int
     */
    public function level(): int {
        $avg = $this->avgTechLevel();

        if ($avg < 8) return 0;
        if ($avg < 12) return 1;
        if ($avg < 16) return 2;
        if ($avg < 20) return 3;

        return 4;
    }

    public function armor(): Value
    {
        return new Value($this->armor_pts, CalcLevels::maxLevels($this->armor));
    }

    public function fighters(): Value
    {
        return new Value($this->ship_fighters, CalcLevels::maxLevels($this->computer));
    }

    public function torpedoes(): Value
    {
        return new Value($this->torps, CalcLevels::maxLevels($this->torp_launchers));
    }

    public function energy(): Value
    {
        return new Value($this->ship_energy, CalcLevels::maxLevels($this->power));
    }

    /**
     * In the classic game of BNT the ships inventory was managed via various cargo hold
     * values stored within the ships table. I have chosen to refactor this into a
     * volumetric inventory system where each commodity has a fixed volume.
     *
     * @return void
     */
    public function inventory()
    {

        // TODO

    }

    /**
     * Moving the ship, does just that, with no checks to see if the ship can
     * travel there under its own power. To be used for spawning and towing.
     *
     * @param int $systemId
     * @param MovementMode $mode
     * @return MovementLog
     */
    public function moveTo(int $systemId, MovementMode $mode): MovementLog
    {
        return MovementLog::writeLog($this->owner_id, $systemId, $mode);
    }

    /**
     * This function handles moving the player between sectors, it returns a movement log which
     * can contain events that have happened during travel.
     *
     * @param int $systemId
     * @param MovementMode $mode
     * @param int $turnsUsed
     * @param int $energyScooped
     * @return MovementLog
     * @throws Exception
     */
    public function travelTo(int $systemId, MovementMode $mode, int $turnsUsed, int $energyScooped = 0): MovementLog
    {
        $this->owner->spendTurns($turnsUsed);

        // TODO: travelling should cost some energy
        if ($energyScooped > 0) $this->increment('ship_energy', $energyScooped);

        $movement = MovementLog::writeLog($this->owner_id, $systemId, $mode, $turnsUsed, $energyScooped);
        $this->system_id = $systemId;
        $this->save();

        // TODO fighters encounter (check_fighters.php)
        // TODO mines encounter (check_mines.php)
        // TODO random encounters

        $movement->encounter()->save(new Encounter([
            'system_id' => $systemId,
            'user_id' => $this->owner_id,
            'type' => EncounterType::Navigation,
            'state' => [
                'energy_scooped' => $energyScooped,
                'turns_used' => $turnsUsed,
            ],
        ]));

        return $movement;
    }

    /**
     * Added so that different ship classes can cost different amount of turns for travel.
     * @todo make warp travel cost differently depending upon ship class
     * @return int
     */
    public function warpTravelTurnCost(): int
    {
        return 1;
    }
}
