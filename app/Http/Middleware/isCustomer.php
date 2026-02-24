<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() && Auth::user()->role === 'customer') {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
