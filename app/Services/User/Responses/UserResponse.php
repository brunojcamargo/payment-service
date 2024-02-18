<?php

namespace App\Services\User\Responses;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class UserResponse
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