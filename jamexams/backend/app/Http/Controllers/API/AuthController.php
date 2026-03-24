<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * AuthController — handles mobile app authentication.
 *
 * All responses follow the standard format:
 *   { "status": "success|error", "message": "...", "data": {} }
 */
class AuthController extends Controller
{
    /**
     * Register a new viewer account.
     * Account will be inactive until an admin activates it.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => $request->password, // hashed by model cast
            'role'     => 'viewer',
            'is_active'=> false,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Account created. Please wait for admin activation.',
            'data'    => [
                'user' => $this->userResource($user),
            ],
        ], 201);
    }

    /**
     * Login — returns Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid email or password.',
            ], 401);
        }

        // Check activation
        if (!$user->hasActiveAccess()) {
            $message = $user->is_active
                ? "Lipa 1,000 Voda 0756527718 January\nTuma Ujumbe Malipo WhatsApp"
                : 'Your account is not activated. Please contact admin.';

            return response()->json([
                'status'  => 'error',
                'message' => $message,
                'data'    => ['activation_required' => true],
            ], 403);
        }

        // Update FCM token if provided
        if ($request->fcm_token) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful.',
            'data'    => [
                'token'        => $token,
                'user'         => $this->userResource($user),
                'days_remaining' => $user->daysRemaining(),
            ],
        ]);
    }

    /**
     * Logout — revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get current user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        // Auto-deactivate if expired
        if ($user->is_active && $user->expires_at && $user->expires_at->isPast()) {
            $user->deactivate();

            return response()->json([
                'status'  => 'error',
                'message' => "Lipa 1,000 Voda 0756527718 January\nTuma Ujumbe Malipo WhatsApp",
                'data'    => ['activation_required' => true],
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message'=> 'Profile retrieved.',
            'data'   => [
                'user'           => $this->userResource($user),
                'days_remaining' => $user->daysRemaining(),
            ],
        ]);
    }

    /**
     * Update FCM token.
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json([
            'status'  => 'success',
            'message' => 'FCM token updated.',
        ]);
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function userResource(User $user): array
    {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'phone'       => $user->phone,
            'role'        => $user->role,
            'is_active'   => $user->is_active,
            'activated_at'=> $user->activated_at?->toISOString(),
            'expires_at'  => $user->expires_at?->toISOString(),
        ];
    }
}
