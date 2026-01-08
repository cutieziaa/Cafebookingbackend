<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // --- TAMBAHKAN BAGIAN INI ---
        // Daftarkan middleware CORS manual ke grup 'api'
        $middleware->group('api', [
            \App\Http\Middleware\HandleCors::class, 
        ]);
        // --- AKHIR BAGIAN YANG DITAMBAHKAN ---

        $middleware->alias([
            'admin' => App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();