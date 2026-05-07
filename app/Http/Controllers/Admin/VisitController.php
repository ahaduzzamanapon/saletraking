<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with(['salesperson:id,name', 'client:id,name'])
            ->orderByDesc('checkin_at');

        if ($request->filled('date')) {
            $query->whereDate('checkin_at', $request->date);
        } else {
            $query->whereDate('checkin_at', today());
        }
        if ($request->filled('rep')) {
            $query->where('salesperson_id', $request->rep);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->paginate(25)->withQueryString();
        $reps   = User::where('role', 'salesperson')->orderBy('name')->get(['id', 'name']);

        return view('admin.visits.index', compact('visits', 'reps'));
    }

    public function show(Visit $visit)
    {
        $visit->load(['salesperson', 'client', 'feedback.template']);
        return view('admin.visits.show', compact('visit'));
    }
}
