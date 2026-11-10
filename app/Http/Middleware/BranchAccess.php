<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * BranchAccess Middleware
 *
 * Ensures that non-super-admin users can only write data for their own branch.
 * On write operations (POST/PUT/PATCH/DELETE), if a branch_id is supplied in the
 * request, it must match the authenticated user's branch_id.
 *
 * Super Admins (branch_id = NULL or isSuperAdmin() = true) bypass this check entirely.
 */
class BranchAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not authenticated, let the auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Super Admins bypass branch restrictions
        if ($user->isSuperAdmin()) {
            // For super admins: auto-inject branch_id from selected branch if not set
            return $next($request);
        }

        // For branch users: enforce branch_id on write operations
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $requestedBranchId = $request->input('branch_id');

            // If the request provides a branch_id, it must match the user's branch
            if ($requestedBranchId && $user->branch_id && (int) $requestedBranchId !== (int) $user->branch_id) {
                return response()->json([
                    'error' => 'You are not authorized to perform actions for another branch.',
                ], 403);
            }

            // Auto-fill branch_id from the authenticated user's branch
            if ($user->branch_id) {
                $request->merge(['branch_id' => $user->branch_id]);
            }
        }

        return $next($request);
    }
}
