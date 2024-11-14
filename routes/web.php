<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WriteOffController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(VerifyCsrfToken::class)->group(function () {
    Route::any('/', [IndexController::class, 'index'])->name('index');
    Route::any('/setup', [SetupController::class, 'setup'])->name('setup');

    Route::name('entry.')->prefix('entry')->group(function () {
        Route::get('/list/{type?}', [EntryController::class, 'list'])->name('list');
        Route::get('/data', [EntryController::class, 'data'])->name('data');
        Route::get('/detail/{entry}', [EntryController::class, 'detail'])->name('detail');
        Route::post('/store', [EntryController::class, 'store'])->name('store');
        Route::patch('/update/{entry}', [EntryController::class, 'update'])->name('update');
        Route::patch('/transfer/{entry}', [EntryController::class, 'transfer'])->name('transfer');
        Route::delete('/delete', [EntryController::class, 'delete'])->name('delete');
        Route::patch('/passed', [EntryController::class, 'passed'])->name('passed');
        Route::patch('/ignore/{type}', [EntryController::class, 'ignore'])->name('ignore');
    });

    Route::name('write-off.')->prefix('write-off')->group(function () {
        Route::get('/list', [WriteOffController::class, 'list'])->name('list');
        Route::get('/data', [WriteOffController::class, 'data'])->name('data');
        Route::get('/detail/{writeoff}', [WriteOffController::class, 'detail'])->name('detail');
        Route::patch('/transfer/{writeoff}', [WriteOffController::class, 'transfer'])->name('transfer');
        Route::post('/store', [WriteOffController::class, 'store'])->name('store');
        Route::post('/upload', [WriteOffController::class, 'upload'])->name('upload');
        Route::patch('/update/{writeoff}', [WriteOffController::class, 'update'])->name('update');
        Route::patch('/passed', [WriteOffController::class, 'passed'])->name('passed');
        Route::patch('/canceled', [WriteOffController::class, 'canceled'])->name('canceled');
        Route::patch('/free', [WriteOffController::class, 'free'])->name('free');
        Route::delete('/delete', [WriteOffController::class, 'delete'])->name('delete');
        Route::patch('/comment/{writeoff}', [WriteOffController::class, 'comment'])->name('comment');

    });

    Route::name('supplier.')->prefix('supplier')->group(function () {
        Route::get('/list', [SupplierController::class, 'list'])->name('list');
    });

    Route::name('client.')->prefix('client')->group(function () {
        Route::get('/list', [ClientController::class, 'list'])->name('list');
    });

    Route::name('contract.')->prefix('contract')->group(function () {
        Route::get('/list', [ContractController::class, 'list'])->name('list');
        Route::get('/get-retailers', [ContractController::class, 'getRetailers'])->name('get.retailers');
        Route::get('/data', [ContractController::class, 'data'])->name('data');
        Route::get('/getNames', [ContractController::class, 'getNames'])->name('getNames');
        Route::post('/changeBalance', [ContractController::class, 'changeBalance'])->withoutMiddleware(VerifyCsrfToken::class)->name('changeBalance');
        Route::get('/history/{contract}', [ContractController::class, 'history'])->name('history');
    });


    Route::any('/settings', function () {
        return view('welcome');
    });
});
