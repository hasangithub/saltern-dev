<?php 
namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Membership;
use App\Models\Representative;

class DashboardController extends Controller
{
    public function index()
    {
    // Owners by Month for Chart.js
    $ownersByMonth = Owner::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->pluck('count', 'month');

    // Example: Recent Memberships
    $recentMemberships = Membership::orderBy('created_at', 'desc')->limit(5)->get();

    // Example: Recent Representatives
    $recentRepresentatives = Representative::orderBy('created_at', 'desc')->limit(5)->get();

    // Pass all data to the dashboard view
    return view('dashboard', [
        'months' => $ownersByMonth->keys(),
        'counts' => $ownersByMonth->values(),
        'recentMemberships' => $recentMemberships,
        'recentRepresentatives' => $recentRepresentatives,
    ]);
}
}