<?php

use Illuminate\Support\Facades\Route;

// Root SPA: semua route diarahkan ke React
Route::get('{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
