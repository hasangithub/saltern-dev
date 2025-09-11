<?php

namespace App\Http\Controllers;

use App\Models\StaffLoanRepayment;
use Illuminate\Http\Request;

class StaffLoanRepaymentController extends Controller
{
    public function index()
    {
        $repayments = StaffLoanRepayment::with(['staffLoan']) 
        ->orderBy('created_at', 'desc')
        ->get();
        return view('staff_loan_repayments.index', compact('repayments'));
    }
}
