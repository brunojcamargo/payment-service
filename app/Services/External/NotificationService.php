<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;

class NotificationService
{
    const URL = "https://run.mocky.io/v3";

    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = app(Http::class);
    }

    public function sendNotification(): bool
    {
        $endpoint = "/54dc2cf1-3add-45b5-b5a9-6bf7e7f1f4a6";

        $response = $this->httpClient->get(self::URL.$endpoint);

        if (!$response->successful()) {
            return 'Erro';
        }

        return ($response->json('message') === true) ? true : false;
    }
}
