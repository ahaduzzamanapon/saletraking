<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;

class SalespersonController extends Controller
{
    public function index()
    {
        $reps = User::where('role', 'salesperson')
            ->with('territory:id,name')
            ->withCount(['visits as visits_today' => fn($q) => $q->whereDate('checkin_at', today())])
            ->withCount(['visits as visits_total'])
            ->orderBy('name')
            ->get();

        return view('admin.salespersons.index', compact('reps'));
    }

    public function show(User $salesperson)
    {
        abort_if($salesperson->role !== 'salesperson', 404);

        $salesperson->load(['territory', 'latestLocation']);

        $visitsToday = Visit::where('salesperson_id', $salesperson->id)
            ->whereDate('checkin_at', today())
            ->with('client:id,name')
            ->orderByDesc('checkin_at')
            ->get();

        $dailyTarget  = $salesperson->todayTarget();
        $monthlyTarget = Target::where('user_id', $salesperson->id)
            ->where('type', 'monthly')
            ->whereDate('period_date', today()->startOfMonth())
            ->first();

        $recentVisits = Visit::where('salesperson_id', $salesperson->id)
            ->with('client:id,name')
            ->orderByDesc('checkin_at')
            ->limit(20)
            ->get();

        return view('admin.salespersons.show', compact('salesperson', 'visitsToday', 'dailyTarget', 'monthlyTarget', 'recentVisits'));
    }
}
