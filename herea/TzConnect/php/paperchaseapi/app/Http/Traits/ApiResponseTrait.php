<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait ApiResponseTrait
{
    /**
     * Return success response.
     */
    public function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
            'statusCode' => $statusCode,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Return error response.
     */
    public function errorResponse(
        string $message = 'An error occurred',
        int $statusCode = 400,
        ?string $errorCode = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'timestamp' => now()->toIso8601String(),
            'errorCode' => $errorCode,
            'statusCode' => $statusCode,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Return created response.
     */
    public function createdResponse(
        mixed $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Return no content response.
     */
    public function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Return paginated response.
     */
    public function paginatedResponse(
        $paginator,
        mixed $data = null,
        string $message = 'Success'
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
            'statusCode' => 200,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ];

        return response()->json($response, 200);
    }

    /**
     * Get current user from request.
     */
    protected function getCurrentUser(Request $request): ?\App\Models\User
    {
        return $request->user();
    }

    /**
     * Check if user is authenticated.
     */
    protected function isAuthenticated(Request $request): bool
    {
        return $request->user() !== null;
    }

    /**
     * Check if user has specific role.
     */
    protected function hasRole(Request $request, string $role): bool
    {
        $user = $this->getCurrentUser($request);
        return $user && $user->role === $role;
    }

    /**
     * Check if user is admin.
     */
    protected function isAdmin(Request $request): bool
    {
        return $this->hasRole($request, 'ADMIN');
    }

    /**
     * Check if user can manage content (editor or admin).
     */
    protected function canManageContent(Request $request): bool
    {
        $user = $this->getCurrentUser($request);
        return $user && in_array($user->role, ['EDITOR', 'ADMIN']);
    }
}

