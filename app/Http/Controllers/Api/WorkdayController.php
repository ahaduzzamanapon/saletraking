<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class WorkdayController extends Controller
{
    public function start(Request $request)
    {
        $user = $request->user();

        if ($user->is_working) {
            return response()->json([
                'message'     => 'Already started',
                'started_at'  => $user->work_started_at,
                'is_working'  => true,
            ]);
        }

        $user->update([
            'is_working'      => true,
            'work_started_at' => now(),
            'work_ended_at'   => null,
            'last_seen_at'    => now(),
        ]);

        return response()->json([
            'message'    => 'Day started. GPS tracking active.',
            'started_at' => $user->work_started_at,
            'is_working' => true,
        ]);
    }

    public function end(Request $request)
    {
        $user = $request->user();

        $user->update([
            'is_working'    => false,
            'work_ended_at' => now(),
        ]);

        // Summary of today's work
        $visitsToday = $user->visits()->whereDate('checkin_at', today())->count();
        $duration    = $user->work_started_at
            ? (int) $user->work_started_at->diffInMinutes(now())
            : 0;

        return response()->json([
            'message'      => 'Day ended. Good work!',
            'ended_at'     => $user->work_ended_at,
            'is_working'   => false,
            'visits_today' => $visitsToday,
            'duration_min' => $duration,
        ]);
    }

    public function status(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'is_working'      => (bool) $user->is_working,
            'work_started_at' => $user->work_started_at,
            'work_ended_at'   => $user->work_ended_at,
            'visits_today'    => $user->visits()->whereDate('checkin_at', today())->count(),
        ]);
    }
}
