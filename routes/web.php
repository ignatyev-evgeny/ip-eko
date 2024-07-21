<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setup', function () {
    return view('welcome');
});

Route::get('/settings', function () {
    return view('welcome');
});
