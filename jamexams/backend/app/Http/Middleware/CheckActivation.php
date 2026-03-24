<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * CheckActivation Middleware
 * Protects API routes by verifying user activation and expiry.
 * Applied to all authenticated mobile API routes.
 */
class CheckActivation
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401);
        }

        // Check if account is active
        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account is not activated. Please contact administrator.',
                'code'    => 'ACCOUNT_NOT_ACTIVATED',
            ], 403);
        }

        // Check if access has expired
        if ($user->isExpired()) {
            // Auto-deactivate
            $user->update(['is_active' => false]);

            return response()->json([
                'status'  => 'error',
                'message' => "Lipa 1,000 Voda 0756527718 January\nTuma Ujumbe Malipo WhatsApp",
                'code'    => 'ACCESS_EXPIRED',
            ], 403);
        }

        return $next($request);
    }
}
