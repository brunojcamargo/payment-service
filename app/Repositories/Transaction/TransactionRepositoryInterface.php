<?php

namespace App\Repositories\Transaction;
;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function findOrFail($id) : ?Transaction;

    public function getAll() : Collection;

    public function createOrFail(array $data) : ?Transaction;

    public function updateOrFail($id, array $data) : ?Transaction;

    public function deleteOrFail($id) : bool;
}
