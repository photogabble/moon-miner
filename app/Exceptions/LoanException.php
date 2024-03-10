<?php

namespace App\Exceptions;

class LoanException extends \Exception {

    const EXISTING_LOAN = 1000;

    const LOAN_LIMIT = 1001;

    const LOAN_NOT_FOUND = 1002;

}
