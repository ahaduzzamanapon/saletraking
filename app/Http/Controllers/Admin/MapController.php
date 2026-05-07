<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        return view('admin.map');
    }

    /**
     * Polled every 30s — returns all reps' latest positions + today's visit count.
     */
    public function liveData()
    {
        $reps = User::where('role', 'salesperson')
            ->where('is_active', true)
            ->with(['latestLocation', 'territory:id,name'])
            ->withCount(['visits as visits_today' => fn($q) => $q->whereDate('checkin_at', today())])
            ->get()
            ->map(function ($rep) {
                $loc = $rep->latestLocation;
                return [
                    'id'           => $rep->id,
                    'name'         => $rep->name,
                    'employee_id'  => $rep->employee_id,
                    'territory'    => $rep->territory?->name,
                    'visits_today' => $rep->visits_today,
                    'last_seen'    => $rep->last_seen_at?->diffForHumans(),
                    'is_online'    => $rep->last_seen_at && $rep->last_seen_at->gte(now()->subMinutes(5)),
                    'lat'          => $loc?->lat,
                    'lng'          => $loc?->lng,
                    'battery'      => $loc?->battery_level,
                ];
            });

        return response()->json(['reps' => $reps]);
    }

    /**
     * Full route + visits for one rep on a given date (used by map on rep click).
     */
    public function repRouteData(Request $request, User $rep)
    {
        $date = $request->date('date', 'Y-m-d') ?? today();

        // GPS trail
        $points = Location::where('user_id', $rep->id)
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at')
            ->get(['lat', 'lng', 'recorded_at', 'battery_level', 'speed']);

        // Today's visits with client coords
        $visits = Visit::where('salesperson_id', $rep->id)
            ->whereDate('checkin_at', $date)
            ->with('client:id,name,address,lat,lng')
            ->orderBy('checkin_at')
            ->get()
            ->map(fn($v) => [
                'id'          => $v->id,
                'client_name' => $v->client->name,
                'address'     => $v->client->address,
                'lat'         => $v->checkin_lat ?? $v->client->lat,
                'lng'         => $v->checkin_lng ?? $v->client->lng,
                'checkin'     => $v->checkin_at?->format('H:i'),
                'checkout'    => $v->checkout_at?->format('H:i'),
                'duration'    => $v->durationMinutes(),
                'status'      => $v->status,
                'outcome'     => $v->outcome ? str_replace('_', ' ', ucfirst($v->outcome)) : null,
                'rating'      => $v->rating,
                'order_value' => $v->order_value,
                'notes'       => $v->notes,
                'url'         => route('admin.visits.show', $v->id),
            ]);

        return response()->json([
            'rep'    => $rep->only('id', 'name', 'employee_id'),
            'points' => $points,
            'visits' => $visits,
        ]);
    }
}
