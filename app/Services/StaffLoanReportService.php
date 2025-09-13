<?php

namespace App\Services;

use App\Models\User;

class StaffLoanReportService
{
    /**
     * Prepare the staff loan report data.
     *
     * @param array $filters
     * @return array
     */
    public function prepare(array $filters): array
    {
        $fromDate = $filters['from_date'] ?? null;
        $toDate   = $filters['to_date'] ?? null;
        $userId   = $filters['user_id'] ?? null;


        $users = User::with([
            'staffLoans' => function ($query) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $query->whereBetween('created_at', [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $query->whereDate('created_at', '>=', $fromDate);
                } elseif ($toDate) {
                    $query->whereDate('created_at', '<=', $toDate);
                }

                $query->with(['staffLoanRepayment' => function ($q) {
                    $q->orderBy('repayment_date');
                }]);
            }
        ])
        ->when($userId, function ($q) use ($userId) {
            $q->where('id', $userId);   // ✅ only this user
        })
        ->get();

        $grouped = [];

        $totalOutstanding = 0;
        foreach ($users as $user) {
            $userName = $user->name;

            foreach ($user->staffLoans as $loan) {
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

                foreach ($loan->staffLoanRepayment as $repayment) {
                    $balance -= $repayment->amount;

                    $loanRows[] = [
                        'date' => $repayment->repayment_date,
                        'description' => 'Loan Repayment#' . $repayment->id,
                        'debit' => null,
                        'credit' => $repayment->amount,
                        'balance' => $balance,
                    ];
                }

                $totalOutstanding += $balance;

                $grouped[$userName][] = [
                    'loan_id' => $loan->id,
                    'rows' => $loanRows,
                ];
            }
        }


        return [
            'memberships' => $users,
            'grouped'     => $grouped,
            'fromDate'    => $fromDate,
            'toDate'      => $toDate,
            'totalOutstanding' => $totalOutstanding
        ];
    }
}
