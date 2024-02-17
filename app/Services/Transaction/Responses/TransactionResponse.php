<?php

namespace App\Services\Transaction\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class TransactionResponse
{
    public bool $error;
    public int $code;
    public Collection $data;
    public string $transactionId = '';
    public string $transactionStatus = '';

    public function __construct() {
        $this->error = false;
        $this->code = Response::HTTP_OK;
        $this->data = collect([]);
    }

}