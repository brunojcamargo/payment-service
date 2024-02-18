<?php

namespace App\Services\Wallet;

use App\Jobs\CreateWalletJob;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Services\Transaction\TransactionService;
use App\Services\User\Responses\UserResponse;
use App\Services\User\UserService;
use App\Services\Wallet\Responses\WalletResponse;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Response;

class WalletService
{
    protected WalletRepositoryInterface $walletRepository;
    protected WalletResponse $response;
    protected UserService $userService;
    protected TransactionService $transactionService;
    protected DatabaseManager $database;

    public function create(array $data): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository =  app(WalletRepositoryInterface::class);

        $newWallet = $this->walletRepository->createOrFail($data);
        if (!$this->isValidWallet($newWallet)) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_BAD_REQUEST;
            return $this->response;
        }

        $this->response->code = Response::HTTP_CREATED;
        $this->response->data->push($newWallet);

        return $this->response;
    }

    public function findById(string $walletId): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository = app(WalletRepositoryInterface::class);

        $wallet = $this->walletRepository->findOrFail($walletId);
        if (!$this->isValidWallet($wallet)) {
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

    public function update(string $walletId, array $data): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository = app(WalletRepositoryInterface::class);

        $wallet = $this->walletRepository->updateOrFail($walletId, $data);
        if (!$this->isValidWallet($wallet)) {
            $this->response->error = true;
            $this->response->code = Response::HTTP_NOT_FOUND;
            return $this->response;
        }

        $this->response->data->push($wallet);

        return $this->response;
    }

    public function delete(string $walletId): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->walletRepository = app(WalletRepositoryInterface::class);

        if (!$this->walletRepository->deleteOrFail($walletId)) {
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

    private function addAmountInBalance(Wallet $wallet, float $ammount): bool
    {
        $this->walletRepository = app(WalletRepositoryInterface::class);

        $newValue = $wallet->balance + $ammount;
        $updateData = [
            'balance' => $newValue
        ];

        $update = $this->walletRepository->updateOrFail($wallet->id, $updateData);
        if ($this->isValidWallet($update)) {
            return true;
        }

        return false;
    }

    private function removeAmountInBalance(Wallet $wallet, float $ammount): bool
    {
        $this->walletRepository = app(WalletRepositoryInterface::class);

        $newValue = $wallet->balance - $ammount;
        $updateData = [
            'balance' => $newValue
        ];

        $update = $this->walletRepository->updateOrFail($wallet->id, $updateData);
        if ($this->isValidWallet($update)) {
            return true;
        }

        return false;
    }

    public function updateBalance(Transaction $transaction): bool
    {
        $this->userService = new UserService;

        if ($transaction->type == 'deposit') {
            return $this->updateBalanceForDeposit($transaction);
        } elseif ($transaction->type == 'transfer') {
            return $this->updateBalanceForTransfer($transaction);
        }

        return false;
    }

    private function updateBalanceForDeposit(Transaction $transaction): bool
    {
        $userResponse = $this->userService->findById($transaction->to);
        if (!$this->isValidUserResponse($userResponse)) {
            return false;
        }

        $wallet = $userResponse->data->first()->wallet()->first();
        if (!$this->isValidWallet($wallet)) {
            return false;
        }

        return $this->addAmountInBalance($wallet, $transaction->value);
    }

    private function updateBalanceForTransfer(Transaction $transaction): bool
    {
        $userResponseTo = $this->userService->findById($transaction->to);
        $userResponseFrom = $this->userService->findById($transaction->from);

        if (!$this->isValidUserResponse($userResponseTo) || !$this->isValidUserResponse($userResponseFrom)) {
            return false;
        }

        $walletTo = $userResponseTo->data->first()->wallet()->first();
        $walletFrom = $userResponseFrom->data->first()->wallet()->first();

        if (!$this->isValidWallet($walletTo) || !$this->isValidWallet($walletFrom)) {
            return false;
        }

        $this->database = app(DatabaseManager::class);

        $this->database->beginTransaction();
        if ($this->addAmountInBalance($walletTo, $transaction->value)) {
            if (!$this->removeAmountInBalance($walletFrom, $transaction->value)) {
                $this->database->rollBack();
                return false;
            }
            $this->database->commit();
            $this->userService->dispatchJobNotification();
            return true;
        }
        $this->database->rollBack();
        return false;
    }

    public function updateBalanceForCancelTransfer(Transaction $transaction): WalletResponse
    {
        $this->response = new WalletResponse;
        $this->userService = new UserService;

        $userResponseTo = $this->userService->findById($transaction->to);
        $userResponseFrom = $this->userService->findById($transaction->from);

        if (!$this->isValidUserResponse($userResponseTo) || !$this->isValidUserResponse($userResponseFrom)) {
            $this->response->error = true;
            $this->response->message = 'Usuários da operação inválidos';
            return $this->response;
        }

        $walletTo = $userResponseTo->data->first()->wallet()->first();
        $walletFrom = $userResponseFrom->data->first()->wallet()->first();

        if (!$this->isValidWallet($walletTo) || !$this->isValidWallet($walletFrom)) {
            $this->response->error = true;
            $this->response->message = 'Carteiras da operação inválidas';
            return $this->response;
        }

        if (!$this->hasAmountAvailable($transaction->to, $transaction->value)) {
            $this->response->error = true;
            $this->response->message = 'Usuário de destino não possui saldo para o estorno.';
            return $this->response;
        }

        $this->database = app(DatabaseManager::class);

        $this->database->beginTransaction();
        if ($this->addAmountInBalance($walletFrom, $transaction->value)) {
            if (!$this->removeAmountInBalance($walletTo, $transaction->value)) {
                $this->database->rollBack();
                $this->response->error = true;
                $this->response->message = 'Erro interno ao realizar a operação. tente novamente em alguns minutos.';
                return $this->response;
            }
            $this->database->commit();
            $this->response->message = 'Estorno realisado com sucesso.';
            return $this->response;
        }
        $this->database->rollBack();
        $this->response->error = true;
        $this->response->message = 'Erro interno ao realizar a operação. tente novamente em alguns minutos.';
        return $this->response;
    }

    private function isValidUserResponse(?UserResponse $userResponse): bool
    {
        return $userResponse instanceof UserResponse && !$userResponse->error;
    }

    private function isValidWallet(?Wallet $wallet): bool
    {
        return $wallet instanceof Wallet;
    }

    public function hasAmountAvailable(string $userId, float $amount): bool
    {
        $this->userService = new UserService;

        $userResponse = $this->userService->findById($userId);
        if ($this->isValidUserResponse($userResponse)) {
            $wallet = $userResponse->data->first()->wallet()->first();
            if ($this->isValidWallet($wallet)) {
                return $this->hasAmountInBalance($wallet, $amount, $userResponse->data->first());
            }
        }

        return false;
    }

    private function hasAmountInBalance(Wallet $wallet, float $amount, User $user): bool
    {
        $balance = $wallet->balance ?? 0;
        $pendingValue = $user->transactionsFrom()
            ->where('status', 'pending')
            ->where('type', 'transfer')
            ->sum('value');

        $availableBalance = $balance - $pendingValue;
        $isBalanceSufficient = $availableBalance >= $amount;
        return $isBalanceSufficient;
    }
}
