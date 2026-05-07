<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Http\Request;

class TerritoryController extends Controller
{
    public function index()
    {
        $territories = Territory::with('manager:id,name')
            ->withCount(['users', 'clients'])
            ->orderBy('name')
            ->get();

        return view('admin.territories.index', compact('territories'));
    }

    public function create()
    {
        $managers = User::whereIn('role', ['manager', 'superadmin'])
            ->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('admin.territories.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:territories',
            'description' => 'nullable|string',
            'manager_id'  => 'nullable|exists:users,id',
        ]);

        Territory::create($data);
        return redirect()->route('admin.territories.index')->with('success', 'Territory created.');
    }

    public function edit(Territory $territory)
    {
        $managers = User::whereIn('role', ['manager', 'superadmin'])
            ->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('admin.territories.edit', compact('territory', 'managers'));
    }

    public function update(Request $request, Territory $territory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:territories,name,' . $territory->id,
            'description' => 'nullable|string',
            'manager_id'  => 'nullable|exists:users,id',
        ]);

        $territory->update($data);
        return redirect()->route('admin.territories.index')->with('success', 'Territory updated.');
    }

    public function destroy(Territory $territory)
    {
        $territory->delete();
        return redirect()->route('admin.territories.index')->with('success', 'Territory deleted.');
    }
}
