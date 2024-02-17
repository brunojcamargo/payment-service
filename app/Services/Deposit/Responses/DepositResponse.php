<?php

namespace App\Services\Deposit\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class DepositResponse
{
    public bool $error;
    public int $code;
    public Collection $data;

    public function __construct() {
        $this->error = false;
        $this->code = Response::HTTP_OK;
        $this->data = collect([]);
    }

}