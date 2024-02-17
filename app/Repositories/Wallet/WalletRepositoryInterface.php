<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;

interface WalletRepositoryInterface
{
    public function findOrFail($id) : ?Wallet;

    public function getAll() : Collection;

    public function createOrFail(array $data) : ?Wallet;

    public function updateOrFail($id, array $data) : ?Wallet;

    public function deleteOrFail($id) : bool;
}
