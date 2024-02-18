<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Response;
use Tests\TestCase;

class ApiDepositTest extends TestCase
{
    public function test_api_create_deposit_valid_to_required(): void
    {
        $requestData = [
            'value' => '19.90',
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

    public function test_api_create_deposit_valid_value_required(): void
    {
        $requestData = [
            'to' => '1abc1d',
        ];

        $response = $this->json('POST', '/api/deposit', $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'value' => ['O valor mínimo para o campo value é 0.01.']
            ]
        ]);


    }

}
