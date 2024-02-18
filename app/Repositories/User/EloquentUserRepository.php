<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class EloquentUserRepository implements UserRepositoryInterface
{
    protected $logService;
    protected User $model;

    public function __construct() {
        $this->logService = app(Log::class);
        $this->model = app(User::class);
    }

    public function findOrFail($userId): ?User
    {
        try {
            $user = $this->model->findOrFail($userId);
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
        return $this->model->all();
    }

    public function createOrFail(array $data): ?User
    {
        try {
            $data['password'] = bcrypt($data['password']);
            $user = $this->model->create($data);

            return $user;
        } catch (\Exception $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
            return null;
        }
    }

    public function updateOrFail($userId, array $data): ?User
    {
        try {
            $user = $this->model->findOrFail($userId);
            $user->update($data);
            return $user;
        } catch (ModelNotFoundException $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
        } catch (QueryException $e) {
            $this->log(true, __FUNCTION__, $e->getMessage());
        }
        return null;
    }

    public function deleteOrFail($userId): bool
    {
        try {
            $user = $this->model->findOrFail($userId);
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
            $this->logService->error(get_class($this) . '::' . $method . ' Database error: ' . $message);
            return;
        }
        $this->logService->error(get_class($this) . '::' . $method . ' Error: ' . $message);
        return;
    }
}
