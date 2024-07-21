<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(VerifyCsrfToken::class)->group(function () {
    Route::any('/', function () {
        return view('welcome');
    });

    Route::any('/setup', function () {
        return view('welcome');
    });

    Route::any('/settings', function () {
        return view('welcome');
    });
});
