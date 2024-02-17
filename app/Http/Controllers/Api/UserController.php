<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\User\Requests\CreateUserRequest;
use App\Services\User\Requests\UpdateUserRequest;
use App\Services\User\UserService;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function store(CreateUserRequest $request)
    {
        $response = $this->userService->create($request->all());

        return response()->json($response, $response->code);
    }


    public function get(string $id)
    {
        $response = $this->userService->findById($id);

        return response()->json($response, $response->code);
    }

    public function getAll()
    {
        $response =  $this->userService->findAll();

        return response()->json($response, $response->code);
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $response = $this->userService->update($id, $request->all());

        return response()->json($response, $response->code);
    }

    public function destroy(string $id)
    {
        $response = $this->userService->delete($id);

        return response()->json($response, $response->code);
    }
}
