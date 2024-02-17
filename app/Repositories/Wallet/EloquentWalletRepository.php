<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class EloquentWalletRepository implements WalletRepositoryInterface
{
    public function findOrFail($id): ?Wallet
    {
        try {
            $wallet = Wallet::findOrFail($id);
            return $wallet;
        } catch (ModelNotFoundException $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
        } catch (QueryException $e) {
            $this->log(true, __FUNCTION__, $e->getMessage());
        }
        return null;
    }

    public function getAll(): Collection
    {
        return Wallet::all();
    }

    public function createOrFail(array $data): ?Wallet
    {
        try {
            $wallet = Wallet::create($data);

            return $wallet;
        } catch (\Exception $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
            return null;
        }
    }

    public function updateOrFail($id, array $data): ?Wallet
    {
        try {
            $wallet = Wallet::findOrFail($id);
            $wallet->update($data);
            return $wallet;
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
            $wallet = Wallet::findOrFail($id);
            $wallet->delete();
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
