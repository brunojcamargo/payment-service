<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Response;
use Tests\TestCase;

class ApiTransactionTest extends TestCase
{
    public function test_api_create_transaction_valid_to_required(): void
    {
        $requestData = [
            'value' => '19.90',
            'from' => '1abdc'
        ];

        $response = $this->json('POST', '/api/deposit', $requestData);


        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'to' => ['O campo to é obrigatório.']
            ]
        ]);
    }

    public function test_api_create_transaction_valid_value_required(): void
    {
        $requestData = [
            'to' => '1abcd',
        ];

        $response = $this->json('POST', '/api/deposit', $requestData);


        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'value' => ["O valor mínimo para o campo value é 0.01."]
            ]
        ]);
    }
}