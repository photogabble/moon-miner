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

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_ledger_keeps_balance(): void
    {
        $jane = User::factory()->create();
        $john = User::factory()->create();

        $janeStartingBalance = $jane->wallet()->balance;
        $johnStartingBalance = $john->wallet()->balance;

        $this->assertEquals(1, $jane->wallet()->transactions()->count());
        $this->assertEquals(1, $john->wallet()->transactions()->count());
        $this->assertDatabaseCount('wallet_transactions', 2);

        $jane->wallet()->debit(100, null, $john->wallet());

        $this->assertEquals($janeStartingBalance - 100, $jane->wallet()->balance);
        $this->assertEquals($johnStartingBalance + 100, $john->wallet()->balance);
        $this->assertEquals(2, $jane->wallet()->transactions()->count());
        $this->assertEquals(2, $john->wallet()->transactions()->count());
        $this->assertDatabaseCount('wallet_transactions', 4);

        $john->wallet()->debit(100, null, $jane->wallet());

        $this->assertEquals($janeStartingBalance, $jane->wallet()->balance);
        $this->assertEquals($johnStartingBalance, $john->wallet()->balance);
        $this->assertEquals(3, $jane->wallet()->transactions()->count());
        $this->assertEquals(3, $john->wallet()->transactions()->count());
        $this->assertDatabaseCount('wallet_transactions', 6);
    }

}
