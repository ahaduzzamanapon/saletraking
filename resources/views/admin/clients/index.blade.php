@extends('layouts.admin')
@section('title', 'Clients')
@section('page-title', '🏢 Clients')

@section('content')
<div class="page-header">
    <div><h1>Client Database</h1><p>All registered clients with geofence settings</p></div>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">+ Add Client</a>
</div>

<form method="GET" class="filter-bar">
    <div class="form-group">
        <label>Search</label>
        <input type="text" name="search" class="form-control" placeholder="Client name…" value="{{ request('search') }}">
    </div>
    <div class="form-group">
        <label>Territory</label>
        <select name="territory" class="form-control">
            <option value="">All Territories</option>
            @foreach($territories as $t)
                <option value="{{ $t->id }}" {{ request('territory') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline">Reset</a>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Territory</th>
                    <th>Assigned Rep</th>
                    <th>Category</th>
                    <th>Contact</th>
                    <th>Geofence</th>
                    <th>Total Visits</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $client->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $client->address }}</div>
                    </td>
                    <td class="text-muted">{{ $client->territory?->name ?? '—' }}</td>
                    <td class="text-muted">{{ $client->assignedTo?->name ?? '—' }}</td>
                    <td><span class="badge badge-purple">{{ $client->category ?? '—' }}</span></td>
                    <td>
                        <div style="font-size:12px">{{ $client->contact_name }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $client->contact_phone }}</div>
                    </td>
                    <td class="text-muted">{{ $client->geofence_radius }}m</td>
                    <td><span class="badge badge-blue">{{ $client->visits_count }}</span></td>
                    <td>
                        @if($client->is_active) <span class="badge badge-green">Active</span>
                        @else <span class="badge badge-red">Inactive</span> @endif
                    </td>
                    <td><a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline btn-sm">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:48px;color:var(--text-muted)">No clients found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
