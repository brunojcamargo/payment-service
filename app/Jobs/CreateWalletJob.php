<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateWalletJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;
    protected WalletService $walletService;
    public $tries = 3;
    public $retryAfter = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, WalletService $walletService)
    {
        $this->user = $user;
        $this->walletService = $walletService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->walletService->create(['user_id' => $this->user->id]);
    }
}
