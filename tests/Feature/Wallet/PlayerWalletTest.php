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
use App\Models\Wallet;
use App\Types\WalletType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\UniqueConstraintViolationException;

class PlayerWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_players_start_with_personal_wallet(): void
    {
        // UserObserver should have created a personal Wallet entry for User
        $user = User::factory()->create();

        $this->assertEquals(1, $user->wallets()->count());
    }

    public function test_player_can_only_have_one_of_each_wallet_type(): void
    {
        $user = User::factory()->create();

        $personalWallet = new Wallet();
        $personalWallet->type = WalletType::Personal;
        $personalWallet->balance = 99;

        // Unique index on user_id && type
        $this->expectException(UniqueConstraintViolationException::class);
        $user->wallets()->save($personalWallet);
    }
}
