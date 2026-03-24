<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthController
 * Handles student authentication via the mobile API.
 * Uses Laravel Sanctum for token-based auth.
 */
class AuthController extends Controller
{
    /**
     * Login - Authenticate user and return token.
     *
     * POST /api/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
            'fcm_token' => 'nullable|string', // Firebase token for push notifications
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid email or password.', 401);
        }

        // Check if user is activated
        if (!$user->is_active) {
            return $this->errorResponse(
                'Your account is not activated. Please contact administrator.',
                403,
                ['code' => 'ACCOUNT_NOT_ACTIVATED']
            );
        }

        // Check if access has expired
        if ($user->isExpired()) {
            // Auto-deactivate expired user
            $user->update(['is_active' => false]);

            return $this->errorResponse(
                "Lipa 1,000 Voda 0756527718 January\nTuma Ujumbe Malipo WhatsApp",
                403,
                ['code' => 'ACCESS_EXPIRED']
            );
        }

        // Update FCM token if provided
        if ($request->fcm_token) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        // Revoke old tokens and create new one
        $user->tokens()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return $this->successResponse('Login successful.', [
            'token' => $token,
            'user' => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'phone'          => $user->phone,
                'avatar'         => $user->avatar,
                'is_active'      => $user->is_active,
                'activated_at'   => $user->activated_at?->toISOString(),
                'expires_at'     => $user->expires_at?->toISOString(),
                'days_remaining' => $user->daysRemaining(),
                'roles'          => $user->getRoleNames(),
            ],
        ]);
    }

    /**
     * Logout - Revoke current token.
     *
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse('Logged out successfully.');
    }

    /**
     * Get authenticated user profile.
     *
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        // Re-check access on every profile request
        if ($user->isExpired()) {
            $user->update(['is_active' => false]);
            return $this->errorResponse(
                "Lipa 1,000 Voda 0756527718 January\nTuma Ujumbe Malipo WhatsApp",
                403,
                ['code' => 'ACCESS_EXPIRED']
            );
        }

        return $this->successResponse('Profile retrieved.', [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'avatar'         => $user->avatar,
            'is_active'      => $user->is_active,
            'activated_at'   => $user->activated_at?->toISOString(),
            'expires_at'     => $user->expires_at?->toISOString(),
            'days_remaining' => $user->daysRemaining(),
            'roles'          => $user->getRoleNames(),
        ]);
    }

    /**
     * Update FCM token.
     *
     * PUT /api/auth/fcm-token
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => 'required|string']);

        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return $this->successResponse('FCM token updated.');
    }

    // ==================== HELPERS ====================

    protected function successResponse(string $message, array $data = []): JsonResponse
    {
        $response = ['status' => 'success', 'message' => $message];
        if (!empty($data)) $response['data'] = $data;
        return response()->json($response, 200);
    }

    protected function errorResponse(string $message, int $code = 400, array $extra = []): JsonResponse
    {
        return response()->json(array_merge(['status' => 'error', 'message' => $message], $extra), $code);
    }
}
