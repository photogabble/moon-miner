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

use App\Types\WalletType;
use Illuminate\Support\Carbon;
use App\Exceptions\WalletException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $user_id
 * @property int $balance
 * @property WalletType $type
 */
class Wallet extends Model
{
    protected $fillable = ['user_id'];

    protected $casts = [
        'type' => WalletType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function debit(int $amount, ?string $description = null, ?Wallet $destination = null): void
    {
        if (!is_null($destination) && $destination->is($this)) {
            throw new WalletException('You are attempting to transfer funds to the same wallet.', WalletException::SAME_WALLET);
        }

        if ($this->type !== WalletType::Loan && $amount > $this->balance) {
            throw new WalletException('Insufficient funds', WalletException::INSUFFICIENT_FUNDS);
        }

        // Observer on the transaction will update the Wallet balance upon save.

        $transaction = new WalletTransaction();
        $transaction->wallet_id = $this->id;
        $transaction->debit = $amount;
        $transaction->description = $description;
        $transaction->save();

        if (is_null($destination)) return;

        $destination->credit($amount, $description, $this);
    }

    public function credit(int $amount, ?string $description, ?Wallet $origin = null): void
    {
        if (!is_null($origin) && $origin->is($this)) {
            throw new WalletException('You are attempting to transfer funds to the same wallet.', WalletException::SAME_WALLET);
        }

        $transaction = new WalletTransaction();
        $transaction->wallet_id = $this->id;
        $transaction->credit = $amount;
        $transaction->description = $description;

        if (!is_null($origin)) {
            $transaction->origin_wallet_id = $origin->id;
        }

        $transaction->save();
    }

    public function transferOutToWallet(Wallet $destination, int $amount, ?string $description = null): void
    {
        if ($destination->is($this)) {
            throw new WalletException('You are attempting to transfer funds to the same wallet.', WalletException::SAME_WALLET);
        }

        if ($this->type !== WalletType::Loan && $amount > $this->balance) {
            throw new WalletException('Insufficient funds', WalletException::INSUFFICIENT_FUNDS);
        }

        $this->decrement('balance', $amount);

        // Record transaction sending credits from this wallet to destination
        $transaction = new WalletTransaction();
        $transaction->source_wallet_id = $this->id;
        $transaction->dest_wallet_id = $destination->id;
        $transaction->amount = $amount;
        $transaction->description = $description;
        $transaction->save();

        $destination->transferInFromWallet($this, $amount, $description);
    }
}
