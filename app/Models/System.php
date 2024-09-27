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

use App\Types\WaypointType;
use Illuminate\Support\Facades\DB;
use App\Types\Geometry\Point;
use App\Types\QuadTree\Insertable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\HasPolarCoordinates;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Systems
 *
 * Systems are navigable units of space and may, or may not contain one or more
 * stars and their corresponding orbitals. Players can real space travel between
 * systems, while WarpGates within systems can be used for cheaper travel to the
 * systems that they link to.
 *
 * When Real Space travelling between systems the player will be placed in orbit
 * around the first Star in the system.
 *
 * @property int $id
 * @property string $name
 *
 * @property int $zone_id
 * @property float $angle
 * @property float $distance
 *
 * @property-read Zone $zone
 * @property-read Collection<Waypoint> $waypoints
 * @property-read Collection<Ship> $ships
 * @property-read Collection<SectorDefense> $defenses
 * @property-read Collection<MovementLog> $movementLog
 * @property-read Collection<Link> $links
 *
 * Virtual Attributes, these are set by the queryForUser method or elsewhere
 * when needing to flag one or all of the below values:
 * @property bool $has_visited
 * @property bool $is_current_sector
 * @property bool $has_danger
 */
class System extends Model implements ToCartesian, Insertable
{
    use HasFactory, HasPolarCoordinates;

    protected $casts = [
        // These are virtual attributes, filled by the queryForUser method.
        'is_current_sector' => 'bool',
        'has_visited' => 'bool',
        'has_danger' => 'bool',
    ];

    /**
     * Each system can belong to a zone, this controls what activities are allowed.
     * @return BelongsTo
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Systems are seeded with waypoints upon universe creation. Waypoints have replaced the
     * legacy planet and link concepts.
     *
     * @return HasMany
     */
    public function waypoints(): HasMany
    {
        return $this->hasMany(Waypoint::class);
    }

    /**
     * @param WaypointType $type
     * @return Collection<Waypoint>
     */
    public function waypointsOfType(WaypointType $type): Collection
    {
        return $this->waypoints()
            ->where('type', $type)
            ->get();
    }

    public function defenses(): HasMany
    {
        return $this->hasMany(SectorDefense::class, 'sector_id');
    }

    /**
     * Systems are linked to one another via jump routes, these are made available to
     * players via WarpGate waypoints.
     *
     * @return HasMany
     */
    public function links(): HasMany
    {
        return $this->hasMany(Link::class, 'left_system_id');
    }

    /**
     * Which ships are currently located within this system?
     * @return HasMany
     */
    public function ships(): HasMany
    {
        return $this->hasMany(Ship::class, 'system_id');
    }

    public function movementLog(): HasMany
    {
        return $this->hasMany(MovementLog::class, 'sector_id');
    }

    public function latestMovementLog(): HasOne
    {
        return $this->hasOne(MovementLog::class, 'sector_id')->latestOfMany();
    }

    /**
     * Returns the Systems position within the game map with the origin (0,0) being the top left.
     * @return Point
     */
    public function position(): Point
    {
        $origin = setting('game.map_size') / 2;
        $position = $this->toCartesian()
            ->scale($origin);

        return new Point(
            $origin + $position->x,
            $origin + $position->y
        );
    }

    /**
     * This functionality was refactored from rsmove.php, which was also
     * similar or the same as classes/Realspace.php.
     * @param Ship $ship
     * @param \Tki\Models\System $destination
     * @return array
     */
    public function calculateRealSpaceMove(Ship $ship, System $destination): array
    {
        if ($destination->id === $ship->sector_id) {
            return [
                'turns' => 0,
                'energyScooped' => 0,
            ];
        }

        // Calculate the distance.
        $deg = pi() / 180;
        $sa1 = $this->angle1 * $deg;
        $sa2 = $this->angle2 * $deg;
        $fa1 = $destination->angle1 * $deg;
        $fa2 = $destination->angle2 * $deg;

        $xx = ($this->distance * sin($sa1) * cos($sa2)) - ($destination->distance * sin($fa1) * cos($fa2));
        $yy = ($this->distance * sin($sa1) * sin($sa2)) - ($destination->distance * sin($fa1) * sin($fa2));
        $zz = ($this->distance * cos($sa1)) - ($destination->distance * cos($fa1));

        $distance = (int)round(sqrt(pow($xx, 2) + pow($yy, 2) + pow($zz, 2)));

        // Calculate the speed of the ship.
        $shipSpeed = pow(config('game.level_factor'), $ship->engines);

        // Calculate the trip time.
        $turns = (int)round($distance / $shipSpeed);

        return [
            'turns' => $turns,
            'energyScooped' => \App\Actions\Move::calcFuelScooped($ship, $distance, $turns),
        ];
    }

    /**
     * Helper function for getting a sector (or sectors) from the point of view of the
     * player. If this begins getting more complex, maybe turn into a macro.
     *
     * @param User $user
     * @return Builder
     */
    public static function queryForUser(User $user): Builder
    {
        return System::query()
            ->select([
                'systems.*',
                DB::raw("(SELECT COUNT(id) FROM movement_logs WHERE `movement_logs`.`sector_id` = `systems`.`id` AND `movement_logs`.`user_id` = $user->id) > 0 as has_visited"),
                DB::raw("(SELECT COUNT(id) FROM ships WHERE `ships`.`sector_id` = `systems`.`id` AND `ships`.`id` = $user->ship_id) > 0 as is_current_sector"),
            ]);
    }

}
