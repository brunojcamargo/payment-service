<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Transaction\Requests\TransactionRequest;
use App\Services\Transaction\TransactionService;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    public function transfer(TransactionRequest $request)
    {
        $response = $this->transactionService->newTransaction($request->all());

        return response()->json($response,$response->code);
    }
}
