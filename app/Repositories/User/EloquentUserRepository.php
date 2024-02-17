<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findOrFail($id): ?User
    {
        try {
            $user = User::findOrFail($id);
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
        return User::all();
    }

    public function createOrFail(array $data): ?User
    {
        try {
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);

            return $user;
        } catch (\Exception $e) {
            $this->log(false, __FUNCTION__, $e->getMessage());
            return null;
        }
    }

    public function updateOrFail($id, array $data): ?User
    {
        try {
            $user = User::findOrFail($id);
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
            $user = User::findOrFail($id);
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
