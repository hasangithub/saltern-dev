<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date of Loan</th>
            <th>Loan Amount</th>
            <th>Outstanding Amount</th>
            <th>Paying Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($loans as $loan)
            <tr>
                <td>{{ date('Y-m-d', strtotime($loan->created_at)) }}</td>
                <td>{{ number_format($loan->approved_amount, 2) }}</td>
                <td>{{ number_format($loan->approved_amount - $loan->ownerLoanRepayment->sum('amount'), 2) }}</td>
                <td>
                    <input type="number" class="form-control paying-amount" 
                           data-loan-id="{{ $loan->id }}" 
                           placeholder="Enter amount to pay" />
                </td>
                <td>
                    <button class="btn btn-primary pay-button" data-loan-id="{{ $loan->id }}">Pay</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">No loans available for this saltern.</td>
            </tr>
        @endforelse
    </tbody>
</table>
