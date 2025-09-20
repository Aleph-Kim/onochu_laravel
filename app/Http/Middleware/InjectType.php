<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InjectType
{
    public function handle(Request $request, Closure $next, string $type)
    {
        // type을 request에 merge
        $request->merge([
            'type' => $type,
        ]);

        return $next($request);
    }
}
