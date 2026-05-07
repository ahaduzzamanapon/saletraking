<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visit;
use App\Models\Target;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $date  = $request->date('date', 'Y-m-d') ?? today();
        $month = $request->input('month', today()->format('Y-m'));

        // Daily report
        $dailyReps = User::where('role', 'salesperson')
            ->where('is_active', true)
            ->with([
                'visits' => fn($q) => $q->whereDate('checkin_at', $date)->with('client:id,name'),
                'targets' => fn($q) => $q->where('type', 'daily')->whereDate('period_date', $date),
            ])
            ->get()
            ->map(function ($rep) {
                $visits    = $rep->visits;
                $target    = $rep->targets->first();
                return [
                    'id'               => $rep->id,
                    'name'             => $rep->name,
                    'employee_id'      => $rep->employee_id,
                    'visits_done'      => $visits->where('status', 'completed')->count(),
                    'visits_target'    => $target?->visits_target ?? 0,
                    'sales_achieved'   => $visits->sum('order_value'),
                    'sales_target'     => $target?->sales_target ?? 0,
                    'avg_rating'       => round($visits->whereNotNull('rating')->avg('rating'), 1),
                    'last_seen'        => $rep->last_seen_at?->format('H:i'),
                ];
            });

        // Leaderboard (this month)
        [$year, $mon] = explode('-', $month);
        $startOfMonth = \Carbon\Carbon::createFromDate($year, $mon, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $leaderboard = User::where('role', 'salesperson')
            ->withCount(['visits as visits_count' => fn($q) => $q->whereBetween('checkin_at', [$startOfMonth, $endOfMonth])->where('status', 'completed')])
            ->withSum(['visits as sales_sum' => fn($q) => $q->whereBetween('checkin_at', [$startOfMonth, $endOfMonth])], 'order_value')
            ->orderByDesc('visits_count')
            ->get();

        return view('admin.reports.index', compact('dailyReps', 'leaderboard', 'date', 'month'));
    }
}
