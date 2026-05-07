<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Returns clients assigned to the authenticated salesperson.
     */
    public function index(Request $request)
    {
        $clients = Client::where('assigned_to', $request->user()->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'address', 'lat', 'lng', 'contact_name', 'contact_phone', 'geofence_radius', 'category']);

        return response()->json(['clients' => $clients]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string',
            'lat'             => 'required|numeric',
            'lng'             => 'required|numeric',
            'contact_name'    => 'nullable|string|max:100',
            'contact_phone'   => 'nullable|string|max:20',
            'category'        => 'nullable|string|max:100',
            'geofence_radius' => 'nullable|integer|min:50|max:1000',
        ]);

        $user = $request->user();

        $client = Client::create(array_merge($data, [
            'assigned_to'  => $user->id,
            'territory_id' => $user->territory_id,
            'is_active'    => true,
        ]));

        return response()->json(['message' => 'Client created successfully', 'client' => $client], 201);
    }
}
