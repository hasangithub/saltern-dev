<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\AccountGroup;
use App\Models\Buyer;
use App\Models\JournalDetail;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Membership;
use App\Models\OwnerLoan;
use App\Models\Saltern;
use App\Models\SubLedger;
use App\Models\WeighbridgeEntry;
use App\Models\Yahai;
use App\Models\Owner;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OwnerLoanRepayment;
use App\Models\OtherIncome;
use App\Models\Voucher;
use Carbon\Carbon;
use App\Models\ReceiptDetail;
use App\Exports\LedgerReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{

    public function indexTrialBalance(Request $request)
    {
        $yahaies = Yahai::all();
        $owners = Membership::all();
        $buyers = Buyer::all();

        return view('reports.trial_balance_index', compact('yahaies', 'owners', 'buyers'));
    }

    public function indexPendingPayments(Request $request)
    {
        return view('reports.pending.index');
    }

    public function indexReceipts(Request $request)
    {
        return view('reports.receipt.index');
    }

    public function receiptPaymentsReport(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate  = $request->to_date;


        $receiptDetails = ReceiptDetail::with(['receipt'])
            ->whereHas('receipt', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('receipt_date', [$fromDate, $toDate]);
            })->get();
        $totalAmount = $receiptDetails->sum('amount');
        return view('reports.receipt.receipts-details', compact('receiptDetails', 'fromDate', 'toDate', 'totalAmount'));
    }

    public function indexVoucher(Request $request)
    {
        return view('reports.voucher.index');
    }

    public function trialBalance(Request $request)
    {
        [$trialData, $totalDebit, $totalCredit] = $this->getTrialBalanceData($request);
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        return view('reports.trial_balance', compact('trialData', 'totalDebit', 'totalCredit', 'fromDate', 'toDate'));
    }

    public function balanceSheet(Request $request)
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $from = $request->from_date;
        $to = $request->to_date;

        $balanceSheetData = $this->getBalanceSheetData($from, $to);

        return view('reports.balance_sheet', $balanceSheetData);
    }

    private function getBalanceSheetData(?string $from = null, ?string $to = null)
    {
        $accountGroups = AccountGroup::with([
            'subAccountGroups.ledgers' => function ($q) use ($from, $to) {
                $q->with(['journalDetails' => function ($query) use ($from, $to) {
                    $query->when($from && $to, function ($q2) use ($from, $to) {
                        $q2->whereHas('journalEntry', function ($q3) use ($from, $to) {
                            $q3->whereBetween('journal_date', [$from, $to]);
                        });
                    })->when(!$from && !$to, function ($q2) {
                        // No date filter, get all
                        $q2->whereHas('journalEntry');
                    });
                }]);
            }
        ])->get()->groupBy('name');

        $data = [
            'Assets' => [],
            'Equity' => [],
            'CurrentLiabilities' => [],
        ];

        $assetsTotal = 0;
        $equityTotal = 0;
        $liabilitiesTotal = 0;

        // ASSETS
        if ($accountGroups->has('Assets')) {
            foreach ($accountGroups['Assets'] as $group) {
                $groupTotal = 0;
                $subGroups = [];

                foreach ($group->subAccountGroups as $subGroup) {
                    $subTotal = 0;

                    foreach ($subGroup->ledgers as $ledger) {
                        $opening = $from ? $this->calculateOpeningBalance($ledger->id, null, $from) : 0;
                        $openingBal = is_array($opening) ? $opening['balance'] : $opening;
                        $ledgerTotal = $openingBal + ($ledger->journalDetails->sum('debit_amount') - $ledger->journalDetails->sum('credit_amount'));
                        $subTotal += $ledgerTotal;
                        $subGroups[$subGroup->name]['ledgers'][] = [
                            'name' => $ledger->name,
                            'total' => $ledgerTotal,
                        ];
                    }

                    $subGroups[$subGroup->name]['total'] = $subTotal;
                    $groupTotal += $subTotal;
                }

                $data['Assets'][] = [
                    'name' => $group->name,
                    'subGroups' => $subGroups,
                    'total' => $groupTotal,
                ];

                $assetsTotal += $groupTotal;
            }
        }

        // EQUITY (Accumulated Fund, Reserve, Net Profit)
        $accumFund = 0;
        $reserves = 0;

        if ($accountGroups->has('Liability')) {
            foreach ($accountGroups['Liability'] as $group) {
                foreach ($group->subAccountGroups as $subGroup) {
                    foreach ($subGroup->ledgers as $ledger) {

                        if ($subGroup->name == 'Reserve') {
                            $opening = $from ? $this->calculateOpeningBalance($ledger->id, null, $from) : 0;
                            $openingBal = is_array($opening) ? $opening['balance'] : $opening;
                            $reserves += $openingBal + ($ledger->journalDetails->sum('credit_amount') - $ledger->journalDetails->sum('debit_amount'));
                        }

                        if ($ledger->name === 'Accumulated Fund') {
                            $opening = $from ? $this->calculateOpeningBalance($ledger->id, null, $from) : 0;
                            $openingBal = is_array($opening) ? $opening['balance'] : $opening;
                            $accumFund += $openingBal + ($ledger->journalDetails->sum('credit_amount') - $ledger->journalDetails->sum('debit_amount'));
                        }
                    }
                }
            }
        }

        // Net Profit = Income - Expenses
        $income = 0;
        if ($accountGroups->has('Income')) {
            foreach ($accountGroups['Income'] as $group) {
                foreach ($group->subAccountGroups as $subGroup) {
                    foreach ($subGroup->ledgers as $ledger) {
                        $opening = $from ? $this->calculateOpeningBalance($ledger->id, null, $from) : 0;
                        $openingBal = is_array($opening) ? $opening['balance'] : $opening;
                        $income += $openingBal + ($ledger->journalDetails->sum('credit_amount') - $ledger->journalDetails->sum('debit_amount'));
                    }
                }
            }
        }

        $expenses = 0;
        if ($accountGroups->has('Expenses')) {
            foreach ($accountGroups['Expenses'] as $group) {
                foreach ($group->subAccountGroups as $subGroup) {
                    foreach ($subGroup->ledgers as $ledger) {
                        $opening = $from ? $this->calculateOpeningBalance($ledger->id, null, $from) : 0;
                        $openingBal = is_array($opening) ? $opening['balance'] : $opening;
                        $expenses += $openingBal + ($ledger->journalDetails->sum('debit_amount') - $ledger->journalDetails->sum('credit_amount'));
                    }
                }
            }
        }

        $netProfit = $income - $expenses;

        $data['Equity'][] = ['name' => 'Accumulated Fund', 'total' => $accumFund];
        $data['Equity'][] = ['name' => 'Net Profit', 'total' => $netProfit];
        $data['Equity'][] = ['name' => 'Reserves', 'total' => $reserves];

        $equityTotal = $accumFund + $netProfit + $reserves;

        // CURRENT LIABILITIES
        if ($accountGroups->has('Liability')) {
            foreach ($accountGroups['Liability'] as $group) {

                $payableTotal = 0;
                $creditorsTotal = 0;

                foreach ($group->subAccountGroups as $subGroup) {
                    if ($subGroup->name != 'Payable') continue;
                    foreach ($subGroup->ledgers as $ledger) {
                        if (trim($ledger->name) === 'Service Charge 30%') {
                            $opening = $from ? $this->calculateOpeningBalance($ledger->id, null, $from) : 0;
                            $openingBal = is_array($opening) ? $opening['balance'] : $opening;
                            $payableTotal += $openingBal + ($ledger->journalDetails->sum('credit_amount') - $ledger->journalDetails->sum('debit_amount'));
                        } else {
                            $opening = $from ? $this->calculateOpeningBalance($ledger->id, null, $from) : 0;
                            $openingBal = is_array($opening) ? $opening['balance'] : $opening;
                            $creditorsTotal += $openingBal + ($ledger->journalDetails->sum('credit_amount') - $ledger->journalDetails->sum('debit_amount'));
                        }
                    }
                }

                $data['CurrentLiabilities'][] = [
                    'name' => 'Owner Payable Service Charge 30%',
                    'total' => $payableTotal
                ];

                $data['CurrentLiabilities'][] = [
                    'name' => 'Creditors',
                    'total' => $creditorsTotal
                ];

                $liabilitiesTotal += $payableTotal;
                $liabilitiesTotal += $creditorsTotal;
            }
        }

        return compact('data', 'assetsTotal', 'equityTotal', 'liabilitiesTotal');
        //return view('reports.balance_sheet', compact('data', 'assetsTotal', 'equityTotal', 'liabilitiesTotal'));
    }



    private function getTrialBalanceData(Request $request)
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $from = $request->from_date;
        $to = $request->to_date;

        $accountGroups = AccountGroup::with([
            'subAccountGroups.ledgers' => function ($q) use ($from, $to) {
                $q->with([
                    'directJournalDetails' => function ($query) use ($from, $to) {
                        $query->when($from && $to, function ($q2) use ($from, $to) {
                            $q2->whereHas('journalEntry', function ($q3) use ($from, $to) {
                                $q3->whereBetween('journal_date', [$from, $to]);
                            });
                        })->whereNull('sub_ledger_id');
                    },
                    'subLedgers.journalDetails' => function ($query) use ($from, $to) {
                        $query->when($from && $to, function ($q2) use ($from, $to) {
                            $q2->whereHas('journalEntry', function ($q3) use ($from, $to) {
                                $q3->whereBetween('journal_date', [$from, $to]);
                            });
                        });
                    }
                ]);
            }
        ])->get();

        $trialData = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accountGroups as $group) {
            foreach ($group->subAccountGroups as $subGroup) {
                foreach ($subGroup->ledgers as $ledger) {

                    // 1. Ledger own entries
                    $ledgerDebit = $ledger->directJournalDetails->sum('debit_amount');
                    $ledgerCredit = $ledger->directJournalDetails->sum('credit_amount');

                    // 2. Ledger Opening Balance (if date filter applied)
                    $openingBalance = $from ? $this->calculateOpeningBalance($ledger->id, null, $from) : 0;
                    $openingBalance = is_array($openingBalance) ? $openingBalance['balance'] : $openingBalance;
                    // 3. Subledgers processing
                    $subRows = [];
                    $subDebitTotal = 0;
                    $subCreditTotal = 0;

                    foreach ($ledger->subLedgers as $sub) {
                        $sd = $sub->journalDetails->sum('debit_amount');
                        $sc = $sub->journalDetails->sum('credit_amount');
                        $subOpening = $from ? $this->calculateOpeningBalance($ledger->id, $sub->id, $from) : 0;
                        $subOpening = is_array($subOpening) ? $subOpening['balance'] : $subOpening;
                        $subBalance = $subOpening + ($sd - $sc);
                        [$subDebit, $subCredit] = $this->adjustBalance($group->name, $subBalance);

                        if ($subDebit != 0 || $subCredit != 0) {
                            $subRows[] = [
                                'group' => $group->name,
                                'sub_group' => $subGroup->name,
                                'ledger' => '↳ ' . $sub->name,
                                'debit' => $subDebit,
                                'credit' => $subCredit,
                                'is_sub' => true,
                            ];
                        }

                        $subDebitTotal += $sd;
                        $subCreditTotal += $sc;
                        $openingBalance += $subOpening; // Include subledger opening into ledger total
                    }

                    // 4. Ledger total (with subledgers and opening)
                    $totalLedgerDebit = $ledgerDebit + $subDebitTotal;
                    $totalLedgerCredit = $ledgerCredit + $subCreditTotal;
                    $balance = $openingBalance + ($totalLedgerDebit - $totalLedgerCredit);
                    [$adjDebit, $adjCredit] = $this->adjustBalance($group->name, $balance);

                    if ($adjDebit != 0 || $adjCredit != 0) {
                        $trialData[] = [
                            'group' => $group->name,
                            'sub_group' => $subGroup->name,
                            'ledger' => $ledger->name,
                            'debit' => $adjDebit,
                            'credit' => $adjCredit,
                            'is_sub' => false,
                        ];

                        $totalDebit += $adjDebit;
                        $totalCredit += $adjCredit;
                    }

                    // 5. Append subledger rows
                    foreach ($subRows as $row) {
                        $trialData[] = $row;
                    }
                }
            }
        }

        return [$trialData, $totalDebit, $totalCredit];
    }


    public function printTrialBalance(Request $request)
    {
        [$trialData, $totalDebit, $totalCredit] = $this->getTrialBalanceData($request);
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $pdf = Pdf::loadView('reports.trial_balance_print', compact('trialData', 'totalDebit', 'totalCredit', 'fromDate', 'toDate'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('trial_balance.pdf');
    }

    public function printBalanceSheet(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $balanceSheetData = $this->getBalanceSheetData($fromDate, $toDate);

        $pdf = PDF::loadView('reports.balance_sheet_print', array_merge(
            $balanceSheetData,
            compact('fromDate', 'toDate')
        ))->setPaper('A4', 'portrait');

        return $pdf->stream('balance_sheet.pdf');
    }

    private function adjustBalance($groupName, $balance)
    {
        $debit = 0;
        $credit = 0;

        switch (strtolower($groupName)) {
            case 'assets':
                $balance >= 0 ? $debit = $balance : $credit = abs($balance);
                break;
            case 'liability':
            case 'income':
            case 'expenses':
                $balance < 0 ? $credit = abs($balance) : $debit = $balance;
                break;
            default:
                $debit = $balance >= 0 ? $balance : 0;
                $credit = $balance < 0 ? abs($balance) : 0;
        }

        return [$debit, $credit];
    }


    public function indexProduction()
    {
        $yahaies = Yahai::all();
        $owners = Membership::all();
        $buyers = Buyer::all();

        return view('reports.production.index', compact('yahaies', 'owners', 'buyers'));
    }

    public function indexOwnerLaon()
    {
        $yahaies = Yahai::all();
        $owners = Owner::all();

        return view('reports.ownerLoan.index', compact('owners', 'yahaies'));
    }

    public function yahaiWiseLoanPrint(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;


        $memberships = Membership::with([
            'saltern.yahai',
            'ownerLoans' => function ($query) use ($request) {
                if ($request->from_date && $request->to_date) {
                    $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                } elseif ($request->from_date) {
                    $query->whereDate('created_at', '>=', $request->from_date);
                } elseif ($request->to_date) {
                    $query->whereDate('created_at', '<=', $request->to_date);
                }

                $query->with(['ownerLoanRepayment' => function ($q) {
                    $q->orderBy('repayment_date');
                }]);
            }
        ])
            ->where('id', $request->membership_id)
            ->get();

        $owner = $memberships->first()?->owner;


        $grouped = [];

        foreach ($memberships as $membership) {
            $salternName = $membership->saltern->yahai->name . " - " . $membership->saltern->name;

            foreach ($membership->ownerLoans as $loan) {
                $balance = $loan->approved_amount;
                $loanRows = [];

                // Add initial loan row
                $loanRows[] = [
                    'date' => $loan->created_at->format('Y-m-d'),
                    'description' => 'Loan Issued Loan#' . $loan->id,
                    'debit' => $loan->approved_amount,
                    'credit' => null,
                    'balance' => $balance,
                ];

                foreach ($loan->ownerLoanRepayment as $repayment) {
                    $balance -= $repayment->amount;

                    $loanRows[] = [
                        'date' => $repayment->repayment_date,
                        'description' => 'Loan Repayment#' . $repayment->id . (!empty($repayment->buyer->full_name) ? ' (' . $repayment->buyer->full_name . ')' : ''),
                        'debit' => null,
                        'credit' => $repayment->amount,
                        'balance' => $balance,
                    ];
                }

                $grouped[$salternName][] = [
                    'loan_id' => $loan->id,
                    'rows' => $loanRows,
                ];
            }
        }

        $pdf = Pdf::loadView('reports.ownerLoan.print-owner-loan', compact('memberships', 'grouped', 'owner', 'fromDate', 'toDate'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('owner_loan_report.pdf');
    }

    public function ownerLoanReport(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);


        $memberships = Membership::with([
            'saltern.yahai',
            'ownerLoans' => function ($query) use ($request) {
                if ($request->from_date && $request->to_date) {
                    $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                } elseif ($request->from_date) {
                    $query->whereDate('created_at', '>=', $request->from_date);
                } elseif ($request->to_date) {
                    $query->whereDate('created_at', '<=', $request->to_date);
                }

                $query->with(['ownerLoanRepayment' => function ($q) {
                    $q->orderBy('repayment_date');
                }]);
            }
        ])
            ->where('id', $request->membership_id)
            ->get();

        $owner = $memberships->first()?->owner;


        $grouped = [];

        foreach ($memberships as $membership) {
            $salternName = $membership->saltern->yahai->name . " - " . $membership->saltern->name;

            foreach ($membership->ownerLoans as $loan) {
                $balance = $loan->approved_amount;
                $loanRows = [];

                // Add initial loan row
                $loanRows[] = [
                    'date' => $loan->created_at->format('Y-m-d'),
                    'description' => 'Loan Issued Loan#' . $loan->id,
                    'debit' => $loan->approved_amount,
                    'credit' => null,
                    'balance' => $balance,
                ];

                foreach ($loan->ownerLoanRepayment as $repayment) {
                    $balance -= $repayment->amount;

                    $loanRows[] = [
                        'date' => $repayment->repayment_date,
                        'description' => 'Loan Repayment#' . $repayment->id . (!empty($repayment->buyer->full_name) ? ' (' . $repayment->buyer->full_name . ')' : ''),
                        'debit' => null,
                        'credit' => $repayment->amount,
                        'balance' => $balance,
                    ];
                }

                $grouped[$salternName][] = [
                    'loan_id' => $loan->id,
                    'rows' => $loanRows,
                ];
            }
        }

        return view('reports.ownerLoan.owner_loan_report', compact('memberships', 'grouped', 'owner'));
    }


    public function generateProduction(Request $request)
    {
        $yahaies = Yahai::all();
        $owners = Membership::all();
        $buyers = Buyer::all();

        $entries = collect();

        if ($request->filled('from_date') && $request->filled('to_date') && $request->filled('yahai_id')) {
            $entries = WeighbridgeEntry::with(['buyer', 'membership.owner', 'membership.saltern.yahai'])
                ->whereBetween('transaction_date', [$request->from_date, $request->to_date])
                ->whereHas('membership.saltern', function ($query) use ($request) {
                    $query->where('yahai_id', $request->yahai_id);
                })
                ->when($request->membership_id, function ($query) use ($request) {
                    $query->whereHas('membership', function ($q) use ($request) {
                        $q->where('id', $request->membership_id);
                    });
                })
                ->when($request->buyer_id, function ($query) use ($request) {
                    $query->where('buyer_id', $request->buyer_id);
                })
                ->get();
        }

        return view('reports.production.result', compact('yahaies', 'owners', 'buyers', 'entries'));
    }

    public function indexLedger(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $ledgerId = $request->input('ledger_id');

        $query = JournalDetail::query();

        if ($from) {
            $query->whereDate('entry_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('entry_date', '<=', $to);
        }
        if ($ledgerId) {
            $query->where('ledger_id', $ledgerId);
        }

        $entries = $query->get();

        $subAccountGroups = \App\Models\SubAccountGroup::all();

        return view('reports.ledger.index', compact('entries', 'subAccountGroups', 'from', 'to', 'ledgerId'));
    }

    public function generateLedger(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'ledger_id' => 'nullable|exists:ledgers,id',
            'sub_ledger_id' => 'nullable|exists:sub_ledgers,id',
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $ledgerId = $request->ledger_id;
        $subLedgerId = $request->sub_ledger_id;

        // If subledger selected
        if ($subLedgerId) {
            $subLedger = SubLedger::with('ledger')->findOrFail($subLedgerId);
            $ledger = $subLedger->ledger;

            $opening = $this->calculateOpeningBalance($ledger->id, $subLedgerId, $fromDate);
            $journalDetails = $this->getJournalDetails($ledger->id, $subLedgerId, $fromDate, $toDate);

            return view('reports.ledger.result', compact('ledger', 'subLedger', 'opening', 'journalDetails', 'fromDate', 'toDate'));
        }

        // If ledger selected
        if ($ledgerId) {
            $ledger = Ledger::with('subLedgers')->findOrFail($ledgerId);

            // Ledger has subledgers — show per subledger details
            if ($ledger->subLedgers->isNotEmpty()) {
                $subLedgerSummaries = [];

                foreach ($ledger->subLedgers as $sub) {
                    $opening = $this->calculateOpeningBalance($ledgerId, $sub->id, $fromDate);
                    $journalDetails = $this->getJournalDetails($ledgerId, $sub->id, $fromDate, $toDate);

                    // Only add subledger if there's some balance or transactions
                    if ($opening['balance'] != 0 || $journalDetails->isNotEmpty()) {
                        $subLedgerSummaries[] = [
                            'sub_ledger' => $sub,
                            'opening' => $opening,
                            'journalDetails' => $journalDetails,
                        ];
                    }
                }

                return view('reports.ledger.subledger_summary', compact('ledger', 'subLedgerSummaries', 'fromDate', 'toDate'));
            }

            // Ledger has no subledgers — show ledger transactions
            $opening = $this->calculateOpeningBalance($ledgerId, null, $fromDate);
            $journalDetails = $this->getJournalDetails($ledgerId, null, $fromDate, $toDate);

            return view('reports.ledger.summary', compact('ledger', 'opening', 'journalDetails', 'fromDate', 'toDate'));
        }

        return back()->withErrors('Please select a ledger or subledger.');
    }

    public function exportLedgerReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'ledger_id' => 'nullable|exists:ledgers,id',
            'sub_ledger_id' => 'nullable|exists:sub_ledgers,id',
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $ledgerId = $request->ledger_id;
        $subLedgerId = $request->sub_ledger_id;

        if ($subLedgerId) {
            $subLedger = SubLedger::with('ledger')->findOrFail($subLedgerId);
            $ledger = $subLedger->ledger;

            $opening = $this->calculateOpeningBalance($ledger->id, $subLedgerId, $fromDate);
            $journalDetails = $this->getJournalDetails($ledger->id, $subLedgerId, $fromDate, $toDate);

            return Excel::download(new LedgerReportExport(
                'subledger_detail',
                [
                    'opening' => $opening,
                    'journalDetails' => $journalDetails,

                ],
                'PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD',
                $ledger->name . ' - ' . $subLedger->name . ' SubLedger Detail Report 1',
                'For: ' . $fromDate . ' to ' . $toDate
            ), "ledger_subledger_detail_{$fromDate}_to_{$toDate}.xlsx");
        }

        if ($ledgerId) {
            $ledger = Ledger::with('subLedgers')->findOrFail($ledgerId);

            if ($ledger->subLedgers->isNotEmpty()) {
                $subLedgerSummaries = [];

                foreach ($ledger->subLedgers as $sub) {
                    $opening = $this->calculateOpeningBalance($ledgerId, $sub->id, $fromDate);
                    $journalDetails = $this->getJournalDetails($ledgerId, $sub->id, $fromDate, $toDate);

                    if ($opening['balance'] != 0 || $journalDetails->isNotEmpty()) {
                        $subLedgerSummaries[] = [
                            'sub_ledger' => $sub,
                            'opening' => $opening,
                            'journalDetails' => $journalDetails,
                        ];
                    }
                }

                return Excel::download(new LedgerReportExport(
                    'subledger_summary',
                    [
                        'subLedgerSummaries' => $subLedgerSummaries,

                    ],
                    'PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD',
                    $ledger->name . ' Ledger Detail Report 2',
                    'For: ' . $fromDate . ' to ' . $toDate
                ), "ledger_subledger_summary_{$fromDate}_to_{$toDate}.xlsx");
            }

            $opening = $this->calculateOpeningBalance($ledgerId, null, $fromDate);
            $journalDetails = $this->getJournalDetails($ledgerId, null, $fromDate, $toDate);

            return Excel::download(new LedgerReportExport(
                'ledger_detail',
                [
                    'opening' => $opening,
                    'journalDetails' => $journalDetails,
                ],
                'PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD',
                $ledger->name . ' Ledger Detail Report 3',
                'For: ' . $fromDate . ' to ' . $toDate
            ), "ledger_detail_{$fromDate}_to_{$toDate}.xlsx");
        }

        return back()->withErrors('Please select a ledger or subledger.');
    }

    private function calculateOpeningBalance($ledgerId, $subLedgerId = null, $beforeDate)
    {
        $query = JournalDetail::where('ledger_id', $ledgerId)
            ->whereHas('journalEntry', fn($q) => $q->where('journal_date', '<', $beforeDate));

        if ($subLedgerId) {
            $query->where('sub_ledger_id', $subLedgerId);
        } else {
            $query->whereNull('sub_ledger_id');
        }

        $debit = $query->sum('debit_amount');
        $credit = $query->sum('credit_amount');
        $balance = $debit - $credit;

        return [
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
        ];
    }

    private function getJournalDetails($ledgerId, $subLedgerId = null, $fromDate, $toDate)
    {
        $query = JournalDetail::with('journalEntry')
            ->where('ledger_id', $ledgerId)
            ->whereHas(
                'journalEntry',
                fn($q) =>
                $q->whereBetween('journal_date', [$fromDate, $toDate])
            );

        if ($subLedgerId) {
            $query->where('sub_ledger_id', $subLedgerId);
        } else {
            $query->whereNull('sub_ledger_id');
        }

        return $query->get()
            ->sortBy(fn($d) => optional($d->journalEntry)->journal_date ?? '2100-01-01')
            ->values(); // Re-index for blade use
    }


    public function generateBuyerProduction(Request $request)
    {
        $yahaies = Yahai::all();
        $owners = Membership::all();
        $buyers = Buyer::all();

        $entries = collect();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $entries = WeighbridgeEntry::with(['buyer', 'membership.owner', 'membership.saltern.yahai'])
                ->whereBetween('transaction_date', [$request->from_date, $request->to_date])
                ->when($request->buyer_id, function ($query) use ($request) {
                    $query->where('buyer_id', $request->buyer_id);
                })
                ->get();
        }

        return view('reports.production.buyer-result', compact('yahaies', 'owners', 'buyers', 'entries'));
    }

    public function getSalterns(Request $request)
    {
        $salterns = Saltern::with('memberships.owner')
            ->where('yahai_id', $request->yahai_id)
            ->whereHas('memberships') // ensures only those with active membership are included
            ->get();

        return response()->json(['salterns' => $salterns]);
    }

    public function getLedgers(Request $request)
    {
        $ledgers = Ledger::where('sub_account_group_id', $request->sub_account_id)->get();
        return response()->json(['ledgers' => $ledgers]);
    }



    public function getSubLedgers(Request $request)
    {
        $subLedgers = SubLedger::where('ledger_id', $request->ledger_id)->get();
        return response()->json(['subLedgers' => $subLedgers]);
    }

    public function getYahaiWiseLoanTrialBalanceData(Request $request)
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $loans = OwnerLoan::with([
            'membership.owner',
            'membership.saltern.yahai',
            'ownerLoanRepayment'
        ])
            ->when($fromDate, function ($q) use ($fromDate) {
                $q->whereDate('approval_date', '>=', $fromDate);
            })
            ->when($toDate, function ($q) use ($toDate) {
                $q->whereDate('approval_date', '<=', $toDate);
            })
            ->get();

        $grouped = [];

        foreach ($loans as $loan) {
            $yahai = $loan->membership->saltern->yahai->name ?? 'Unknown Yahai';
            $saltern = $loan->membership->saltern->name ?? 'Unknown Saltern';
            $owner = $loan->membership->owner->name_with_initial ?? 'Unknown Owner';

            $repaid = $loan->ownerLoanRepayment->sum('amount');
            $outstanding = $loan->approved_amount - $repaid;

            if (!isset($grouped[$yahai])) {
                $grouped[$yahai] = [];
            }

            $grouped[$yahai][] = [
                'saltern' => $saltern,
                'owner' => $owner,
                'loan_id' => $loan->id,
                'approved' => $loan->approved_amount,
                'repaid' => $repaid,
                'outstanding' => $outstanding,
            ];
        }

        // Yahai-wise totals
        $yahaiTotals = [];
        foreach ($grouped as $yahai => $records) {
            $yahaiTotals[$yahai] = collect($records)->sum('outstanding');
        }

        $grandTotal = array_sum($yahaiTotals);
        return compact('grouped', 'yahaiTotals', 'grandTotal', 'fromDate', 'toDate');
        //  view('reports.loan.loan_trial_balance_detailed', compact('grouped', 'yahaiTotals'));
    }

    public function yahaiWiseLoanTrialBalance(Request $request)
    {
        $data = $this->getYahaiWiseLoanTrialBalanceData($request);
        return view('reports.loan.loan_trial_balance_detailed', $data);
    }

    public function yahaiWiseLoanTrialBalancePrint(Request $request)
    {
        $data = $this->getYahaiWiseLoanTrialBalanceData($request);

        $pdf = Pdf::loadView('reports.loan.loan_trial_balance_print', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->stream('loan_trial_balance.pdf');
    }

    public function pendingPaymentsReport(Request $request)
    {
        $buyers = Buyer::all();
        $report = [];

        $from = $request->from_date;
        $to = $request->to_date;

        foreach ($buyers as $buyer) {
            // Weighbridge Entries (Unpaid service charges)
            $weighbridgeQuery = WeighbridgeEntry::where('buyer_id', $buyer->id)
                ->where('is_service_charge_paid', 0);

            // Apply date range filter if provided
            if ($from && $to) {
                $weighbridgeQuery->whereBetween('transaction_date', [$from, $to]);
            }

            $weighbridgeTotal = $weighbridgeQuery->sum('total_amount');

            // Owner Loan Repayments (Pending within date range)
            $loanQuery = OwnerLoanRepayment::where('buyer_id', $buyer->id)
                ->where('status', 'pending');

            if ($from && $to) {
                $loanQuery->whereBetween('repayment_date', [$from, $to]); // Assuming 'date' field exists
            }

            $loanTotal = $loanQuery->sum('amount');
            // Other Incomes (Pending within date range)
            $incomeQuery = OtherIncome::where('buyer_id', $buyer->id)
                ->where('status', 'pending');

            if ($from && $to) {
                $incomeQuery->whereBetween('received_date', [$from, $to]); // Assuming 'date' field exists
            }

            $incomeTotal = $incomeQuery->sum('amount');
            $total = $weighbridgeTotal + $loanTotal + $incomeTotal;

            // Only include if any pending amount exists
            if ($total > 0) {
                $report[] = [
                    'buyer' => $buyer,
                    'weighbridge' => $weighbridgeTotal,
                    'loan' => $loanTotal,
                    'income' => $incomeTotal,
                    'total' => $total,
                ];
            }
        }

        // For final total
        $grandTotal = [
            'weighbridge' => array_sum(array_column($report, 'weighbridge')),
            'loan' => array_sum(array_column($report, 'loan')),
            'income' => array_sum(array_column($report, 'income')),
            'total' => array_sum(array_column($report, 'total')),
        ];

        return view('reports.pending.pending-payments', compact('report', 'grandTotal'));
    }


    public function voucherReport(Request $request)
    {
        $query = Voucher::with(['paymentMethod', 'bank']);

        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $fromDate,
                $toDate
            ]);
        }

        $vouchers = $query->orderBy('created_at', 'desc')->get();

        return view('reports.voucher.voucher-details', compact('vouchers', 'fromDate', 'toDate'));
    }

    public function generateLedgerPdf(Request $request)
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'ledger_id' => 'nullable|exists:ledgers,id',
            'sub_ledger_id' => 'nullable|exists:sub_ledgers,id',
        ]);

        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $ledgerId = $request->ledger_id;
        $subLedgerId = $request->sub_ledger_id;

        if ($subLedgerId) {
            $subLedger = SubLedger::with('ledger')->findOrFail($subLedgerId);
            $ledger = $subLedger->ledger;

            $opening = $this->calculateOpeningBalance($ledger->id, $subLedgerId, $fromDate);
            $journalDetails = $this->getJournalDetails($ledger->id, $subLedgerId, $fromDate, $toDate);

            $pdf = Pdf::loadView('reports.ledger.result_print', compact('ledger', 'subLedger', 'opening', 'journalDetails', 'fromDate', 'toDate'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('ledger-subledger-report.pdf');
        }

        if ($ledgerId) {
            $ledger = Ledger::with('subLedgers')->findOrFail($ledgerId);

            if ($ledger->subLedgers->isNotEmpty()) {
                $subLedgerSummaries = [];

                foreach ($ledger->subLedgers as $sub) {
                    $opening = $this->calculateOpeningBalance($ledgerId, $sub->id, $fromDate);
                    $journalDetails = $this->getJournalDetails($ledgerId, $sub->id, $fromDate, $toDate);

                    if ($opening['balance'] != 0 || $journalDetails->isNotEmpty()) {
                        $subLedgerSummaries[] = [
                            'sub_ledger' => $sub,
                            'opening' => $opening,
                            'journalDetails' => $journalDetails,
                        ];
                    }
                }

                $pdf = Pdf::loadView('reports.ledger.subledger_summary_print', compact('ledger', 'subLedgerSummaries', 'fromDate', 'toDate'))
                    ->setPaper('a4', 'portrait');

                return $pdf->stream('ledger-subledgers-summary.pdf');
            }

            $opening = $this->calculateOpeningBalance($ledgerId, null, $fromDate);
            $journalDetails = $this->getJournalDetails($ledgerId, null, $fromDate, $toDate);

            $pdf = Pdf::loadView('reports.ledger.summary_print', compact('ledger', 'opening', 'journalDetails', 'fromDate', 'toDate'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('ledger-summary.pdf');
        }

        return back()->withErrors('Please select a ledger or subledger.');
    }

    public function generateAllProduction(Request $request)
    {
        $entries = $this->getAllProduction($request);
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        return view('reports.production.all-production', compact('entries', 'fromDate', 'toDate'));
    }

    private function getAllProduction(Request $request)
    {
        $entries = [];

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = $request->from_date . ' 00:00:00';
            $to = $request->to_date . ' 23:59:59';

            $entries = WeighbridgeEntry::with(['buyer', 'membership.owner', 'membership.saltern'])
                ->whereBetween('transaction_date', [$from, $to])
                ->orderBy('transaction_date', 'desc')
                ->get();
        }

        return $entries;
    }

    public function printAllProduction(Request $request)
    {
        $entries = $this->getAllProduction($request);
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $pdf = Pdf::loadView('reports.production.all-production-print', compact('entries', 'fromDate', 'toDate'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('all-production-report.pdf');
    }
}
