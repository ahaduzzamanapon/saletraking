<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Target;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $visits = Visit::where('salesperson_id', $request->user()->id)
            ->with('client:id,name,address')
            ->orderByDesc('checkin_at')
            ->paginate(20);

        return response()->json($visits);
    }

    public function checkin(Request $request)
    {
        $data = $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'lat'            => 'required|numeric',
            'lng'            => 'required|numeric',
            'checkin_method' => 'in:auto,manual',
        ]);

        $user = $request->user();

        // Prevent double check-in
        $active = Visit::where('salesperson_id', $user->id)->where('status', 'in_progress')->first();
        if ($active) {
            return response()->json(['message' => 'You already have an active visit. Check out first.'], 422);
        }

        $visit = Visit::create([
            'salesperson_id' => $user->id,
            'client_id'      => $data['client_id'],
            'checkin_at'     => now(),
            'checkin_lat'    => $data['lat'],
            'checkin_lng'    => $data['lng'],
            'checkin_method' => $data['checkin_method'] ?? 'manual',
            'status'         => 'in_progress',
        ]);

        // Mark schedule as completed if it exists
        Schedule::where('salesperson_id', $user->id)
            ->where('client_id', $data['client_id'])
            ->whereDate('scheduled_at', today())
            ->where('status', 'pending')
            ->update(['status' => 'completed', 'visit_id' => $visit->id]);

        return response()->json(['visit' => $visit->load('client:id,name,address')], 201);
    }

    public function checkout(Request $request, Visit $visit)
    {
        if ($visit->salesperson_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($visit->status !== 'in_progress') {
            return response()->json(['message' => 'Visit is not in progress'], 422);
        }

        $data = $request->validate([
            'outcome'     => 'nullable|in:order_placed,follow_up,not_interested,demo_requested,other',
            'rating'      => 'nullable|integer|between:1,5',
            'order_value' => 'nullable|numeric',
            'notes'       => 'nullable|string|max:1000',
            'contact_met' => 'nullable|string|max:100',
        ]);

        $visit->update(array_merge($data, ['checkout_at' => now(), 'status' => 'completed']));

        // Update daily target achieved
        $target = Target::where('user_id', $visit->salesperson_id)
            ->where('type', 'daily')
            ->whereDate('period_date', today())
            ->first();

        if ($target) {
            $target->increment('visits_achieved');
            if (!empty($data['order_value'])) {
                $target->increment('sales_achieved', $data['order_value']);
            }
        }

        return response()->json(['visit' => $visit->fresh()->load('client:id,name,address')]);
    }

    public function show(Visit $visit)
    {
        if ($visit->salesperson_id !== request()->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json(['visit' => $visit->load('client', 'feedback')]);
    }

    public function todaySchedule(Request $request)
    {
        $schedules = Schedule::where('salesperson_id', $request->user()->id)
            ->whereDate('scheduled_at', today())
            ->with('client:id,name,address,lat,lng,contact_name,contact_phone,geofence_radius')
            ->orderBy('sort_order')
            ->get();

        return response()->json(['schedules' => $schedules]);
    }

    /**
     * Simple one-tap visit log (no check-in/check-out, just a point-in-time record).
     * Salesperson name-drops a client + location while GPS is tracking.
     */
    public function log(Request $request)
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'lat'         => 'required|numeric',
            'lng'         => 'required|numeric',
            'outcome'     => 'nullable|in:order_placed,follow_up,not_interested,demo_requested,other',
            'order_value' => 'nullable|numeric',
            'notes'       => 'nullable|string|max:1000',
            'rating'      => 'nullable|integer|between:1,5',
        ]);

        $user = $request->user();

        // Find or create client by name (under this rep)
        $client = \App\Models\Client::firstOrCreate(
            ['name' => $data['client_name'], 'assigned_to' => $user->id],
            ['address' => '', 'lat' => $data['lat'], 'lng' => $data['lng']]
        );

        $visit = Visit::create([
            'salesperson_id' => $user->id,
            'client_id'      => $client->id,
            'checkin_at'     => now(),
            'checkout_at'    => now(),
            'checkin_lat'    => $data['lat'],
            'checkin_lng'    => $data['lng'],
            'checkin_method' => 'auto',
            'status'         => 'completed',
            'outcome'        => $data['outcome'] ?? null,
            'order_value'    => $data['order_value'] ?? null,
            'notes'          => $data['notes'] ?? null,
            'rating'         => $data['rating'] ?? null,
        ]);

        // Update daily target
        $target = Target::where('user_id', $user->id)
            ->where('type', 'daily')
            ->whereDate('period_date', today())
            ->first();

        if ($target) {
            $target->increment('visits_achieved');
            if (!empty($data['order_value'])) {
                $target->increment('sales_achieved', $data['order_value']);
            }
        }

        return response()->json(['visit' => $visit->load('client:id,name,address')], 201);
    }
}
