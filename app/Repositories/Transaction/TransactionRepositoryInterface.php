<?php

namespace App\Repositories\Transaction;
;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function findOrFail($transactionId) : ?Transaction;

    public function getAll() : Collection;

    public function createOrFail(array $data) : ?Transaction;

    public function updateOrFail($transactionId, array $data) : ?Transaction;

    public function deleteOrFail($transactionId) : bool;

    public function updateInstance(Transaction $transaction, array $data) : ?Transaction;
}
