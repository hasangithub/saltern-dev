<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date of Loan</th>
                <th>Loan Amount</th>
                <th>Outstanding Amount</th>
                <th>Amount to Pay</th>
            </tr>
        </thead>
        <tbody>
            @php
                $filteredLoans = $loans->filter(function ($loan) {
                    $outstanding = $loan->approved_amount - $loan->ownerLoanRepayment->sum('amount');
                    return $outstanding > 0;
                });
            @endphp

            @forelse ($filteredLoans as $loan)
                @php
                    $outstanding = $loan->approved_amount - $loan->ownerLoanRepayment->sum('amount');
                @endphp
                <tr>
                    <td>{{ $loan->created_at->format('Y-m-d') }}</td>
                    <td>{{ number_format($loan->approved_amount, 2) }}</td>
                    <td>{{ number_format($outstanding, 2) }}</td>
                    <td>
                        <input type="number"
                            name="repayments[{{ $loan->id }}]"
                            max="{{ $outstanding }}"
                            step="0.01"
                            class="form-control">
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No outstanding loans for this saltern.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
