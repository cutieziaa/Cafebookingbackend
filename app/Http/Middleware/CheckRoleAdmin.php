<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika user terautentikasi dan role-nya adalah 'admin'
        if (auth()->check() && auth()->user()->peran === 'admin') {
            return $next($request);
        }
        
        // Redirect dengan pesan error jika bukan admin
        return redirect('/dashboard')->with('error', 'Akses ditolak! Hanya Administrator yang bisa mengakses fitur ini.');
    }
}