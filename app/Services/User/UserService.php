<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\User\Responses\UserResponse;
use Illuminate\Http\Response;

class UserService
{
    protected UserRepositoryInterface $userRepository;
    protected UserResponse $response;

    public function create(array $data): UserResponse
    {
        $this->userRepository = app(UserRepositoryInterface::class);
        $this->response = new UserResponse;

        $newUser = $this->userRepository->createOrFail($data);
        if (!self::isValidUser($newUser)) {
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
        $this->userRepository = app(UserRepositoryInterface::class);
        $this->response = new UserResponse;

        $user = $this->userRepository->findOrFail($id);
        if (!self::isValidUser($user)) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->data->push($user);

        return $this->response;
    }

    public function findAll(): UserResponse
    {
        $this->userRepository = app(UserRepositoryInterface::class);
        $this->response = new UserResponse;

        $allUsers = $this->userRepository->getAll();

        if ($allUsers->isEmpty()) {
            $this->response->code = Response::HTTP_NO_CONTENT;
            return $this->response;
        }

        $this->response->data = $allUsers->values();

        return $this->response;
    }

    public function update(string $id, array $data): UserResponse
    {
        $this->userRepository = app(UserRepositoryInterface::class);
        $this->response = new UserResponse;

        $user = $this->userRepository->updateOrFail($id, $data);
        if (!self::isValidUser($user)) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->data->push($user);

        return $this->response;
    }

    public function delete(string $id): UserResponse
    {
        $this->userRepository = app(UserRepositoryInterface::class);
        $this->response = new UserResponse;

        if (!$this->userRepository->deleteOrFail($id)) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->code = Response::HTTP_NO_CONTENT;
        return $this->response;
    }

    public function userAllowTransfer(string $userId) : bool
    {
        $this->userRepository = app(UserRepositoryInterface::class);

        $user = $this->userRepository->findOrFail($userId);
        if (!self::isValidUser($user)) {
            return false;
        }

        return $user->type != 'shopkeeper';
    }

    public static function isValidUser(?User $user): bool
    {
        return $user instanceof User;
    }
}
