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
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\OwnerLoanController;
use App\Http\Controllers\OwnerLoanRepaymentController;
use App\Http\Controllers\SubAccountGroupController;
use App\Http\Controllers\SubLedgerController;
use App\Http\Controllers\VoucherController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('/test', function () {
    return view('test');
});

Route::resource('owners', OwnerController::class);
Route::resource('buyers', BuyerController::class);
Route::resource('memberships', MembershipController::class);

Route::get('/weighbridge/entries', [WeighbridgeEntryController::class, 'index'])->name('weighbridge_entries.index');
Route::get('/weighbridge/entries/create', [WeighbridgeEntryController::class, 'create'])->name('weighbridge_entries.create');
Route::post('/weighbridge/entries', [WeighbridgeEntryController::class, 'store'])->name('weighbridge_entries.store');
Route::get('/weighbridge/entries/{entry_id}', [WeighbridgeEntryController::class, 'show'])->name('weighbridge_entries.show');
Route::put('weighbridge/entries/{entry_id}/tare', [WeighbridgeEntryController::class, 'addTare'])->name('weighbridge_entries.tare');

Route::resource('yahai', YahaiController::class);
Route::resource('saltern',SalternController::class);
Route::resource('sub-account-groups',SubAccountGroupController::class);
Route::resource('ledgers',LedgerController::class);
Route::resource('sub-ledgers',SubLedgerController::class);
Route::resource('accounts', AccountGroupController::class);
Route::resource('journal-entries', JournalEntryController::class);
Route::resource('vouchers', VoucherController::class);
Route::put('vouchers/{voucher_id}/approve', [VoucherController::class, 'approve'])->name('voucher.approve');
Route::resource('owner-loans', OwnerLoanController::class);

Route::resource('owner-loan-repayments', OwnerLoanRepaymentController::class);
Route::get('loan-repayments/{loanId}/create', [OwnerLoanRepaymentController::class, 'createForLoan'])->name('loan-repayments.create-for-loan');
Route::post('loan-repayments', [OwnerLoanRepaymentController::class, 'storeForCash'])->name('loan-repayments.store');
Route::put('owner-loans/{loan_request}/approve', [OwnerLoanController::class, 'approve'])->name('owner-loan.approve');


Route::get('test/',function() {
    return view('journal_entries.test');
});
Route::get('test1/',function() {
    return view('journal_entries.test1');
});

Route::get('/api/subledgers/{ledgerId}', [SubledgerController::class, 'getSubledgers'])->name('api.subledgers');

Route::get('loan-requests', [OwnerLoanController::class, 'index'])->name('loan-requests.index');  // List owner's loan requests
Route::get('loan-requests/create', [OwnerLoanController::class, 'create'])->name('loan-requests.create');  // Request a new loan
Route::post('loan-requests', [OwnerLoanController::class, 'store'])->name('loan-requests.store');  // Submit loan request
Route::get('loan-requests/{loan_request}', [OwnerLoanController::class, 'show'])->name('loan-requests.show');  // View a specific loan request
Route::resource('other_incomes', OtherIncomeController::class);
Route::resource('expenses', ExpenseController::class);
