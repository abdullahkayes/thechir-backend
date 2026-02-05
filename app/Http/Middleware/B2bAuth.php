<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class B2bAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request has an Authorization header with Bearer token
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Find the token in the personal_access_tokens table
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Invalid token.'], 401);
        }

        // Check if the token belongs to a B2B user
        $b2b = $accessToken->tokenable;
        if (!$b2b instanceof \App\Models\B2b) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Check if B2B user is approved
        if ($b2b->status !== 'approved') {
            return response()->json(['message' => 'Account not approved.'], 403);
        }

        // Set the authenticated B2B user on the request
        $request->setUserResolver(function () use ($b2b) {
            return $b2b;
        });

        Auth::setUser($b2b);

        return $next($request);
    }
}
