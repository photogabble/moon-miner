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

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Types\LogType;

/**
 * @property int $amount
 * @property int $bounty_on
 * @property int|null $placed_by
 *
 * @property-read User $bountyOn
 * @property-read User $placedBy
 */
class Bounty extends Model
{
    public function bountyOn(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bounty_on');
    }

    public function placedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'placed_by');
    }

    public function cancel() : void
    {
        if ($placedBy = $this->placedBy) {
            $characterName = $this->bountyOn->name;
            PlayerLog::writeLog($this->placed_by, LogType::LOG_BOUNTY_CANCELLED, "$this->amount|$characterName");

            $placedBy->wallet()->credit($this->amount, "Bounty refund");
        }

        $this->delete();
    }

    public function collect(User $attacker): void
    {
        if ($attacker->id === $this->bounty_on) {
            throw new Exception("Can't collect bounty on self");
        }

        $characterName = $this->bountyOn->name;

        if (is_null($this->placed_by)) {
            $placed = __('bounty.l_by_thefeds');
        } else {
            $placed = $this->placedBy->name;
            PlayerLog::writeLog($this->placed_by, LogType::LOG_BOUNTY_PAID, "$this->amount|$characterName|");
        }

        // TODO: lang
        $attacker->wallet()->credit($this->amount, 'Bounty paid on ' . $characterName);
        PlayerLog::writeLog($attacker->id, LogType::LOG_BOUNTY_CLAIMED, "$this->amount|$characterName|$placed");

        $this->delete();
    }
}
