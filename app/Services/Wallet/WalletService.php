<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Services\Wallet\Responses\WalletResponse;
use Illuminate\Http\Response;

class WalletService
{
    public function __construct(
        protected WalletRepositoryInterface $walletRepository,
        protected WalletResponse $response
    ) {
    }

    public function create(array $data): WalletResponse
    {

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
        if (!$this->walletRepository->deleteOrFail($id)) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->code = Response::HTTP_NO_CONTENT;
        return $this->response;
    }
}
