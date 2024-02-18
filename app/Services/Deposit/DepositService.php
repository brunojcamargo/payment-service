<?php

namespace App\Services\Deposit;

use App\Models\Transaction;
use App\Services\Deposit\Requests\DepositRequest;
use App\Services\Deposit\Responses\DepositResponse;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DepositService
{
    protected DepositResponse $response;
    protected TransactionService $transactionService;
    protected Validator $validator;

    public function deposit(array $data): DepositResponse
    {
        $this->response = new DepositResponse;
        $this->transactionService = new TransactionService;

        if(!$this->validateDepositData($data)){
            return $this->response;
        }

        $transaction = $this->transactionService->createDepositTransaction($data['to'], $data['value']);

        if(!$this->transactionService->isValidTransaction($transaction))
        {
            $this->response->error = true;
            $this->response->code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $this->response->message = 'Erro ao iniciar a transação. Tente novamente em alguns minutos';
            return $this->response;
        }

        $this->response->code = Response::HTTP_CREATED;
        $this->response->message = 'Estamos processando seu depósito';
        $this->response->transactionId = $transaction->id;
        $this->response->transactionStatus = $transaction->status;

        $this->transactionService->dispatchJobValidPayment($transaction);

        return $this->response;
    }

    private function validateDepositData(array $data): bool
    {
        $request = new DepositRequest(app(Rule::class));
        $this->validator = app(Validator::class);
        $rules = $request->rules();
        $customMessages = $request->messages();

        $validator = $this->validator->make(
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
}
