<?php

use App\Services\External\PaymentValidationService;
use Tests\TestCase;

class PaymentValidationServiceTest extends TestCase
{

    public function test_payment_external_validation_service()
    {
        $paymentValidationService = new PaymentValidationService();

        $result = $paymentValidationService->validatePayment();

        $this->assertContains($result, ['Autorizado', 'Recusado']);
    }
}
