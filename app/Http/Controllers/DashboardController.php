<?php

namespace App\Http\Controllers;

use App\Models\WeighbridgeEntry;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek(); // Monday
        $endOfWeek = Carbon::now()->endOfWeek();     // Sunday
    
        // ===== TODAY =====
        $totalServiceChargeToday = WeighbridgeEntry::whereDate('created_at', $today)->sum('total_amount');
        $totalWeighbridgeEntriesToday = WeighbridgeEntry::whereDate('created_at', $today)->count();
    
        // ===== THIS WEEK =====
        $totalServiceChargeWeek = WeighbridgeEntry::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->sum('total_amount');
        $totalWeighbridgeEntriesWeek = WeighbridgeEntry::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();
    
        return view('dashboard', compact(
            'totalServiceChargeToday',
            'totalWeighbridgeEntriesToday',
            'totalServiceChargeWeek',
            'totalWeighbridgeEntriesWeek'
        ));
    }

    public function ownerIndex()
    {

    }
}
