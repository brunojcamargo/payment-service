<?php

use App\Services\External\NotificationService;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{

    public function test_notification_external_service()
    {
        $paymentValidationService = new NotificationService();

        $result = $paymentValidationService->sendNotification();

        $this->assertTrue(is_bool($result));
    }
}
