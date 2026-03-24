<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivationRecord;
use Illuminate\Support\Facades\Hash;

/**
 * UserService
 * Handles user creation, activation, deactivation.
 */
class UserService
{
    /**
     * Create a new user with role.
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
        ]);

        $user->assignRole($data['role']);

        return $user;
    }

    /**
     * Activate a user for a given number of days.
     * Creates an activation record and updates user status.
     */
    public function activateUser(User $user, int $days, string $activatedBy, ?string $notes = null): ActivationRecord
    {
        $now = now();
        $expiresAt = $now->copy()->addDays($days);

        // Mark previous records as not current
        ActivationRecord::where('user_id', $user->id)
                         ->update(['is_current' => false]);

        // Create new activation record
        $record = ActivationRecord::create([
            'user_id'       => $user->id,
            'activated_by'  => $activatedBy,
            'activated_at'  => $now,
            'expires_at'    => $expiresAt,
            'duration_days' => $days,
            'notes'         => $notes,
            'is_current'    => true,
        ]);

        // Update user status
        $user->update([
            'is_active'    => true,
            'activated_at' => $now,
            'expires_at'   => $expiresAt,
        ]);

        return $record;
    }

    /**
     * Deactivate a user immediately.
     */
    public function deactivateUser(User $user): void
    {
        $user->update(['is_active' => false]);
    }

    /**
     * Auto-deactivate users whose access has expired.
     * Called by Laravel Scheduler every day.
     */
    public function deactivateExpiredUsers(): int
    {
        $expired = User::where('is_active', true)
                       ->where('expires_at', '<', now())
                       ->get();

        foreach ($expired as $user) {
            $user->update(['is_active' => false]);
        }

        return $expired->count();
    }

    /**
     * Get user management statistics.
     */
    public function getStats(): array
    {
        return [
            'total'    => User::count(),
            'active'   => User::active()->count(),
            'inactive' => User::where('is_active', false)->count(),
            'expired'  => User::where('is_active', true)->expired()->count(),
            'admins'   => User::role('admin')->count(),
            'editors'  => User::role('editor')->count(),
            'viewers'  => User::role('viewer')->count(),
        ];
    }
}
