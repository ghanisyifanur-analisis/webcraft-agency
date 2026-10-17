<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;
    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'login' => 'admin@sekolah.sch.id',
            'password' => 'password123',
            'device_name' => 'Automated Test',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email', 'roles'],
                ],
            ]);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'login' => 'admin@sekolah.sch.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'login' => 'admin@sekolah.sch.id',
            'password' => 'password123',
        ]);

        $token = $loginRes->json('data.token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'admin@sekolah.sch.id');
    }
}
