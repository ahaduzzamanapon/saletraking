@extends('layouts.admin')
@section('title', 'Salespersons')
@section('page-title', '🚶 Salespersons')

@section('content')
<div class="page-header">
    <div>
        <h1>Salespersons</h1>
        <p>All active field representatives and their today's status</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add Salesperson</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Territory</th>
                    <th>Status</th>
                    <th>Visits Today</th>
                    <th>Total Visits</th>
                    <th>Last Seen</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reps as $rep)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $rep->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $rep->employee_id }} · {{ $rep->phone }}</div>
                    </td>
                    <td class="text-muted">{{ $rep->territory?->name ?? '—' }}</td>
                    <td>
                        @if($rep->last_seen_at && $rep->last_seen_at->gte(now()->subMinutes(5)))
                            <span class="badge badge-green">● Online</span>
                        @elseif($rep->last_seen_at && $rep->last_seen_at->gte(now()->subHour()))
                            <span class="badge badge-amber">◐ Recent</span>
                        @else
                            <span class="badge badge-gray">○ Offline</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $rep->visits_today > 0 ? 'badge-blue' : 'badge-gray' }}">
                            {{ $rep->visits_today }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $rep->visits_total }}</td>
                    <td class="text-muted">{{ $rep->last_seen_at?->diffForHumans() ?? 'Never' }}</td>
                    <td>
                        <a href="{{ route('admin.salespersons.show', $rep) }}" class="btn btn-outline btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:48px;color:var(--text-muted)">No salespersons found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
