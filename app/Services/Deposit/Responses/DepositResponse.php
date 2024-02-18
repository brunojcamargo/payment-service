<?php

namespace App\Services\Deposit\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class DepositResponse
{
    public bool $error;
    public int $code;
    public string $message = '';
    public string $transactionId = '';
    public string $transactionStatus = '';

    public function __construct() {
        $this->error = false;
        $this->code = Response::HTTP_OK;
    }

}