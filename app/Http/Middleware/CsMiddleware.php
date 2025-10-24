<?php
// app/Http/Middleware/CsMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->peran === 'cs' || auth()->user()->peran === 'admin')) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Akses ditolak. Halaman untuk Customer Service.');
    }
}