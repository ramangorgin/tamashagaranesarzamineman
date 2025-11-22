<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeDigits
{
    public function handle(Request $request, Closure $next)
    {
        // Convert all request string values to English digits
        // (prefix \ to call the global helper)
        $normalized = \normalize_digits_recursive($request->all(), 'en');
        $request->merge($normalized);

        return $next($request);
    }
}