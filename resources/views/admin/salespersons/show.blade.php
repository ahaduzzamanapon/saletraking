@extends('layouts.admin')
@section('title', $salesperson->name)
@section('page-title', '🚶 ' . $salesperson->name)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $salesperson->name }}</h1>
        <p>{{ $salesperson->employee_id }} · {{ $salesperson->territory?->name ?? 'No Territory' }}</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('admin.map') }}" class="btn btn-outline">🗺 View on Map</a>
        <a href="{{ route('admin.users.edit', $salesperson) }}" class="btn btn-primary">Edit Profile</a>
    </div>
</div>

<!-- Status Row -->
<div class="kpi-grid" style="margin-bottom:24px">
    <div class="kpi-card blue">
        <span class="kpi-icon">🤝</span>
        <div class="kpi-value">{{ $visitsToday->count() }}</div>
        <div class="kpi-label">Visits Today</div>
    </div>
    <div class="kpi-card green">
        <span class="kpi-icon">🎯</span>
        <div class="kpi-value">{{ $dailyTarget?->visits_target ?? '—' }}</div>
        <div class="kpi-label">Daily Target</div>
    </div>
    <div class="kpi-card amber">
        <span class="kpi-icon">💰</span>
        <div class="kpi-value">{{ number_format($visitsToday->sum('order_value') / 1000, 0) }}K</div>
        <div class="kpi-label">Revenue Today (BDT)</div>
    </div>
    <div class="kpi-card {{ $salesperson->last_seen_at?->gte(now()->subMinutes(5)) ? 'green' : 'accent' }}">
        <span class="kpi-icon">📡</span>
        <div class="kpi-value" style="font-size:16px">{{ $salesperson->last_seen_at?->format('H:i') ?? '—' }}</div>
        <div class="kpi-label">Last Seen</div>
    </div>
</div>

@if($dailyTarget)
<div class="card" style="margin-bottom:24px">
    <div class="card-header"><h2>🎯 Today's Target Progress</h2></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                    <span class="text-muted">Visits</span>
                    <span class="fw-600">{{ $dailyTarget->visits_achieved }} / {{ $dailyTarget->visits_target }}</span>
                </div>
                <div class="progress-wrap">
                    <div class="progress-bar {{ $dailyTarget->visitsProgress() >= 100 ? 'green' : ($dailyTarget->visitsProgress() >= 60 ? '' : 'amber') }}"
                         style="width:{{ $dailyTarget->visitsProgress() }}%"></div>
                </div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:4px">{{ $dailyTarget->visitsProgress() }}%</div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                    <span class="text-muted">Sales (BDT)</span>
                    <span class="fw-600">{{ number_format($dailyTarget->sales_achieved) }} / {{ number_format($dailyTarget->sales_target) }}</span>
                </div>
                <div class="progress-wrap">
                    <div class="progress-bar {{ $dailyTarget->salesProgress() >= 100 ? 'green' : ($dailyTarget->salesProgress() >= 60 ? '' : 'amber') }}"
                         style="width:{{ $dailyTarget->salesProgress() }}%"></div>
                </div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:4px">{{ $dailyTarget->salesProgress() }}%</div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="detail-grid">
    <!-- Today's Visits -->
    <div class="card">
        <div class="card-header"><h2>📅 Today's Visits</h2></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Client</th><th>Check-in</th><th>Duration</th><th>Status</th><th>Rating</th></tr>
                </thead>
                <tbody>
                    @forelse($visitsToday as $visit)
                    <tr>
                        <td><a href="{{ route('admin.visits.show', $visit) }}" style="color:var(--text-primary);font-weight:500;text-decoration:none">{{ $visit->client->name }}</a></td>
                        <td class="text-muted">{{ $visit->checkin_at?->format('H:i') }}</td>
                        <td class="text-muted">
                            @if($visit->durationMinutes())
                                {{ $visit->durationMinutes() }}m
                            @else
                                <span class="badge badge-blue">Active</span>
                            @endif
                        </td>
                        <td>
                            @if($visit->status === 'completed') <span class="badge badge-green">Done</span>
                            @elseif($visit->status === 'in_progress') <span class="badge badge-blue">Active</span>
                            @else <span class="badge badge-red">Missed</span> @endif
                        </td>
                        <td>
                            @if($visit->rating)
                                <span class="stars">{{ str_repeat('★', $visit->rating) }}{{ str_repeat('☆', 5 - $visit->rating) }}</span>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted)">No visits today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Profile Info -->
    <div class="card">
        <div class="card-header"><h2>👤 Profile</h2></div>
        <div class="card-body">
            <div class="detail-row"><div class="detail-label">Full Name</div><div class="detail-value">{{ $salesperson->name }}</div></div>
            <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value">{{ $salesperson->email }}</div></div>
            <div class="detail-row"><div class="detail-label">Phone</div><div class="detail-value">{{ $salesperson->phone ?? '—' }}</div></div>
            <div class="detail-row"><div class="detail-label">Employee ID</div><div class="detail-value">{{ $salesperson->employee_id ?? '—' }}</div></div>
            <div class="detail-row"><div class="detail-label">Territory</div><div class="detail-value">{{ $salesperson->territory?->name ?? '—' }}</div></div>
            <div class="detail-row">
                <div class="detail-label">Account Status</div>
                <div class="detail-value">
                    @if($salesperson->is_active) <span class="badge badge-green">Active</span>
                    @else <span class="badge badge-red">Inactive</span> @endif
                </div>
            </div>
            @if($salesperson->latestLocation)
            <div class="detail-row">
                <div class="detail-label">Last GPS Position</div>
                <div class="detail-value" style="font-size:12px;font-family:monospace">
                    {{ $salesperson->latestLocation->lat }}, {{ $salesperson->latestLocation->lng }}
                    <br><span class="text-muted">{{ $salesperson->latestLocation->recorded_at?->diffForHumans() }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
