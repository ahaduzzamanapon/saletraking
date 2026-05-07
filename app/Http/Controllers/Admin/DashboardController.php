<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Target;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $totalReps      = User::where('role', 'salesperson')->where('is_active', true)->count();
        $activeToday    = User::where('role', 'salesperson')
                              ->where('last_seen_at', '>=', now()->subHour())
                              ->count();
        $visitsToday    = Visit::whereDate('checkin_at', $today)->count();
        $completedToday = Visit::whereDate('checkin_at', $today)->where('status', 'completed')->count();
        $missedToday    = Visit::whereDate('checkin_at', $today)->where('status', 'missed')->count();
        $revenueToday   = Visit::whereDate('checkin_at', $today)->whereNotNull('order_value')->sum('order_value');

        // Top reps by visits today
        $topReps = User::where('role', 'salesperson')
            ->withCount(['visits as visits_today' => fn($q) => $q->whereDate('checkin_at', $today)])
            ->orderByDesc('visits_today')
            ->limit(5)
            ->get();

        // Recent visits
        $recentVisits = Visit::with(['salesperson:id,name', 'client:id,name'])
            ->whereDate('checkin_at', $today)
            ->orderByDesc('checkin_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalReps', 'activeToday', 'visitsToday', 'completedToday',
            'missedToday', 'revenueToday', 'topReps', 'recentVisits'
        ));
    }
}
