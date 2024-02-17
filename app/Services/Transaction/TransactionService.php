<?php

namespace App\Services\Transaction;

use App\Jobs\PaymentValidationJob;
use App\Models\Transaction;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use App\Services\External\PaymentValidationService;
use App\Services\Transaction\Responses\TransactionResponse;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function __construct(
        protected TransactionResponse $response,
        protected TransactionRepositoryInterface $transactionRepository,
        protected WalletService $walletService
    ) {
    }

    public function createDepositTransaction(string $to, float $value) : ? Transaction
    {
        $transactionData = [
            'from' => $to,
            'to' => $to,
            'value' => $value,
            'type' => 'deposit',
            'status' => 'pending'
        ];

        $transaction = $this->transactionRepository->createOrFail($transactionData);
        if($transaction instanceof Transaction){
            return $transaction;
        }
        return null;
    }

    public function validTransaction(string $paymentValidResponse, Transaction $transaction) : bool
    {
        if($paymentValidResponse != 'Autorizado'){
            return $this->updateStatusTransaction('refused', $transaction);
        }

        if($this->updateStatusTransaction('accepted', $transaction)){
            if($this->walletService->updateBalance($transaction)){
                return true;
            }
        }

        return $this->updateStatusTransaction('canceled', $transaction);
    }

    private function updateStatusTransaction(string $newStatus, Transaction $transaction) : bool
    {
        $transactionData = [
            'status' => $newStatus
        ];

        $response = $this->transactionRepository->updateOrFail($transaction->id, $transactionData);

        if($response instanceof Transaction){
            return true;
        }

        return false;
    }

    public function dispatchJobValidPayment(Transaction $transaction)
    {
        dispatch(new PaymentValidationJob($transaction, app(TransactionService::class), app(PaymentValidationService::class)));
    }
}
