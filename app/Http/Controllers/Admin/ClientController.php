<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['territory:id,name', 'assignedTo:id,name'])
            ->withCount('visits');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('territory')) {
            $query->where('territory_id', $request->territory);
        }

        $clients     = $query->orderBy('name')->paginate(20)->withQueryString();
        $territories = Territory::orderBy('name')->get();

        return view('admin.clients.index', compact('clients', 'territories'));
    }

    public function create()
    {
        $territories  = Territory::orderBy('name')->get();
        $salespersons = User::where('role', 'salesperson')->where('is_active', true)->orderBy('name')->get();
        return view('admin.clients.create', compact('territories', 'salespersons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string',
            'lat'             => 'nullable|numeric',
            'lng'             => 'nullable|numeric',
            'contact_name'    => 'nullable|string|max:100',
            'contact_phone'   => 'nullable|string|max:20',
            'contact_email'   => 'nullable|email',
            'geofence_radius' => 'nullable|integer|min:50|max:1000',
            'category'        => 'nullable|string|max:100',
            'territory_id'    => 'nullable|exists:territories,id',
            'assigned_to'     => 'nullable|exists:users,id',
            'notes'           => 'nullable|string',
        ]);

        Client::create($data);
        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    public function edit(Client $client)
    {
        $territories  = Territory::orderBy('name')->get();
        $salespersons = User::where('role', 'salesperson')->where('is_active', true)->orderBy('name')->get();
        return view('admin.clients.edit', compact('client', 'territories', 'salespersons'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string',
            'lat'             => 'nullable|numeric',
            'lng'             => 'nullable|numeric',
            'contact_name'    => 'nullable|string|max:100',
            'contact_phone'   => 'nullable|string|max:20',
            'contact_email'   => 'nullable|email',
            'geofence_radius' => 'nullable|integer|min:50|max:1000',
            'category'        => 'nullable|string|max:100',
            'territory_id'    => 'nullable|exists:territories,id',
            'assigned_to'     => 'nullable|exists:users,id',
            'notes'           => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $client->update($data);
        return redirect()->route('admin.clients.index')->with('success', 'Client updated.');
    }
}
