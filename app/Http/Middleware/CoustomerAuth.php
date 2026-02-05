<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CoustomerAuth
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

        // Check if the token belongs to a coustomer
        $coustomer = $accessToken->tokenable;
        if (!$coustomer instanceof \App\Models\Coustomer) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Set the authenticated coustomer on the request
        $request->setUserResolver(function () use ($coustomer) {
            return $coustomer;
        });

        Auth::setUser($coustomer);

        return $next($request);
    }
}