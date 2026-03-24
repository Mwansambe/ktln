<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    public function test_activate_user_sets_is_active_true(): void
    {
        $user  = User::factory()->create(['is_active' => false]);
        $admin = User::factory()->create();

        $this->userService->activateUser($user, 30, $admin->id);

        $user->refresh();
        $this->assertTrue($user->is_active);
        $this->assertNotNull($user->expires_at);
    }

    public function test_deactivate_expired_users_count(): void
    {
        User::factory()->create(['is_active' => true, 'expires_at' => now()->subDay()]);
        User::factory()->create(['is_active' => true, 'expires_at' => now()->addDays(15)]);

        $count = $this->userService->deactivateExpiredUsers();

        $this->assertEquals(1, $count);
    }
}
