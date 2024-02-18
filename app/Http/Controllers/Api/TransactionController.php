<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Transaction\Requests\CancelTransactionRequest;
use App\Services\Transaction\Requests\GetTransactionsRequest;
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

    public function getTransactions(GetTransactionsRequest $request)
    {
        $response = $this->transactionService->getTransactions($request->from);

        return response()->json($response,$response->code);
    }

    public function cancelTransfer(CancelTransactionRequest $request)
    {
        $response = $this->transactionService->cancelTransaction($request->all());

        return response()->json($response,$response->code);
    }
}
