<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with(['salesperson:id,name', 'visit.client:id,name'])
            ->orderByDesc('created_at');

        if ($request->filled('rep')) {
            $query->where('salesperson_id', $request->rep);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        } else {
            $query->whereDate('created_at', today());
        }

        $feedbacks = $query->paginate(25)->withQueryString();
        $reps      = User::where('role', 'salesperson')->orderBy('name')->get(['id', 'name']);

        return view('admin.feedback.index', compact('feedbacks', 'reps'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load(['salesperson', 'visit.client', 'template']);
        return view('admin.feedback.show', compact('feedback'));
    }
}
