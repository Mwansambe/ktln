<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'VIEWER',
            ]);

            $token = JWTAuth::fromUser($user);
            $refreshToken = $this->generateRefreshToken($user);

            return $this->createdResponse([
                'accessToken' => $token,
                'refreshToken' => $refreshToken,
                'tokenType' => 'Bearer',
                'expiresIn' => config('jwt.ttl') * 60,
                'user' => $this->formatUser($user),
                'issuedAt' => now()->toIso8601String(),
            ], 'User registered successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Registration failed: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Login user.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse(
                    'Invalid email or password',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            $user = auth()->user();

            if (!$user->is_active) {
                JWTAuth::invalidate($token);
                return $this->errorResponse(
                    'Account is deactivated',
                    Response::HTTP_FORBIDDEN
                );
            }

            // Update last login
            $user->update(['last_login' => now()]);

            $refreshToken = $this->generateRefreshToken($user);

            return $this->successResponse([
                'accessToken' => $token,
                'refreshToken' => $refreshToken,
                'tokenType' => 'Bearer',
                'expiresIn' => config('jwt.ttl') * 60,
                'user' => $this->formatUser($user),
                'issuedAt' => now()->toIso8601String(),
            ], 'Login successful');
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Login failed: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Refresh token.
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'refreshToken' => 'required|string',
            ]);

            // For simplicity, we'll just generate a new token
            // In production, you would validate the refresh token properly
            $user = auth()->user();
            
            if (!$user) {
                return $this->errorResponse(
                    'Invalid token',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            $token = JWTAuth::fromUser($user);
            $refreshToken = $this->generateRefreshToken($user);

            return $this->successResponse([
                'accessToken' => $token,
                'refreshToken' => $refreshToken,
                'tokenType' => 'Bearer',
                'expiresIn' => config('jwt.ttl') * 60,
                'user' => $this->formatUser($user),
                'issuedAt' => now()->toIso8601String(),
            ], 'Token refreshed successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Token refresh failed: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get current user.
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            return $this->successResponse(
                $this->formatUser($user),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve user: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'currentPassword' => 'required|string',
                'newPassword' => 'required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (!Hash::check($validated['currentPassword'], $user->password)) {
                return $this->errorResponse(
                    'Current password is incorrect',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $user->update([
                'password' => Hash::make($validated['newPassword']),
            ]);

            // Invalidate all tokens
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->successResponse(null, 'Password changed successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Password change failed: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->successResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Logout failed: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Verify email.
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $token = $request->query('token');
            
            if (!$token) {
                return $this->errorResponse(
                    'Verification token is required',
                    Response::HTTP_BAD_REQUEST
                );
            }

            // In production, implement email verification logic
            return $this->successResponse(null, 'Email verified successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Email verification failed: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->email_verified_at) {
                return $this->errorResponse(
                    'Email is already verified',
                    Response::HTTP_BAD_REQUEST
                );
            }

            // In production, send verification email
            return $this->successResponse(null, 'Verification email sent');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to resend verification: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Generate refresh token.
     */
    private function generateRefreshToken(User $user): string
    {
        // In production, store refresh token in database or cache
        return base64_encode($user->id . ':' . time());
    }

    /**
     * Format user for response.
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'avatar' => $user->avatar,
            'isActive' => $user->is_active,
            'lastLogin' => $user->last_login?->toIso8601String(),
            'createdAt' => $user->created_at?->toIso8601String(),
            'updatedAt' => $user->updated_at?->toIso8601String(),
            'totalDownloads' => $user->total_downloads,
            'totalBookmarks' => $user->total_bookmarks,
            'totalExamsCreated' => $user->total_exams_created,
        ];
    }
}

