<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            // Get the authenticated user
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if user has any of the required roles
            if (!in_array($user->role, $roles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions. Required roles: ' . implode(', ', $roles)
                ], 403);
            }

            return $next($request);
            
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalid or expired'
            ], 401);
        }
    }
}
