<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\User\Responses\UserResponse;
use Illuminate\Http\Response;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserResponse $response
    ) {
    }

    public function create(array $data): UserResponse
    {
        //@todo reaproveitar/restaurar usuarios excluidos

        $newUser = $this->userRepository->createOrFail($data);
        if (!$newUser instanceof User) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_BAD_REQUEST;
            return $this->response;
        }

        $this->response->code = Response::HTTP_CREATED;
        $this->response->data->push($newUser);

        return $this->response;
    }

    public function findById(string $id): UserResponse
    {
        $user = $this->userRepository->findOrFail($id);
        if (!$user instanceof User) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->data->push($user);

        return $this->response;
    }

    public function findAll(): UserResponse
    {
        $allUsers = $this->userRepository->getAll();

        if ($allUsers->isEmpty()) {
            $this->response->code = Response::HTTP_NO_CONTENT;
            return $this->response;
        }

        $this->response->data->push($allUsers);

        return $this->response;
    }

    public function update(string $id, array $data): UserResponse
    {
        //@todo validar se o type realmente pode ser ultilizado

        $user = $this->userRepository->updateOrFail($id, $data);
        if (!$user instanceof User) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->data->push($user);

        return $this->response;
    }

    public function delete(string $id): UserResponse
    {
        if (!$this->userRepository->deleteOrFail($id)) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->code = Response::HTTP_NO_CONTENT;
        return $this->response;
    }
}
