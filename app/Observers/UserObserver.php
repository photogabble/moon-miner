<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;
use App\Types\WalletType;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $personalWallet = new Wallet();
        $personalWallet->type = WalletType::Personal;
        $personalWallet->balance = 0;

        $user->wallets()->save($personalWallet);
        $personalWallet->credit(config('game.start_credits'), 'Starting Balance');
    }
}
