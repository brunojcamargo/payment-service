<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\User\Requests\CreateUserRequest;
use App\Services\User\Requests\UpdateUserRequest;
use App\Services\User\UserService;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected WalletService $walletService
    ) {
    }

    public function store(CreateUserRequest $request)
    {
        $response = $this->userService->create($request->all());

        if($response->code == Response::HTTP_CREATED){
            $this->walletService->dispatchJobCreate($response->data->first());
        }

        return response()->json($response, $response->code);
    }


    public function get(string $userId)
    {
        $response = $this->userService->findById($userId);

        return response()->json($response, $response->code);
    }

    public function getAll()
    {
        $response =  $this->userService->findAll();

        return response()->json($response, $response->code);
    }

    public function update(UpdateUserRequest $request, string $userId)
    {
        $response = $this->userService->update($userId, $request->all());

        return response()->json($response, $response->code);
    }

    public function destroy(string $userId)
    {
        $response = $this->userService->delete($userId);

        return response()->json($response, $response->code);
    }
}
