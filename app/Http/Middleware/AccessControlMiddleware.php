<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AccessControlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $accessType (dashboard|website)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $accessType)
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

            // Check access based on type
            if ($accessType === 'dashboard' && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Admin dashboard requires admin role.',
                    'redirect' => '/customer/dashboard'
                ], 403);
            }

            if ($accessType === 'website' && $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Customer area requires customer role.',
                    'redirect' => '/admin/dashboard'
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
