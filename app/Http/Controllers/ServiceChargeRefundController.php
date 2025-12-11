<?php

namespace App\Http\Controllers;

use App\Models\ServiceChargeRefund;
use App\Models\WeighbridgeEntry;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceChargeRefundController extends Controller
{
    /**
     * Show filter form and (optionally) results
     */
    public function index(Request $request)
    {
        // just show filter form; if query present, we keep values in form
        return view('refunds.index');
    }

    /**
     * Preview grouped totals per membership for a date range
     */
    public function preview(Request $request)
    {
        $v = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        if ($v->fails()) {
            return redirect()->route('refunds.index')
                ->withErrors($v)
                ->withInput();
        }

        $from = $request->from_date;
        $to = $request->to_date;

        // Get non-refunded entries in range
        $entries = WeighbridgeEntry::whereBetween('transaction_date', [$from, $to])
        ->whereNull('refund_id') // only entries not refunded yet
        ->with(['membership.saltern.yahai', 'membership.owner']) // eager load relationships
        ->get(['membership_id', 'total_amount']);

        // Group totals by membership_id
        $grouped = $entries->groupBy('membership_id')->map(function ($rows, $membershipId) {
            $total = $rows->sum('service_charge') ?: 0;
            return (object) [
                'membership_id' => $membershipId,
                'membership' => $rows->first()->membership, // get membership object
                'total_service_charge' => round($total, 2),
                'count' => $rows->count(),
            ];
        })->values();

        $refundPercentage = SettingsHelper::get('owner_share_percentage', 30);

        return view('refunds.preview', compact('grouped', 'from', 'to', 'refundPercentage'));
    }

    /**
     * Approve selected refunds (or all if none selected)
     */
    public function approve(Request $request)
    {
        $v = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'membership_ids' => 'nullable|array',
            'membership_ids.*' => 'integer',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        $from = $request->from_date;
        $to = $request->to_date;
        $selected = $request->membership_ids; // array of membership ids, or null => all

        $refundPercentage = SettingsHelper::get('owner_share_percentage', 30);

        $refundPercDecimal = floatval($refundPercentage);

        DB::beginTransaction();

        try {
            // Build query for eligible entries (not refunded)
            $query = WeighbridgeEntry::whereBetween('transaction_date', [$from, $to])
    ->whereNull('refund_id'); // only entries not refunded

            if (!empty($selected) && is_array($selected)) {
                $query->whereIn('membership_id', $selected);
            }

            $entries = $query->get(['id', 'membership_id', 'total_amount']);

            if ($entries->isEmpty()) {
                DB::rollBack();
                return redirect()->route('refunds.index')->with('warning', 'No eligible entries found to refund.');
            }

            $groups = $entries->groupBy('membership_id');

            foreach ($groups as $membershipId => $rows) {
                $totalServiceCharge = $rows->sum('total_amount') ?: 0;
                $refundAmount = round(($totalServiceCharge * $refundPercDecimal) / 100, 2);

                // create refund record
                $refund = ServiceChargeRefund::create([
                    'membership_id' => $membershipId,
                    'total_service_charge' => $totalServiceCharge,
                    'refund_amount' => $refundAmount,
                    'from_date' => $from,
                    'to_date' => $to,
                    'created_by' => auth('web')->id(),
                ]);

                // mark entries as refunded and link refund_id
                WeighbridgeEntry::whereIn('id', $rows->pluck('id')->all())
                    ->update([
                        'refund_id' => $refund->id,
                    ]);
            }

            DB::commit();

            return redirect()->route('refunds.history')->with('success', 'Refunds approved and saved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Refund approve failed: '.$e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while approving refunds: '.$e->getMessage());
        }
    }

    /**
     * Show history/list of refund batches
     */
    public function history(Request $request)
    {
        $refunds = ServiceChargeRefund::orderBy('created_at', 'desc')->paginate(25);
        return view('refunds.history', compact('refunds'));
    }

    /**
     * Show single refund details including linked entries
     */
    public function show(ServiceChargeRefund $refund)
    {
        // fetch entries linked via refund_id
        $entries = WeighbridgeEntry::where('refund_id', $refund->id)->get();

        return view('refunds.show', compact('refund', 'entries'));
    }
}
