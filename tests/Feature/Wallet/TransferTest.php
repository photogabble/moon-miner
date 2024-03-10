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

use App\Models\Wallet;
use App\Types\WalletType;
use App\Exceptions\WalletException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_to_same_wallet_throws_exception(): void
    {
        $user = User::factory()->create();

        $this->expectException(WalletException::class);
        $this->expectExceptionCode(WalletException::SAME_WALLET);

        $user->wallet()->debit( 100, 'Test Transfer', $user->wallet());
    }

    public function test_transfer_with_insufficient_funds_throws_exception(): void
    {
        $jane = User::factory()->create();
        $john = User::factory()->create();

        $this->expectException(WalletException::class);
        $this->expectExceptionCode(WalletException::INSUFFICIENT_FUNDS);

        $jane->wallet()->debit( 1000000, 'Test Transfer', $john->wallet());
    }

    public function test_transfer_between_two_players(): void
    {
        $jane = User::factory()->create();
        $john = User::factory()->create();

        // Should only have two transactions from initial balance. Upon transfer there should be four.
        $this->assertDatabaseCount('wallet_transactions', 2);

        $janeStartingBalance = $jane->wallet()->balance;
        $johnStartingBalance = $john->wallet()->balance;

        $jane->wallet()->debit( 100, 'Test Transfer', $john->wallet());
        $this->assertDatabaseCount('wallet_transactions', 4);

        $this->assertEquals($janeStartingBalance - 100, $jane->wallet()->balance);
        $this->assertEquals($johnStartingBalance + 100, $john->wallet()->balance);
    }

    public function test_transfer_between_player_and_loan(): void
    {
        $user = User::factory()->create();

        // Loans have a negative balance, once it reaches zero its cleared.
        $loan = new Wallet();
        $loan->type = WalletType::Loan;

        $startingBalance = $user->wallet()->balance;

        $user->wallets()->save($loan);
        $user->wallet(WalletType::Loan)->debit(100000, 'Loan taken', $user->wallet());

        $this->assertEquals($startingBalance + 100000, $user->wallet()->balance);
        $this->assertEquals(-100000, $user->wallet(WalletType::Loan)->balance);
    }

}
