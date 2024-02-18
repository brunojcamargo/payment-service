<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Response;
use Tests\TestCase;

class ApiUserTest extends TestCase
{
    public function test_api_create_user_valid_fullName_required(): void
    {
        $requestData = [
            'document' => '12345678900',
            'email' => 'user@example.com',
            'password' => 'password',
            'type' => 'common'
        ];

        $response = $this->json('POST', '/api/user', $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'fullName' => ['O campo nome completo é obrigatório.'],
            ]
        ]);
    }

    public function test_api_create_user_valid_document_required(): void
    {
        $requestData = [
            'fullName' => 'Teste Teste',
            'email' => 'user@example.com',
            'password' => 'password',
            'type' => 'common'
        ];

        $response = $this->json('POST', '/api/user', $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'document' => ['O campo documento é obrigatório.'],
            ]
        ]);
    }

    public function test_api_create_user_valid_email_required(): void
    {
        $requestData = [
            'fullName' => 'Teste Teste',
            'document' => '12345678900',
            'password' => 'password',
            'type' => 'common'
        ];

        $response = $this->json('POST', '/api/user', $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'email' => ['O campo email é obrigatório.'],
            ]
        ]);
    }

    public function test_api_create_user_valid_password_required(): void
    {
        $requestData = [
            'fullName' => 'Teste Teste',
            'document' => '12345678900',
            'email' => 'user@example.com',
            'type' => 'common'
        ];

        $response = $this->json('POST', '/api/user', $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'password' => ['O campo senha é obrigatório.'],
            ]
        ]);
    }

    public function test_api_create_user_valid_password_format(): void
    {
        $requestData = [
            'fullName' => 'Teste Teste',
            'document' => '12345678900',
            'email' => 'user@example.com',
            'type' => 'common',
            'password' => '1'
        ];

        $response = $this->json('POST', '/api/user', $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'password' => [
                    "A senha deve ter no mínimo 6 caracteres.",
                    "A senha deve conter pelo menos uma letra maiúscula."
                ],
            ]
        ]);
    }

    public function test_api_create_user_valid_type_required(): void
    {
        $requestData = [
            'fullName' => 'User Name',
            'document' => '12345678900',
            'email' => 'user@example.com',
            'password' => 'password'
        ];

        $response = $this->json('POST', '/api/user', $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'type' => ['O campo tipo é obrigatório.']
            ]
        ]);
    }

    public function test_api_create_user_valid_type_in(): void
    {
        $requestData = [
            'fullName' => 'User Name',
            'document' => '12345678900',
            'email' => 'user@example.com',
            'password' => 'password',
            'type' => 'commodn'
        ];

        $response = $this->json('POST', '/api/user', $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'error' => true,
            'code' => 422,
            'data' => [
                'type' => ["O tipo deve ser shopkeeper ou common."]
            ]
        ]);
    }
}
