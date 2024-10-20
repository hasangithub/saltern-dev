<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\WeighbridgeEntryController;
use App\Http\Controllers\YahaiController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::resource('owners', OwnerController::class);
Route::resource('buyers', BuyerController::class);
Route::get('/yahai', [YahaiController::class, 'index'])->name('yahai.index');

Route::get('/weighbridge/entries', [WeighbridgeEntryController::class, 'index'])->name('weighbridge_entries.index');
Route::get('/weighbridge/entries/create', [WeighbridgeEntryController::class, 'create'])->name('weighbridge_entries.create');
Route::post('/weighbridge/entries', [WeighbridgeEntryController::class, 'store'])->name('weighbridge_entries.store');