<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Mobile app sends GPS ping every 30 seconds.
     */
    public function ping(Request $request)
    {
        $data = $request->validate([
            'lat'           => 'required|numeric|between:-90,90',
            'lng'           => 'required|numeric|between:-180,180',
            'accuracy'      => 'nullable|numeric',
            'speed'         => 'nullable|numeric',
            'battery_level' => 'nullable|integer|between:0,100',
            'recorded_at'   => 'nullable|date',
        ]);

        $user = $request->user();
        $user->update(['last_seen_at' => now()]);

        Location::create([
            'user_id'       => $user->id,
            'lat'           => $data['lat'],
            'lng'           => $data['lng'],
            'accuracy'      => $data['accuracy'] ?? null,
            'speed'         => $data['speed'] ?? null,
            'battery_level' => $data['battery_level'] ?? null,
            'recorded_at'   => $data['recorded_at'] ?? now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Batch offline pings sync.
     */
    public function batchPing(Request $request)
    {
        $request->validate(['pings' => 'required|array|max:500', 'pings.*.lat' => 'required|numeric', 'pings.*.lng' => 'required|numeric']);
        $user = $request->user();
        $rows = [];
        foreach ($request->pings as $ping) {
            $rows[] = [
                'user_id'       => $user->id,
                'lat'           => $ping['lat'],
                'lng'           => $ping['lng'],
                'accuracy'      => $ping['accuracy'] ?? null,
                'speed'         => $ping['speed'] ?? null,
                'battery_level' => $ping['battery_level'] ?? null,
                'recorded_at'   => $ping['recorded_at'] ?? now(),
            ];
        }
        Location::insert($rows);
        return response()->json(['synced' => count($rows)]);
    }
}
