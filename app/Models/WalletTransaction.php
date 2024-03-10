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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $wallet_id
 * @property int|null $origin_wallet_id
 * @property int|null $debit
 * @property int|null $credit
 * @property int $balance
 * @property string|null $description
 *
 * @property-read Wallet $wallet
 * @property-read int $transaction_amount
 */
class WalletTransaction extends Model
{

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getTransactionAmountAttribute(): int
    {
        return $this->credit ?: -($this->debit);
    }

    public function save(array $options = []): bool
    {
        $this->balance = $this->wallet->balance + $this->transaction_amount;

        if (!parent::save($options)) return false;

        $this->wallet->balance = $this->balance;
        return $this->wallet->save();
    }

}
