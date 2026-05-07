@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', '👥 User Management')

@section('content')
<div class="page-header">
    <div><h1>Users</h1><p>All platform users across all roles</p></div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add User</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Employee ID</th>
                    <th>Territory</th>
                    <th>Status</th>
                    <th>Last Seen</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="font-weight:600">{{ $user->name }}</td>
                    <td class="text-muted">{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ match($user->role) {
                            'superadmin' => 'badge-purple',
                            'manager'    => 'badge-blue',
                            'salesperson'=> 'badge-green',
                            'analyst'    => 'badge-amber',
                            default      => 'badge-gray'
                        } }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td class="text-muted">{{ $user->employee_id ?? '—' }}</td>
                    <td class="text-muted">{{ $user->territory?->name ?? '—' }}</td>
                    <td>
                        @if($user->is_active) <span class="badge badge-green">Active</span>
                        @else <span class="badge badge-red">Inactive</span> @endif
                    </td>
                    <td class="text-muted">{{ $user->last_seen_at?->diffForHumans() ?? 'Never' }}</td>
                    <td><a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline btn-sm">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:48px;color:var(--text-muted)">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
