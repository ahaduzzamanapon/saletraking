<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('territory:id,name')->orderBy('name')->paginate(25);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $territories = Territory::orderBy('name')->get();
        return view('admin.users.create', compact('territories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users',
            'password'     => 'required|min:8|confirmed',
            'role'         => 'required|in:superadmin,manager,salesperson,analyst',
            'phone'        => 'nullable|string|max:20',
            'employee_id'  => 'nullable|string|max:50|unique:users',
            'territory_id' => 'nullable|exists:territories,id',
        ]);

        User::create(array_merge($data, ['password' => Hash::make($data['password'])]));
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $territories = Territory::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'territories'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,'.$user->id,
            'role'         => 'required|in:superadmin,manager,salesperson,analyst',
            'phone'        => 'nullable|string|max:20',
            'employee_id'  => 'nullable|string|max:50|unique:users,employee_id,'.$user->id,
            'territory_id' => 'nullable|exists:territories,id',
            'is_active'    => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }
}
