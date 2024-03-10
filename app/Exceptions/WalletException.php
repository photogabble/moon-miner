<?php

namespace App\Exceptions;

class WalletException extends \Exception {

    const SAME_WALLET = 1000;

    const INSUFFICIENT_FUNDS = 1001;

}
