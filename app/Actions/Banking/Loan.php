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

namespace App\Actions\Banking;

use App\Models\User;
use App\Models\Wallet;
use App\Types\WalletType;
use App\Exceptions\LoanException;
use App\Exceptions\WalletException;

class Loan
{
    public function __construct(private User $user)
    {
    }

    /**
     * This is the refactored result of Ibank::ibankBorrow.
     * Players can only have one loan at a time with each loan being capped as a percentage
     * of their score squared.
     *
     * Loans must be repaid
     *
     * @param int $amount
     * @return void
     * @throws LoanException
     * @throws WalletException
     */
    public function borrow(int $amount): void
    {
        if (!is_null($this->user->wallet(WalletType::Loan))) {
            throw new LoanException(__('bank.l_ibank_notwoloans'), LoanException::EXISTING_LOAN);
        }

        $borrowLimit = $this->getBorrowingLimit();

        if ($amount > $borrowLimit) {
            throw new LoanException(__('bank.l_ibank_loantoobig'), LoanException::LOAN_LIMIT);
        }

        $fee = $this->getBorrowingFee($amount);

        $loan = new Wallet();
        $loan->type = WalletType::Loan;

        $this->user->wallets()->save($loan);

        $loan->debit($amount + $fee, "Borrowed $amount credits, with $fee borrowing fee.", $this->user->wallet());
    }

    public function getBorrowingFee(int $amount): int
    {
        return intval($amount * config('game.ibank_loanfactor'));
    }

    public function getBorrowingLimit(): int
    {
        $score = 1000; // TODO Bnt\Score
        return intval($score * $score * config('game.ibank_loanlimit'));
    }

    public function repay(int $amount): void
    {
        if (!$loan = $this->user->wallet(WalletType::Loan)) {
            throw new LoanException(__('bank.l_ibank_notrepay'), LoanException::LOAN_NOT_FOUND);
        }

        $balance = abs($loan->balance);
        if ($amount > $balance) $amount = $balance;

        $this->user->wallet()->debit($amount, "Loan Repayment", $loan);

        $loan->refresh();
        if ($loan->balance === 0) $loan->delete();
    }
}
