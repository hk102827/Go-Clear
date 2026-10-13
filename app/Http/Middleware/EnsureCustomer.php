<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Customer access only'
            ], 403);
        }

        return $next($request);
    }
}
