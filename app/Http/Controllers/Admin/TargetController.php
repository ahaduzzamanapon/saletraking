<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index()
    {
        $reps = User::where('role', 'salesperson')
            ->where('is_active', true)
            ->with([
                'targets' => fn($q) => $q->where('type', 'daily')->whereDate('period_date', today()),
            ])
            ->orderBy('name')
            ->get();

        return view('admin.targets.index', compact('reps'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'type'           => 'required|in:daily,monthly',
            'period_date'    => 'required|date',
            'visits_target'  => 'required|integer|min:0',
            'sales_target'   => 'required|numeric|min:0',
        ]);

        Target::updateOrCreate(
            ['user_id' => $data['user_id'], 'type' => $data['type'], 'period_date' => $data['period_date']],
            ['visits_target' => $data['visits_target'], 'sales_target' => $data['sales_target']]
        );

        return redirect()->route('admin.targets.index')->with('success', 'Target set successfully.');
    }
}
