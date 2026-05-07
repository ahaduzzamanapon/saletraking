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
}
