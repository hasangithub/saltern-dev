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
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\SubAccountGroupController;
use App\Http\Controllers\SubLedgerController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\OwnerComplaintController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\StaffComplaintController;

use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\Auth\OwnerLoginController;

use App\Http\Controllers\StaffLoanRequestController;
use App\Http\Controllers\AccountImportController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StaffLoanController;

// routes/web.php
use App\Http\Controllers\PayrollBatchController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\StaffLoanRepaymentController;

// User login
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

// Owner login
Route::get('/owner/login', [OwnerLoginController::class, 'showLoginForm'])->name('owner.login');
Route::post('/owner/login', [OwnerLoginController::class, 'login']);
Route::post('/owner/logout', [OwnerLoginController::class, 'logout'])->name('owner.logout');

Route::middleware(['auth:web'])->group(function () {
    Route::resource('owners', OwnerController::class);
    Route::resource('memberships', MembershipController::class);
    Route::resource('buyers', BuyerController::class);
    Route::resource('yahai', YahaiController::class);
    Route::resource('saltern',SalternController::class);

    Route::get('/weighbridge-test/data', [WeighbridgeEntryController::class, 'data'])->name('weighbridge_entries.data');

    Route::get('/weighbridge/entries', [WeighbridgeEntryController::class, 'index'])->name('weighbridge_entries.index');
    Route::get('/weighbridge/entries/create', [WeighbridgeEntryController::class, 'create'])->name('weighbridge_entries.create');
    Route::post('/weighbridge/entries', [WeighbridgeEntryController::class, 'store'])->name('weighbridge_entries.store');
    Route::get('/weighbridge/entries/{entry_id}', [WeighbridgeEntryController::class, 'show'])->name('weighbridge_entries.show');
    Route::put('weighbridge/entries/{entry_id}/tare', [WeighbridgeEntryController::class, 'addTare'])->name('weighbridge_entries.tare');
    Route::delete('/weighbridge-entries/{id}/delete', [WeighbridgeEntryController::class, 'destroy'])->name('weighbridge-entries.delete');
    Route::get('/weighbridge-entries/{entry}/invoice', [WeighbridgeEntryController::class, 'invoice'])->name('weighbridge_entries.invoice');
    Route::get('/weighbridge/{id}/edit', [WeighbridgeEntryController::class, 'edit'])->name('weighbridge_entries.edit');
    Route::put('/weighbridge/{id}', [WeighbridgeEntryController::class, 'update'])->name('weighbridge_entries.update');
    
    Route::resource('other_incomes', OtherIncomeController::class);
    Route::resource('expenses', ExpenseController::class);


    Route::get('staff/complaints', [StaffComplaintController::class, 'index'])->name('staff.complaints.index');
    Route::get('staff/complaints/{complaint}', [StaffComplaintController::class, 'show'])->name('staff.complaints.show');
    Route::post('staff/complaints/{complaint}/assign', [StaffComplaintController::class, 'assign'])->name('staff.complaints.assign');
    Route::post('staff/complaints/{complaint}/reply', [StaffComplaintController::class, 'reply'])->name('staff.complaints.reply');


    Route::get('/attendance/import', [AttendanceController::class, 'importForm'])->name('attendance.import.form');
    Route::post('/attendance/import', [AttendanceController::class, 'import'])->name('attendance.import');



    Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial.balance');
    Route::get('/balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance.sheet');
    Route::get('/production-report', [ReportController::class, 'indexProduction'])->name('production.report.index');
    Route::get('/production-report/generate', [ReportController::class, 'generateProduction'])->name('production.report.generate');
    Route::get('/all-production-report/generate', [ReportController::class, 'generateAllProduction'])->name('all-production.report.generate');
    Route::get('/all-production-report/print', [ReportController::class, 'printAllProduction'])->name('all-production.report.print');
    Route::get('/production-report/buyerGenerate', [ReportController::class, 'generateBuyerProduction'])->name('production.report.buyerGenerate');
    Route::get('/reports/loan-trial-balance/detailed', [ReportController::class, 'yahaiWiseLoanTrialBalance'])->name('reports.loan-trial-balance.detailed');
    Route::get('/reports/owner-loans', [ReportController::class, 'indexOwnerLaon'])->name('reports.owner.loan.index');
    Route::get('/reports/staff-loans', [ReportController::class, 'indexStaffLaon'])->name('reports.staff.loan.index');
    Route::get('/reports/owner-loans/generate', [ReportController::class, 'ownerLoanReport'])->name('report.owner.loan.generate');
    Route::get('/reports/staff-loans/generate', [ReportController::class, 'staffLoanReport'])->name('report.staff.loan.generate');
    Route::get('/trial-balance-report', [ReportController::class, 'indexTrialBalance'])->name('trial.report.index');
    Route::get('/reports/pending-payments', [ReportController::class, 'indexPendingPayments'])->name('reports.pending.payments.index');
    Route::get('/reports/receipts-payments', [ReportController::class, 'indexReceipts'])->name('reports.receipts.index');
    Route::get('/reports/generate-receipts', [ReportController::class, 'receiptPaymentsReport'])->name('reports.receipts');
    Route::get('/reports/vouchers', [ReportController::class, 'indexVoucher'])->name('reports.voucher.index');
    Route::get('/reports/generate-pending-payments', [ReportController::class, 'pendingPaymentsReport'])->name('reports.pending-payments');
    Route::get('/reports/generate-voucher', [ReportController::class, 'voucherReport'])->name('reports.voucher');
    Route::get('/vouchers/report/print', [VoucherController::class, 'printVoucherReport'])->name('vouchers.report.print');
    Route::get('/ledger-report', [ReportController::class, 'indexLedger'])->name('ledger.report.index');
    Route::get('/ledger-report/generate', [ReportController::class, 'generateLedger'])->name('ledger.report.generate');
    Route::get('/ledger-report/export', [ReportController::class, 'exportLedgerReport'])->name('ledger.report.export');
    Route::post('/reports/ledger/pdf', [ReportController::class, 'generateLedgerPdf'])->name('reports.ledger.pdf');


    Route::resource('sub-account-groups',SubAccountGroupController::class);
    Route::resource('ledgers',LedgerController::class);
    Route::resource('sub-ledgers',SubLedgerController::class);
    Route::resource('accounts', AccountGroupController::class);
    Route::post('/account-tree/update', [AccountGroupController::class, 'update'])->name('account-tree.update');
    Route::resource('journal-entries', JournalEntryController::class);
    Route::resource('vouchers', VoucherController::class);
    Route::put('vouchers/{voucher_id}/approve', [VoucherController::class, 'approve'])->name('voucher.approve');
    Route::get('/journal-entries-all/all', [JournalEntryController::class, 'indexAll'])->name('journal.all.index');

        // Form display
    Route::get('/admin/owner-loans/create', [OwnerLoanController::class, 'adminCreateOwnerLoan'])->name('admin.owner_loans.create');
    Route::post('/admin/owner-loans', [OwnerLoanController::class, 'adminStoreOwnerLoan'])->name('admin.owner_loans.store');
    Route::get('/admin/owner-loans/{id}/print', [OwnerLoanController::class, 'printApprovalForm'])->name('admin.owner-loans.print');


    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/subledger-balance/{id}', [VoucherController::class, 'getSubledgerBalance']);


    Route::get('/reports/trial-balance/print', [ReportController::class, 'printTrialBalance'])->name('trial-balance.print');
    Route::get('/reports/balance-sheet/print', [ReportController::class, 'printBalanceSheet'])->name('balance-sheet.print');
    Route::get('/reports/loan-trial-balance/print', [ReportController::class, 'yahaiWiseLoanTrialBalancePrint'])->name('loan-trial-balance.print');
    Route::get('/reports/owner-loan/print', [ReportController::class, 'yahaiWiseLoanPrint'])->name('owner-loan.print');
    Route::get('/reports/staff-loan/print', [ReportController::class, 'staffLoanPrint'])->name('staff-loan.print');
    Route::get('/loan-repayment/{repayment}/print', [OwnerLoanRepaymentController::class, 'printReceipt'])->name('loan-repayment.print');
    Route::get('/vouchers/{voucher}/print', [VoucherController::class, 'printVoucher'])->name('vouchers.print');

    Route::get('/other-income/{income}/print', [OtherIncomeController::class, 'printOtherIncome'])->name('other-income.print');
    Route::get('/other-income/{income}/a4print', [OtherIncomeController::class, 'printOtherIncomeA4'])->name('other-income.a4print');
    Route::get('/receipt/{receipt}/print', [ReceiptController::class, 'printReceipt'])->name('receipt.print');
    Route::get('/sms/settings', [SmsController::class, 'showSettings'])->name('sms.settings');
    Route::post('/sms/settings', [SmsController::class, 'updateSettings'])->name('sms.settings.update');
    Route::post('/sms/test', [SmsController::class, 'testSms'])->name('sms.test');
    Route::post('/sms/send', [SmsController::class, 'sendSms'])->name('sms.send');

    Route::get('test/',function() {
        return view('journal_entries.test');
    });
    Route::get('test1/',function() {
        return view('journal_entries.test1');
    });

    Route::resource('employees', EmployeeController::class); 
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave/request', [LeaveController::class, 'requestLeave'])->name('leave.request');
    Route::get('/leave/approve/{id}', [LeaveController::class, 'approveLeave'])->name('leave.approve');
    Route::get('/leave/reject/{id}', [LeaveController::class, 'rejectLeave'])->name('leave.reject');
    Route::get('/leave/request', [LeaveController::class, 'createRequest'])->name('leave.create');

    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollBatchController::class, 'index'])->name('batches.index');
        Route::get('/batches/create', [PayrollBatchController::class, 'create'])->name('batches.create');
        Route::post('/batches', [PayrollBatchController::class, 'store'])->name('batches.store'); // validates unique period, redirects to build
        Route::get('/batches/{batch}/build', [PayrollBatchController::class, 'build'])->name('batches.build');
        Route::get('/batches/{batch}/edit', [PayrollBatchController::class, 'edit'])->name('batches.edit');
        Route::post('/batches/{batch}/save', [PayrollBatchController::class, 'save'])->name('batches.save');
        Route::post('/batches/{batch}/update', [PayrollBatchController::class, 'update'])->name('batches.update');
        Route::get('/{batch}/print', [PayrollBatchController::class, 'print'])
        ->name('batches.print');
        Route::get('/batches/{batch}/show', [PayrollBatchController::class, 'show'])->name('batches.show');
        Route::post('/batches/{batch}/approve', [PayrollBatchController::class, 'approve'])
    ->name('batches.approve');
    Route::get('/batches/{id}/payslips', [PayrollBatchController::class, 'printPayslips'])
    ->name('batches.payslips');
    });

    Route::resource('inventories', InventoryController::class);
    Route::resource('places', PlaceController::class);

});


Route::middleware(['auth:owner'])->group(function () {
    Route::resource('productions', ProductionController::class);  
    Route::get('/owner/complaints/create', [OwnerComplaintController::class, 'create'])->name('owner.complaints.create');
    // Store a new complaint
    Route::post('/owner/complaints', [OwnerComplaintController::class, 'store'])->name('owner.complaints.store');
    // List all complaints
    Route::get('/owner/complaints', [OwnerComplaintController::class, 'index'])->name('owner.complaints.index');  

    Route::get('my-loans', [OwnerLoanController::class, 'myLoans'])->name('owner.my-loans.index');
    Route::get('my-loans/{id}', [OwnerLoanController::class, 'showMyLoan'])->name('owner.my-loans.show');
   

});

Route::resource('owner-loans', OwnerLoanController::class);
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('owner-loan-repayments', OwnerLoanRepaymentController::class);
Route::resource('staff-loan-repayments', StaffLoanRepaymentController::class);
Route::get('loan-repayments/{loanId}/create', [OwnerLoanRepaymentController::class, 'createForLoan'])->name('loan-repayments.create-for-loan');
Route::post('loan-repayments', [OwnerLoanRepaymentController::class, 'storeForCash'])->name('loan-repayments.store');
Route::put('owner-loans/{loan_request}/approve', [OwnerLoanController::class, 'approve'])->name('owner-loan.approve');

Route::get('/api/subledgers/{ledgerId}', [SubledgerController::class, 'getSubledgers'])->name('api.subledgers');
Route::get('/api/subaccount-ledgers/{subAccountId}', [LedgerController::class, 'getBySubAccount'])->name('api.ledgers.bySubAccount');


Route::get('loan-requests', [OwnerLoanController::class, 'index'])->name('loan-requests.index');  // List owner's loan requests
Route::get('loan-requests/create', [OwnerLoanController::class, 'create'])->name('loan-requests.create');  // Request a new loan
Route::post('loan-requests', [OwnerLoanController::class, 'store'])->name('loan-requests.store');  // Submit loan request
Route::get('loan-requests/{loan_request}', [OwnerLoanController::class, 'show'])->name('loan-requests.show');  // View a specific loan request



Route::get('wizard/', [MembershipController::class, 'wizard'])->name('membership.wizard');
Route::get('/api/salterns/{yahaiId}', [SalternController::class, 'getByYahai'])->name('api.salterns');

Route::get('/get-sub-accounts', [SubLedgerController::class, 'getSubAccounts'])->name('get.sub_accounts');
Route::get('/get-ledgers', [SubLedgerController::class, 'getLedgers'])->name('get.ledgers');
Route::post('/sub-ledgers', [SubLedgerController::class, 'storeSubLedger'])->name('sub_ledgers.store');

Route::get('/get-yahais', [WeighbridgeEntryController::class, 'getYahais'])->name('get.yahai');

Route::get('api/salterns', [WeighbridgeEntryController::class, 'getSalterns'])->name('get.saltern');
Route::get('api/reports/salterns', [ReportController::class, 'getSalterns'])->name('get.reports.saltern');
Route::get('api/reports/get-ledgers', [ReportController::class, 'getLedgers'])->name('get.reports.ledgers');
Route::get('api/reports/get-sub-ledgers', [ReportController::class, 'getSubLedgers'])->name('get.reports.subledgers');
Route::get('api/membership/{saltern_id}', [WeighbridgeEntryController::class, 'getMembershipDetails'])->name('get.membership');
Route::get('get-saltern-details/{saltern_id}', [OwnerLoanController::class, 'getSalternDetails'])->name('get.saltern.details');
Route::get('get-loan-details/{saltern_id}', [OwnerLoanController::class, 'getLoanDetails'])->name('get.loan.details');

// Staff Loan Request Routes
Route::prefix('staff-loans')->group(function () {
    // Show all loan requests
    Route::get('/', [StaffLoanRequestController::class, 'index'])->name('staff-loans.index');

    // Show the form to create a new loan request
    Route::get('/create', [StaffLoanRequestController::class, 'create'])->name('staff-loans.create');

    // Store a new loan request
    Route::post('/', [StaffLoanRequestController::class, 'store'])->name('staff-loans.store');

    Route::get('my-loans/{id}', [StaffLoanRequestController::class, 'showMyLoan'])->name('staff.my-loans.show');
});



// Owner Loan Request Routes
Route::prefix('admin/staff-loans')->group(function () {
    // Show all loan requests
    Route::get('/', [StaffLoanController::class, 'index'])->name('admin.staff-loans.index');
    Route::get('/create', [StaffLoanController::class, 'adminCreateStaffLoan'])->name('admin.staff_loans.create');
    // Show the form to create a new loan request
    Route::get('get-loan-details/{user_id}', [StaffLoanController::class, 'getStaffLoanDetails'])->name('get.staff.loan.details');

    Route::get('/{loanId}', [StaffLoanController::class, 'show'])->name('admin.staff-loans.show');

    Route::put('/{loan_request}/approve', [StaffLoanController::class, 'approve'])->name('admin.staff-loan.approve');
   
    Route::post('/admin/owner-loans', [StaffLoanController::class, 'adminStoreStaffLoan'])->name('admin.staff_loans.store');
});


Route::get('/import-form', [AccountImportController::class, 'showForm'])->name('accounts.form');
Route::post('/import-chart', [AccountImportController::class, 'import'])->name('accounts.import');

Route::get('/password/request/{type}', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendLink'])->name('password.email');

Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');





