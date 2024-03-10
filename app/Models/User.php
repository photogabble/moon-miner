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

use App\Types\WalletType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mchev\Banhammer\Traits\Bannable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

/**
 * This User class has been refactored from the legacy
 * classes/Players/PlayersGateway.php file.
 *
 * @property int $id
 * @property int $ship_id
 * @property string $name
 *
 * @property-read Collection<PlayerLog> $logEntries
 * @property-read Ship|null $ship
 * @property-read Collection<Ship> $ships
 * @property-read Collection<Bounty> $bounties
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Bannable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Active players are those who have been active within the past five minutes.
     * @return int
     */
    public static function activePlayerCount(): int
    {
        return User::query()
            ->whereBetween('last_login', [
                Carbon::now()->subMinutes(5),
                Carbon::now()
            ])->count();
    }

    /**
     * Players can own more than one ship.
     * @return HasMany
     */
    public function ships(): HasMany
    {
        return $this->hasMany(Ship::class, 'owner_id');
    }

    /**
     * The ship that this player is currently flying.
     * @return BelongsTo
     */
    public function ship(): BelongsTo
    {
        return $this->belongsTo(Ship::class);
    }

    public function logEntries(): HasMany
    {
        return $this->hasMany(PlayerLog::class);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function wallet(WalletType $type = WalletType::Personal): ?Wallet
    {
        return $this->wallets()->where('type', $type)->first();
    }

    public function bounties(): HasMany
    {
        return $this->hasMany(Bounty::class, 'bounty_on');
    }
}
