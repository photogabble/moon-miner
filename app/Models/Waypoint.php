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
use App\Types\QuadTree\Insertable;
use App\Models\Traits\HasPolarCoordinates;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parental\HasChildren;

/**
 * A waypoint is anything in a system that can be travelled to by the player. Currently, ship navigation is only
 * interested in navigation (RealSpace or warp) between sectors, docking at ports or landing on planets. All
 * travel consumes turns but otherwise happens instantly. There is a setting within Trade Wars 2002 that adds
 * a delay to travel, I might implement that at some point in the future.
 *
 * Waypoints can have a primary that they orbit with angle, distance, eccentricity and inclination describing
 * their orbit. This allows Waypoints to orbit other waypoints, for example moons orbiting planets, planets
 * orbiting a star with the stars primary being null.
 *
 * With this setup its possible for a sector to contain more than one solar system (two or more stars) or a single
 * solar system (one star, or binary pair.)
 *
 * @property int $id
 * @property int $system_id
 * @property int|null $owner_id
 * @property int|null $primary_id
 * @property float $angle
 * @property float $distance
 * @property float $eccentricity
 * @property float $inclination
 * @property WaypointType $type
 * @property string $name
 *
 * @property-read Collection<Waypoint> $orbitals
 * @property-read User|null $owner
 * @property-read System $system
 */
class Waypoint extends Model implements ToCartesian, Insertable
{
    use HasFactory, HasChildren, HasPolarCoordinates;

    protected $fillable = [
        'type',
        'system_id'
    ];

    protected $casts = [
        'type' => WaypointType::class, // TODO should this just be traits?
        'properties' => 'json',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function orbitals(): HasMany
    {
        return $this->hasMany(Waypoint::class, 'primary_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function distanceTo(Waypoint $waypoint): float
    {
        return $this->distanceToPolar($waypoint->angle, $waypoint->distance);
    }
}
