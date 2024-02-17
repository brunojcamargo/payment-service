<?php

namespace App\Providers;

use App\Repositories\User\EloquentUserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Wallet\EloquentWalletRepository;
use App\Repositories\Wallet\WalletRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, EloquentWalletRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
