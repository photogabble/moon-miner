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
use Carbon\Carbon;
use Parental\HasChildren;
use Illuminate\View\View;
use App\Types\EncounterType;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use App\Actions\Encounters\EncounterOption;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Carbon|null $completed_at
 * @property int $movement_id
 * @property int $sector_id
 * @property int $user_id
 * @property EncounterType $type
 * @property array $state
 *
 * @property-read User $user
 * @property-read System $system
 * @property-read MovementLog $movement
 */
class Encounter extends Model
{
    use HasChildren;

    protected $casts = [
        'type' => EncounterType::class,
        'completed_at' => 'datetime',
        'state' => 'json',
    ];

    protected $fillable = [
        'type',
        'system_id',
        'user_id',
        'state',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    public function movement(): BelongsTo
    {
        return $this->belongsTo(MovementLog::class, 'movement_id');
    }

    public function persistData(array $data): void
    {
        $this->data = $data;
        $this->save();
    }

    /**
     * Intended to be overridden by child classes, this is the title of the encounters modal
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return null;
    }

    /**
     *
     * @return string
     */
    public function render(): ?View
    {
        return null;
    }

    public function isDangerous(): bool
    {
        return false;
    }

    /**
     * List of actions that the player can do for this encounter.
     * @return array
     */
    public function options(): array
    {
        return [];
    }

    public function hasOption(string $option): bool
    {
        return in_array($option, array_keys($this->options()));
    }

    public function make(string $option): EncounterOption
    {
        if (!$this->hasOption($option)) {
            throw new Exception("Invalid Encounter action [$option]");
        }

        /** @var EncounterOption $class */
        $class = Container::getInstance()->get($this->options()[$option]['class']);
        $class->setEncounter($this);

        return $class;
    }

    /**
     * @param string $option
     * @param array $payload
     * @return bool
     * @throws Exception
     */
    public function do(string $option, array $payload = []): bool
    {
        return $this->make($option)->execute($payload);
    }
}
