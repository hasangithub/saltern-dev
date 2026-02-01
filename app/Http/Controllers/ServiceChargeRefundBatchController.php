<?php

namespace App\Http\Controllers;

use App\Models\RefundBatch;
use App\Models\ServiceChargeRefund;
use App\Models\WeighbridgeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceChargeRefundBatchController extends Controller
{
    /* ==========================
     | 1. List batches
     ========================== */
    public function index()
    {
        $batches = RefundBatch::orderBy('id', 'desc')->get();

        return view('refund_batches.index', compact('batches'));
    }

    /* ==========================
     | 2. Create batch
     ========================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string',
            'date_from'         => 'required|date',
            'date_to'           => 'required|date',
            'refund_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data['status']     = 'draft';
        $data['created_by'] = auth()->id();

        RefundBatch::create($data);

        return redirect()
            ->route('refund-batches.index')
            ->with('success', 'Refund batch created');
    }

    /* ==========================
     | 3. Show batch
     ========================== */
    public function show(RefundBatch $batch)
    {
        $refunds = ServiceChargeRefund::with('memberships')
            ->where('refund_batch_id', $batch->id)
            ->get();

        return view('refund_batches.show', compact('batch', 'refunds'));
    }

    /* ==========================
     | 4. Load service charges
     ========================== */
    public function loadServiceCharges(RefundBatch $batch)
    {
        abort_if($batch->status !== 'draft', 403);

        $entries = WeighbridgeEntry::whereBetween('transaction_date', [
                $batch->date_from,
                $batch->date_to
            ])
            ->whereNull('refund_id')
            ->selectRaw('membership_id, SUM(total_amount) as total')
            ->groupBy('membership_id')
            ->get();

        foreach ($entries as $row) {

            ServiceChargeRefund::updateOrCreate(
                [
                    'refund_batch_id' => $batch->id,
                    'membership_id'   => $row->membership_id,
                ],
                [
                    'from_date'            => $batch->date_from,  
                    'to_date'            => $batch->date_to,  
                    'total_service_charge' => $row->total,
                    'refund_amount'        => round($row->total * ($batch->refund_percentage / 100),2),
                    'created_by'           => auth('web')->id(),
                ]
            );
        }

        return back()->with('success', 'Service charges loaded');
    }

    /* ==========================
     | 5. Approve batch
     ========================== */
    public function approve(RefundBatch $batch)
    {
        abort_if($batch->status !== 'draft', 403);

        $batch->update([
            'status'       => 'approved',
            'approved_by'  => auth('web')->id(),
            'approved_at'  => now(),
        ]);

        return back()->with('success', 'Batch approved');
    }

    /* ==========================
     | 6. Post refunds (CRITICAL)
     ========================== */
    public function post(RefundBatch $batch)
    {
        abort_if($batch->status !== 'approved', 403);

        DB::transaction(function () use ($batch) {

            $refunds = ServiceChargeRefund::where('refund_batch_id', $batch->id)
                ->whereNull('voucher_id')
                ->get();

            foreach ($refunds as $refund) {

                // 🔹 Create voucher per membership (pseudo)
                $voucherId = $this->createVoucher($refund);

                $refund->update([
                    'voucher_id' => $voucherId,
                    'status'     => 'posted',
                ]);

                // 🔹 Lock weighbridge entries
                WeighbridgeEntry::where('membership_id', $refund->membership_id)
                    ->whereNull('service_charge_refund_id')
                    ->whereBetween('date', [
                        $batch->date_from,
                        $batch->date_to
                    ])
                    ->update([
                        'service_charge_refund_id' => $refund->id
                    ]);
            }

            $batch->update([
                'status'    => 'posted',
                'posted_at' => now(),
            ]);
        });

        return back()->with('success', 'Refunds posted & vouchers created');
    }

    /* ==========================
     | Voucher stub
     ========================== */
    private function createVoucher(ServiceChargeRefund $refund): int
    {
        // 🔴 Replace with real voucher logic
        return rand(1000, 9999);
    }
}
