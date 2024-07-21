<?php

use Illuminate\Support\Facades\Route;

Route::any('/', function () {
    return view('welcome');
});

Route::any('/setup', function () {
    return view('welcome');
});

Route::any('/settings', function () {
    return view('welcome');
});
