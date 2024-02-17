<?php

namespace App\Services\Wallet;

use App\Jobs\CreateWalletJob;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Services\User\Responses\UserResponse;
use App\Services\User\UserService;
use App\Services\Wallet\Responses\WalletResponse;
use Illuminate\Http\Response;

class WalletService
{
    protected WalletRepositoryInterface $walletRepository;
    protected WalletResponse $response;
    protected UserService $userService;

    public function create(array $data): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository =  app(WalletRepositoryInterface::class);

        $newWallet = $this->walletRepository->createOrFail($data);
        if (!$newWallet instanceof Wallet) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_BAD_REQUEST;
            return $this->response;
        }

        $this->response->code = Response::HTTP_CREATED;
        $this->response->data->push($newWallet);

        return $this->response;
    }

    public function findById(string $id): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository = app(WalletRepositoryInterface::class);

        $wallet = $this->walletRepository->findOrFail($id);
        if (!$wallet instanceof Wallet) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->data->push($wallet);

        return $this->response;
    }

    public function findAll(): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository = app(WalletRepositoryInterface::class);

        $allWallets = $this->walletRepository->getAll();

        if ($allWallets->isEmpty()) {
            $this->response->code = Response::HTTP_NO_CONTENT;
            return $this->response;
        }

        $this->response->data->push($allWallets);

        return $this->response;
    }

    public function update(string $id, array $data): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository = app(WalletRepositoryInterface::class);

        $wallet = $this->walletRepository->updateOrFail($id, $data);
        if (!$wallet instanceof Wallet) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->data->push($wallet);

        return $this->response;
    }

    public function delete(string $id): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository = app(WalletRepositoryInterface::class);

        if (!$this->walletRepository->deleteOrFail($id)) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->code = Response::HTTP_NO_CONTENT;
        return $this->response;
    }

    public function dispatchJobCreate(User $user)
    {
        dispatch(new CreateWalletJob($user, app(WalletService::class)));
    }

    private function addAmountInBalance(Wallet $wallet, float $ammount) : bool
    {
        $this->walletRepository = app(WalletRepositoryInterface::class);

        $newValue = $wallet->balance + $ammount;
        $updateData = [
            'balance' => $newValue
        ];

        $update = $this->walletRepository->updateOrFail($wallet->id, $updateData);
        if($update instanceof Wallet){
            return true;
        }

        return false;
    }

    public function updateBalance(Transaction $transaction) : bool
    {
        $this->userService = new UserService;

        if($transaction->type == 'deposit')
        {
            $userResponse = $this->userService->findById($transaction->to);
            if($userResponse instanceof UserResponse){
                if($userResponse->error){
                    return false;
                }
                $wallet = $userResponse->data->first()->wallet()->first();
                if($wallet instanceof Wallet){
                    return $this->addAmountInBalance($wallet, $transaction->value);
                }
            }

        }

        return false;
    }
}
