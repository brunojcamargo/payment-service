<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiStatusTest extends TestCase
{
    public function test_endpoint_status_return_ok(): void
    {
        $response = $this->get('/api');

        $response->assertStatus(200);
    }
}
