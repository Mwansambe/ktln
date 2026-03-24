<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token(): void
    {
        User::factory()->create([
            'email'      => 'student@test.com',
            'password'   => bcrypt('password123'),
            'is_active'  => true,
            'expires_at' => now()->addDays(30),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'student@test.com', 'password' => 'password123',
        ])->assertStatus(200)
          ->assertJsonStructure(['status', 'message', 'data' => ['token', 'user']]);
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        $this->postJson('/api/auth/login', [
            'email' => 'wrong@test.com', 'password' => 'wrong',
        ])->assertStatus(401);
    }

    public function test_expired_user_gets_access_expired_code(): void
    {
        User::factory()->create([
            'email'      => 'expired@test.com',
            'password'   => bcrypt('password123'),
            'is_active'  => true,
            'expires_at' => now()->subDay(),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'expired@test.com', 'password' => 'password123',
        ])->assertStatus(403)
          ->assertJsonFragment(['code' => 'ACCESS_EXPIRED']);
    }

    public function test_protected_route_requires_auth(): void
    {
        $this->getJson('/api/exams')->assertStatus(401);
    }
}
