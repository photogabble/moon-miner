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

namespace Tests\Feature\Wallet;

use App\Types\WalletType;
use App\Actions\Banking\Loan;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrowing_creates_loan_wallet(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->wallet(WalletType::Loan));

        $initialBalance = $user->wallet()->balance;

        $action = new Loan($user);
        $action->borrow(1000);

        $this->assertNotNull($user->wallet(WalletType::Loan));

        $loanAmount = 1000 + $action->getBorrowingFee(1000);

        $this->assertEquals($initialBalance + $loanAmount, $user->wallet()->balance);
        $this->assertEquals($loanAmount * -1, $user->wallet(WalletType::Loan)->balance);

        $this->assertFalse($action->isOverdue());
    }

    public function test_repayment_reduces_loan_amount(): void
    {
        $user = User::factory()->create();

        $initialBalance = $user->wallet()->balance;

        $action = new Loan($user);
        $action->borrow(1000);
        $action->repay(500);

        $loanAmount = 1000 + $action->getBorrowingFee(1000);

        $this->assertEquals($initialBalance + $loanAmount - 500, $user->wallet()->balance);
        $this->assertEquals(($loanAmount - 500) * -1 , $user->wallet(WalletType::Loan)->balance);
    }

    public function test_repayment_in_full_deletes_loan_wallet(): void
    {
        $user = User::factory()->create();

        $action = new Loan($user);
        $action->borrow(1000);

        $this->assertNotNull($user->wallet(WalletType::Loan));

        $action->repay(1000 + $action->getBorrowingFee(1000));

        $this->assertNull($user->wallet(WalletType::Loan));
    }

    public function test_loans_can_go_overdue(): void
    {
        $user = User::factory()->create();

        $action = new Loan($user);
        $action->borrow(1000);

        $this->assertFalse($action->isOverdue());

        Carbon::setTestNow(Carbon::now()->addYear());

        $this->assertTrue($action->isOverdue());

        Carbon::setTestNow();
    }
}
