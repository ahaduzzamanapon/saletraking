<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Target;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function my(Request $request)
    {
        $user = $request->user();

        $daily = Target::where('user_id', $user->id)
            ->where('type', 'daily')
            ->whereDate('period_date', today())
            ->first();

        $monthly = Target::where('user_id', $user->id)
            ->where('type', 'monthly')
            ->whereDate('period_date', today()->startOfMonth())
            ->first();

        return response()->json([
            'daily'   => $daily,
            'monthly' => $monthly,
        ]);
    }
}
