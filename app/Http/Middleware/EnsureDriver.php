<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureDriver
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'Driver access only'
            ], 403);
        }

        return $next($request);
    }
}
