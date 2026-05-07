@extends('layouts.admin')
@section('title', 'Add Client')
@section('page-title', '🏢 Add Client')

@section('content')
<div class="page-header">
    <div><h1>Add New Client</h1></div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline">← Cancel</a>
</div>

<div class="card" style="max-width:860px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.clients.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Client Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="Retail, Pharma, Food…">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Full address">
                </div>
                <div class="form-group">
                    <label>Latitude</label>
                    <input type="number" name="lat" class="form-control" step="any" value="{{ old('lat') }}" placeholder="23.7680">
                </div>
                <div class="form-group">
                    <label>Longitude</label>
                    <input type="number" name="lng" class="form-control" step="any" value="{{ old('lng') }}" placeholder="90.4125">
                </div>
                <div class="form-group">
                    <label>Geofence Radius (meters)</label>
                    <input type="number" name="geofence_radius" class="form-control" value="{{ old('geofence_radius', 100) }}" min="50" max="1000">
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
                <div class="form-group">
                    <label>Assigned Salesperson</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">— Unassigned —</option>
                        @foreach($salespersons as $rep)
                            <option value="{{ $rep->id }}" {{ old('assigned_to') == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Contact Name</label>
                    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}">
                </div>
                <div class="form-group">
                    <label>Contact Phone</label>
                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}">
                </div>
                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email') }}">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div style="margin-top:20px;display:flex;gap:10px">
                <button type="submit" class="btn btn-primary">Save Client</button>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
