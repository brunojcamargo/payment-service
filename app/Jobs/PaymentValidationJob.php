<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\External\PaymentValidationService;
use App\Services\Transaction\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaymentValidationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Transaction $transaction;
    protected TransactionService $transactionService;
    protected PaymentValidationService $paymentValidationService;
    public $tries = 3;
    public $retryAfter = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction, TransactionService $transactionService, PaymentValidationService $paymentValidationService)
    {
        $this->transaction = $transaction;
        $this->transactionService = $transactionService;
        $this->paymentValidationService = $paymentValidationService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = $this->paymentValidationService->validatePayment();
        if($response == 'Erro'){
            throw new \Exception('Erro de conexão');
        }

        $updateTransaction = $this->transactionService->validTransaction($response, $this->transaction);

        if(!$updateTransaction){
            throw new \Exception('Erro de atualização');
        }
    }

    public function retryUntil()
    {
        return now()->addMinutes(5);
    }
}
