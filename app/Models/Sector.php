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

use App\Types\Geometry\Point;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Galaxy Sectors
 *
 * The galactic map is split by a grid of sectors (as determined by game.map_size / game.sector_size),
 * these sectors are essentially used as a "spacial hash grid" for determining which systems can have
 * a warp gate to other systems.
 *
 * The generated Galaxy can be assumed to have the same radius as The Milky Way: 52,850 light years. Given that
 * each Star is plotted with polar coordinates with its distance normalised as a value between zero and one,
 * the distance from the galactic center is trivial to calculate.
 *
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property int $x
 * @property int $y
 * @property string $hash
 * @property int $system_count
 *
 * @property-read Collection<System> $systems
 *
 * @method static create(array $attributes) Sector
 */
class Sector extends Model
{
    protected $fillable = ['x', 'y'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Sector $sector) {
            if (!isset($sector->hash)) {
                $sector->hash = Sector::makeHash($sector->x, $sector->y);
            }
        });
    }

    public function systems(): HasMany
    {
        return $this->hasMany(System::class, 'sector_id');
    }

    /**
     * @return array<System|null>
     */
    public function edgeSystems(): array
    {
        $closest = [
            'n' => null,
            'e' => null,
            's' => null,
            'w' => null,
        ];

        foreach ($this->systems as $system) {
            // Get the systems position relative to the sector
            $pos = $system->position()->subtract($this->position());

            if (is_null($closest['n'])) {
                $closest['n'] = [
                    'pos' => $pos,
                    'system' => $system,
                ];
            } else {
                if($closest['n']['pos']->y < $pos->y) {
                    $closest['n'] = [
                        'pos' => $pos,
                        'system' => $system,
                    ];
                }
            }

            if (is_null($closest['e'])) {
                $closest['e'] = [
                    'pos' => $pos,
                    'system' => $system,
                ];
            } else {
                if($closest['e']['pos']->x > $pos->x) {
                    $closest['e'] = [
                        'pos' => $pos,
                        'system' => $system,
                    ];
                }
            }

            if (is_null($closest['s'])) {
                $closest['s'] = [
                    'pos' => $pos,
                    'system' => $system,
                ];
            } else {
                if($closest['s']['pos']->y > $pos->y) {
                    $closest['s'] = [
                        'pos' => $pos,
                        'system' => $system,
                    ];
                }
            }

            if (is_null($closest['w'])) {
                $closest['w'] = [
                    'pos' => $pos,
                    'system' => $system,
                ];
            } else {
                if($closest['w']['pos']->x < $pos->x) {
                    $closest['w'] = [
                        'pos' => $pos,
                        'system' => $system,
                    ];
                }
            }
        }

        return array_map(function(array|null $item) {
            return $item['system'] ?? null;
        }, $closest);
    }

    public function north(): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($this->x, $this->y - 1))
            ->first();
    }

    public function northEast(): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($this->x + 1, $this->y - 1))
            ->first();
    }

    public function east(): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($this->x + 1, $this->y))
            ->first();
    }

    public function southEast(): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($this->x + 1, $this->y + 1))
            ->first();
    }

    public function south(): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($this->x, $this->y + 1))
            ->first();
    }

    public function southWest(): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($this->x - 1, $this->y + 1))
            ->first();
    }

    public function west(): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($this->x - 1, $this->y))
            ->first();
    }

    public function northWest(): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($this->x - 1, $this->y - 1))
            ->first();
    }

    /**
     * @return array<Sector>
     */
    public function neighbours(): array
    {
        return Sector::with('systems')
            ->whereIn('hash', $this->neighbourHashes())
            ->get()
            ->reduce(function(array $carry, Sector $sector){
                if ($sector->x === $this->x && $sector->y === $this->y - 1) {
                    $carry['n'] = $sector;
                } else if ($sector->x === $this->x + 1 && $sector->y === $this->y - 1) {
                    $carry['ne'] = $sector;
                } else if ($sector->x === $this->x + 1 && $sector->y === $this->y) {
                    $carry['e'] = $sector;
                } else if ($sector->x === $this->x + 1 && $sector->y === $this->y + 1) {
                    $carry['se'] = $sector;
                } else if ($sector->x === $this->x && $sector->y == $this->y + 1) {
                    $carry['s'] = $sector;
                } else if ($sector->x === $this->x - 1 && $sector->y === $this->y + 1) {
                    $carry['sw'] = $sector;
                } else if ($sector->x === $this->x - 1 && $sector->y === $this->y) {
                    $carry['w'] = $sector;
                } else if ($sector->x === $this->x - 1 && $sector->y === $this->y - 1) {
                    $carry['nw'] = $sector;
                }

                return $carry;
            }, []);
    }

    public function neighbourHashes(): array
    {
        return [
            'n' => Sector::makeHash($this->x, $this->y - 1), // N
            'ne' => Sector::makeHash($this->x + 1, $this->y - 1), // NE
            'e' => Sector::makeHash($this->x + 1, $this->y), // E
            'se' => Sector::makeHash($this->x + 1, $this->y + 1), // SE
            's' => Sector::makeHash($this->x, $this->y + 1), // S
            'sw' => Sector::makeHash($this->x - 1, $this->y + 1), // SW
            'w' => Sector::makeHash($this->x - 1, $this->y), // W
            'nw' => Sector::makeHash($this->x - 1, $this->y - 1), // NW
        ];
    }

    public function updateSystemCount(): void
    {
        $this->system_count = $this->systems()->count();
        $this->save();
    }

    /**
     * Returns the Sectors position within the game map with the origin (0,0) being the top left.
     * @return Point
     */
    public function position(): Point
    {
        // This (x,y) has the origin (0,0) be in the center of the plane, therefore it is a simple case of
        // treating them as offsets in order to get the position relative to (0,0) being top left.

        $origin = setting('game.map_size') / 2;

        return new Point(
            $origin + ($this->x * setting('game.sector_size')),
            $origin + ($this->y * setting('game.sector_size'))
        );
    }

    public static function findByXY(int $x, int $y): Sector|Model|null
    {
        return Sector::with('systems')
            ->where('hash', Sector::makeHash($x, $y))
            ->first();
    }

    public static function makeHash(int $x, int $y): string
    {
        return "$x.$y";
    }

    /**
     * Returns a hashmap containing all sectors with at least one system
     * @return Sector[]
     */
    public static function hashMap(): array
    {
        return Sector::with(['systems'])
            ->where('system_count', '>', 0)
            ->get()
            ->reduce(function(array $carry, Sector $sector){
                $carry[$sector->hash] = $sector;
                return $carry;
            }, []);
    }

}
