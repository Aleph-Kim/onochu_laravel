<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('user')) {
            return redirect('/login')
                ->cookie('last_url', $request->getRequestUri(), 60);
        }

        return $next($request);
    }
}
