<?php

namespace App\Services\Transaction;

use App\Jobs\PaymentValidationJob;
use App\Models\Transaction;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use App\Services\External\PaymentValidationService;
use App\Services\Transaction\Requests\TransactionRequest;
use App\Services\Transaction\Responses\TransactionResponse;
use App\Services\User\UserService;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransactionService
{
    protected TransactionResponse $response;
    protected TransactionRepositoryInterface $transactionRepo;
    protected WalletService $walletService;
    protected UserService $userService;

    public function createDepositTransaction(string $toUserId, float $value): ?Transaction
    {
        $this->transactionRepo = app(TransactionRepositoryInterface::class);

        $transactionData = [
            'from' => $toUserId,
            'to' => $toUserId,
            'value' => $value,
            'type' => 'deposit',
            'status' => 'pending'
        ];

        $transaction = $this->transactionRepo->createOrFail($transactionData);
        if ($this->isValidTransaction($transaction)) {
            return $transaction;
        }
        return null;
    }

    public function validTransaction(string $paymentValidResponse, Transaction $transaction): bool
    {
        $this->walletService = new WalletService;

        if ($paymentValidResponse != 'Autorizado') {
            return $this->updateStatusTransaction('refused', $transaction);
        }

        if ($this->updateStatusTransaction('accepted', $transaction)) {
            if ($this->walletService->updateBalance($transaction)) {
                return true;
            }
        }

        return $this->updateStatusTransaction('canceled', $transaction);
    }

    private function updateStatusTransaction(string $newStatus, Transaction $transaction): bool
    {
        $this->transactionRepo = app(TransactionRepositoryInterface::class);

        $transactionData = [
            'status' => $newStatus
        ];

        $response = $this->transactionRepo->updateOrFail($transaction->id, $transactionData);

        if ($this->isValidTransaction($response)) {
            return true;
        }

        return false;
    }

    public function dispatchJobValidPayment(Transaction $transaction)
    {
        dispatch(new PaymentValidationJob($transaction, app(TransactionService::class), app(PaymentValidationService::class)));
    }

    public function newTransaction(array $data) : TransactionResponse
    {
        $this->response = new TransactionResponse;

        if(!$this->validateTransactionData($data)){
            return $this->response;
        }

        $this->userService = new UserService;

        if(!$this->userService->userAllowTransfer($data['from'])){
            $this->response->error = true;
            $this->response->code = Response::HTTP_UNAUTHORIZED;
            $this->response->message = 'Logistas não podem realizar transferências.';
            return $this->response;
        }

        $this->walletService = new WalletService;

        if(!$this->walletService->hasAmountAvailable($data['from'], $data['value'])){
            $this->response->error = true;
            $this->response->code = Response::HTTP_UNAUTHORIZED;
            $this->response->message = 'O usuário não possui saldo suficiente.';
            return $this->response;
        }

        $newTransaction = $this->createTransferTransaction($data['to'], $data['from'], $data['value']);

        if(!$this->isValidTransaction($newTransaction))
        {
            $this->response->error = true;
            $this->response->code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $this->response->message = 'Erro ao iniciar a transação. Tente novamente em alguns minutos';
            return $this->response;
        }

        $this->response->code = Response::HTTP_CREATED;
        $this->response->message = 'Estamos processando sua transferência.';
        $this->response->transactionId = $newTransaction->id;
        $this->response->transactionStatus = $newTransaction->status;

        $this->dispatchJobValidPayment($newTransaction);

        return $this->response;
    }

    private function validateTransactionData($data) : bool
    {
        $request = new TransactionRequest(app(Rule::class));
        $rules = $request->rules();
        $customMessages = $request->messages();

        $validator = Validator::make(
            $data,
            $rules,
            $customMessages
        );

        if ($validator->fails()) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_BAD_REQUEST;
            $this->response->message = 'Um ou mais campos possuem valores inválidos.';
            return false;
        }

        return true;
    }

    public function createTransferTransaction(string $toUserId, string $fromUserId, float $value): ?Transaction
    {
        $this->transactionRepo = app(TransactionRepositoryInterface::class);

        $transactionData = [
            'from' => $fromUserId,
            'to' => $toUserId,
            'value' => $value,
            'type' => 'transfer',
            'status' => 'pending'
        ];

        $transaction = $this->transactionRepo->createOrFail($transactionData);
        if ($this->isValidTransaction($transaction)) {
            return $transaction;
        }
        return null;
    }

    public function isValidTransaction(?Transaction $transaction) : bool
    {
        return $transaction instanceof Transaction;
    }
}
