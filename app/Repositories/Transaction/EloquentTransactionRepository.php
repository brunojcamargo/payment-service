<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function findOrFail($id): ?Transaction
    {
        try {
            $transaction = Transaction::findOrFail($id);
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
        return Transaction::all();
    }

    public function createOrFail(array $data): ?Transaction
    {
        try {
            $transaction = Transaction::create($data);

            return $transaction;
        } catch (\Exception $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
            return null;
        }
    }

    public function updateOrFail($id, array $data): ?Transaction
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->update($data);
            return $transaction;
        } catch (ModelNotFoundException $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
        } catch (QueryException $e) {
            $this->log(true, __FUNCTION__, $e->getMessage());
        }
        return null;
    }

    public function deleteOrFail($id): bool
    {
        try {
            $transaction = Transaction::findOrFail($id);
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
            Log::error(get_class($this) . '::' . $method . ' Database error: ' . $message);
            return;
        }
        Log::error(get_class($this) . '::' . $method . ' Error: ' . $message);
        return;
    }
}
