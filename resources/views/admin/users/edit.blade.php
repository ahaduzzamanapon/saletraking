@extends('layouts.admin')
@section('title', 'Edit User')
@section('page-title', '👥 Edit User')

@section('content')
<div class="page-header">
    <div><h1>Edit: {{ $user->name }}</h1></div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">← Cancel</a>
</div>

<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="form-group">
                    <label>New Password <span class="text-muted">(leave blank to keep)</span></label>
                    <input type="password" name="password" class="form-control" minlength="8">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" class="form-control" required>
                        <option value="salesperson" {{ old('role', $user->role) === 'salesperson' ? 'selected' : '' }}>Salesperson</option>
                        <option value="manager"     {{ old('role', $user->role) === 'manager'     ? 'selected' : '' }}>Manager</option>
                        <option value="analyst"     {{ old('role', $user->role) === 'analyst'     ? 'selected' : '' }}>Analyst</option>
                        <option value="superadmin"  {{ old('role', $user->role) === 'superadmin'  ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="form-group">
                    <label>Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $user->employee_id) }}">
                </div>
                <div class="form-group">
                    <label>Territory</label>
                    <select name="territory_id" class="form-control">
                        <option value="">— None —</option>
                        @foreach($territories as $t)
                            <option value="{{ $t->id }}" {{ old('territory_id', $user->territory_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-top:20px;display:flex;gap:10px">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
