<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all users (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 0);
            $size = $request->query('size', 20);
            $sortBy = $request->query('sortBy', 'createdAt');
            $sortDirection = $request->query('sortDirection', 'desc');
            $role = $request->query('role');
            $active = $request->query('active');
            $search = $request->query('search');

            $query = User::query();

            // Apply filters
            if ($role) {
                $query->where('role', $role);
            }

            if ($active !== null) {
                $query->where('is_active', $active === 'true');
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%");
                });
            }

            // Apply sorting
            $sortColumn = match ($sortBy) {
                'name' => 'name',
                'email' => 'email',
                'role' => 'role',
                'lastLogin' => 'last_login',
                'createdAt' => 'created_at',
                default => 'created_at',
            };

            $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');

            $users = $query->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => $users->items(),
                'pageNumber' => $users->currentPage() - 1,
                'pageSize' => $users->perPage(),
                'totalElements' => $users->total(),
                'totalPages' => $users->lastPage(),
                'first' => $users->onFirstPage(),
                'last' => $users->onLastPage(),
                'empty' => $users->isEmpty(),
            ], 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve users: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get user by ID.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return $this->successResponse(
                $this->formatUser($user),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'User not found',
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Update user.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'avatar' => 'sometimes|string|nullable',
            ]);

            $user->update($validated);

            return $this->successResponse(
                $this->formatUser($user),
                'User updated successfully'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update user: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update user role.
     */
    public function updateRole(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'role' => 'required|string|in:VIEWER,EDITOR,ADMIN',
            ]);

            $user->update(['role' => $validated['role']]);

            return $this->successResponse(
                $this->formatUser($user),
                'User role updated successfully'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update role: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['is_active' => !$user->is_active]);

            return $this->successResponse(
                $this->formatUser($user),
                'User status updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to toggle user status: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete user.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return $this->noContentResponse();
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete user: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get user statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $usersByRole = [
                'VIEWER' => User::where('role', 'VIEWER')->count(),
                'EDITOR' => User::where('role', 'EDITOR')->count(),
                'ADMIN' => User::where('role', 'ADMIN')->count(),
            ];

            return $this->successResponse([
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'usersByRole' => $usersByRole,
                'totalAdmins' => $usersByRole['ADMIN'],
                'totalEditors' => $usersByRole['EDITOR'],
                'totalViewers' => $usersByRole['VIEWER'],
                'totalDownloads' => \App\Models\Download::count(),
                'totalBookmarks' => \App\Models\Bookmark::count(),
                'totalExamsCreated' => \App\Models\Exam::count(),
            ], 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve statistics: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
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

