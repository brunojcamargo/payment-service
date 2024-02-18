<?php

namespace App\Repositories\Wallet;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Log\Logger;

class EloquentWalletRepository implements WalletRepositoryInterface
{
    protected $logService;
    protected Wallet $model;

    public function __construct() {
        $this->logService = app(Logger::class);
        $this->model = app(Wallet::class);
    }

    public function findOrFail($walletId): ?Wallet
    {
        try {
            $wallet = $this->model->findOrFail($walletId);
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
        return $this->model->all();
    }

    public function createOrFail(array $data): ?Wallet
    {
        try {
            $wallet = $this->model->create($data);

            return $wallet;
        } catch (\Exception $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
            return null;
        }
    }

    public function updateOrFail($walletId, array $data): ?Wallet
    {
        try {
            $wallet = $this->model->findOrFail($walletId);
            $wallet->update($data);
            return $wallet;
        } catch (ModelNotFoundException $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
        } catch (QueryException $e) {
            $this->log(true, __FUNCTION__, $e->getMessage());
        }
        return null;
    }

    public function deleteOrFail($walletId): bool
    {
        try {
            $wallet = $this->model->findOrFail($walletId);
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
            $this->logService->error(get_class($this) . '::' . $method . ' Database error: ' . $message);
            return;
        }
        $this->logService->error(get_class($this) . '::' . $method . ' Error: ' . $message);
        return;
    }
}
