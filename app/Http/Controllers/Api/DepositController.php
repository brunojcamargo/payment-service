<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Deposit\DepositService;
use App\Services\Deposit\Requests\DepositRequest;

class DepositController extends Controller
{
    public function __construct(
        protected DepositService $depositService
    ) {}

    public function new(DepositRequest $request)
    {
        $response = $this->depositService->deposit($request->all());

        return response()->json($response,$response->code);
    }
}
