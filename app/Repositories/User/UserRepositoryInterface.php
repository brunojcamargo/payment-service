<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findOrFail($userId) : ?User ;

    public function getAll() : Collection;

    public function createOrFail(array $data) : ?User;

    public function updateOrFail($userId, array $data) : ?User;

    public function deleteOrFail($userId) : bool;
}
