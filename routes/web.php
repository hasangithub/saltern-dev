<?php

use App\Http\Controllers\AccountGroupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\WeighbridgeEntryController;
use App\Http\Controllers\YahaiController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\SalternController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\SubAccountGroupController;
use App\Http\Controllers\SubLedgerController;


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


Route::get('/test', function () {
    return view('test');
});

Route::resource('owners', OwnerController::class);
Route::resource('buyers', BuyerController::class);
Route::resource('memberships', MembershipController::class);

Route::get('/weighbridge/entries', [WeighbridgeEntryController::class, 'index'])->name('weighbridge_entries.index');
Route::get('/weighbridge/entries/create', [WeighbridgeEntryController::class, 'create'])->name('weighbridge_entries.create');
Route::post('/weighbridge/entries', [WeighbridgeEntryController::class, 'store'])->name('weighbridge_entries.store');

Route::resource('yahai', YahaiController::class);
Route::resource('saltern',SalternController::class);
Route::resource('sub-account-groups',SubAccountGroupController::class);
Route::resource('ledgers',LedgerController::class);
Route::resource('sub-ledgers',SubLedgerController::class);
Route::resource('accounts',AccountGroupController::class);