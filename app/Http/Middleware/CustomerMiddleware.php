<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->peran === 'customer') {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Akses hanya untuk customer!');
    }
}
