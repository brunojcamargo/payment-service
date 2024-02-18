<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;

class PaymentValidationService
{
    const URL = "https://run.mocky.io/v3";

    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = app(Http::class);
    }

    public function validatePayment(): string
    {
        $endpoint = "/5794d450-d2e2-4412-8131-73d0293ac1cc";

        $response = $this->httpClient->get(self::URL.$endpoint);

        if (!$response->successful()) {
            return 'Erro';
        }

        return ($response->json('message') === 'Autorizado') ? 'Autorizado' : 'Recusado';
    }
}
