<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Missing or invalid Authorization header.',
            ], 401);
        }

        $apiKey = substr($authHeader, 7); // Remove 'Bearer ' prefix
        
        $user = User::where('api_key', $apiKey)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API key.',
            ], 401);
        }

        // Attach the authenticated user to the request
        $request->merge(['api_user' => $user]);
        
        return $next($request);
    }
}
