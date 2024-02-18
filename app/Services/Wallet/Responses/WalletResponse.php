<?php

namespace App\Services\Wallet\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class WalletResponse
{
    public bool $error;
    public int $code;
    public Collection $data;
    public string $message = "";

    public function __construct() {
        $this->error = false;
        $this->code = Response::HTTP_OK;
        $this->data = collect([]);
    }

}