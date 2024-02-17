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
            $user = Wallet::findOrFail($id);
            return $user;
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
            $user = Wallet::create($data);

            return $user;
        } catch (\Exception $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
            return null;
        }
    }

    public function updateOrFail($id, array $data): ?Wallet
    {
        try {
            $user = Wallet::findOrFail($id);
            $user->update($data);
            return $user;
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
            $user = Wallet::findOrFail($id);
            $user->delete();
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
