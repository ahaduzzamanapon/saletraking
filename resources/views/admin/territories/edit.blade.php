@extends('layouts.admin')
@section('title', 'Edit Territory')
@section('page-title', '🗾 Edit Territory')

@section('content')
<div class="page-header">
    <div><h1>Edit: {{ $territory->name }}</h1></div>
    <a href="{{ route('admin.territories.index') }}" class="btn btn-outline">← Cancel</a>
</div>

<div class="card" style="max-width:560px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.territories.update', $territory) }}">
            @csrf @method('PUT')
            <div class="form-group" style="margin-bottom:16px">
                <label>Territory Name *</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $territory->name) }}" required>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $territory->description) }}</textarea>
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label>Assigned Manager</label>
                <select name="manager_id" class="form-control">
                    <option value="">— No manager —</option>
                    @foreach($managers as $m)
                        <option value="{{ $m->id }}" {{ old('manager_id', $territory->manager_id) == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-primary">Update Territory</button>
                <a href="{{ route('admin.territories.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
