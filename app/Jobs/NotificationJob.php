<?php

namespace App\Jobs;

use App\Services\External\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected NotificationService $notificationService;
    public $tries = 3;
    public $retryAfter = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(!$this->notificationService->sendNotification()){
            throw new \Exception('Erro ao notificar');
        }
    }

    public function retryUntil()
    {
        return now()->addMinutes(5);
    }
}
