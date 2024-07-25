<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SupplierController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(VerifyCsrfToken::class)->group(function () {
    Route::any('/', [IndexController::class, 'index'])->name('index');
    Route::any('/setup', [SetupController::class, 'setup'])->name('setup');

    Route::name('entry.')->prefix('entry')->group(function () {
        Route::get('/list', [EntryController::class, 'list'])->name('list');
        Route::post('/store', [EntryController::class, 'store'])->name('store');
    });

    Route::name('supplier.')->prefix('supplier')->group(function () {
        Route::get('/list', [SupplierController::class, 'list'])->name('list');
    });

    Route::name('client.')->prefix('client')->group(function () {
        Route::get('/list', [ClientController::class, 'list'])->name('list');
    });


    Route::any('/settings', function () {
        return view('welcome');
    });
});
