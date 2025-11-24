<?php

use Illuminate\Support\Facades\Route;

// Root SPA: semua route diarahkan ke React
Route::get('/{any}', function () {
    return view('app'); // ini adalah wrapper React (resources/views/app.blade.php)
})->where('any', '.*');
