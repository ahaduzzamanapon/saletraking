@extends('layouts.admin')
@section('title', 'Add User')
@section('page-title', '👥 Add User')

@section('content')
<div class="page-header">
    <div><h1>Add New User</h1></div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">← Cancel</a>
</div>

<div class="card" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" class="form-control" required>
                        <option value="salesperson" {{ old('role') === 'salesperson' ? 'selected' : '' }}>Salesperson</option>
                        <option value="manager"     {{ old('role') === 'manager'     ? 'selected' : '' }}>Manager</option>
                        <option value="analyst"     {{ old('role') === 'analyst'     ? 'selected' : '' }}>Analyst</option>
                        <option value="superadmin"  {{ old('role') === 'superadmin'  ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label>Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}" placeholder="EMP005">
                </div>
                <div class="form-group">
                    <label>Territory</label>
                    <select name="territory_id" class="form-control">
                        <option value="">— None —</option>
                        @foreach($territories as $t)
                            <option value="{{ $t->id }}" {{ old('territory_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-top:20px;display:flex;gap:10px">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
