<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findOrFail($id) : ?User ;

    public function getAll() : Collection;

    public function createOrFail(array $data) : ?User;

    public function updateOrFail($id, array $data) : ?User;

    public function deleteOrFail($id) : bool;
}
