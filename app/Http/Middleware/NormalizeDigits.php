<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeDigits
{
    protected array $skipKeys = ['password','password_confirmation','_token'];

    public function handle(Request $request, Closure $next)
    {
        $data = $request->all();
        array_walk_recursive($data, function (&$val, $key) {
            if (is_string($val) && !in_array($key, $this->skipKeys, true)) {
                $val = normalize_digits($val);
            }
        });
        $request->replace($data);
        return $next($request);
    }
}