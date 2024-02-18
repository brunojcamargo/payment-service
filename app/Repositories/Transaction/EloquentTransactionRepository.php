<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Log\Logger;

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    protected $logService;
    protected Transaction $model;

    public function __construct() {
        $this->logService = app(Logger::class);
        $this->model = app(Transaction::class);
    }

    public function findOrFail($transactionId): ?Transaction
    {
        try {
            $transaction = $this->model->findOrFail($transactionId);
            return $transaction;
        } catch (ModelNotFoundException $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
        } catch (QueryException $e) {
            $this->log(true, __FUNCTION__, $e->getMessage());
        }
        return null;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function createOrFail(array $data): ?Transaction
    {
        try {
            $transaction = $this->model->create($data);

            return $transaction;
        } catch (\Exception $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
            return null;
        }
    }

    public function updateOrFail($transactionId, array $data): ?Transaction
    {
        try {
            $transaction = $this->model->findOrFail($transactionId);
            $transaction->update($data);
            return $transaction;
        } catch (ModelNotFoundException $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
        } catch (QueryException $e) {
            $this->log(true, __FUNCTION__, $e->getMessage());
        }
        return null;
    }

    public function updateInstance(Transaction $transaction, array $data): ?Transaction
    {
        try {
            $transaction->update($data);
            return $transaction;
        } catch (QueryException $e) {
            $this->log(true, __FUNCTION__, $e->getMessage());
        }
    }

    public function deleteOrFail($transactionId): bool
    {
        try {
            $transaction = $this->model->findOrFail($transactionId);
            $transaction->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
        } catch (QueryException $e) {
            $this->log(true, __FUNCTION__, $e->getMessage());
        }

        return false;
    }

    private function log(bool $databaseError, string $method, string $message): void
    {
        if ($databaseError) {
            $this->logService->error(get_class($this) . '::' . $method . ' Database error: ' . $message);
            return;
        }
        $this->logService->error(get_class($this) . '::' . $method . ' Error: ' . $message);
        return;
    }
}
