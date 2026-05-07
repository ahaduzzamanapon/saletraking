@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ Auth::user()->name }}! 👋</h1>
        <p>Here's what's happening with your field team today — {{ now()->format('l, d F Y') }}</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card accent">
        <span class="kpi-icon">🚶</span>
        <div class="kpi-value">{{ $totalReps }}</div>
        <div class="kpi-label">Total Salespersons</div>
    </div>
    <div class="kpi-card green">
        <span class="kpi-icon">📡</span>
        <div class="kpi-value">{{ $activeToday }}</div>
        <div class="kpi-label">Active Right Now</div>
    </div>
    <div class="kpi-card blue">
        <span class="kpi-icon">🤝</span>
        <div class="kpi-value">{{ $visitsToday }}</div>
        <div class="kpi-label">Visits Today</div>
    </div>
    <div class="kpi-card green">
        <span class="kpi-icon">✓</span>
        <div class="kpi-value">{{ $completedToday }}</div>
        <div class="kpi-label">Completed</div>
    </div>
    <div class="kpi-card red">
        <span class="kpi-icon">✕</span>
        <div class="kpi-value">{{ $missedToday }}</div>
        <div class="kpi-label">Missed</div>
    </div>
    <div class="kpi-card amber">
        <span class="kpi-icon">💰</span>
        <div class="kpi-value">{{ number_format($revenueToday / 1000, 0) }}K</div>
        <div class="kpi-label">Revenue (BDT)</div>
    </div>
</div>

<div class="detail-grid">
    <!-- Today's Rep Performance -->
    <div class="card">
        <div class="card-header">
            <h2>🏆 Top Performers Today</h2>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline btn-sm">Full Report</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Salesperson</th>
                        <th>Visits</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topReps as $i => $rep)
                    <tr>
                        <td><span class="text-muted fw-600">{{ $i + 1 }}</span></td>
                        <td>
                            <a href="{{ route('admin.salespersons.show', $rep) }}" style="color:var(--text-primary);text-decoration:none;font-weight:500">
                                {{ $rep->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $rep->visits_today > 0 ? 'badge-green' : 'badge-gray' }}">
                                {{ $rep->visits_today }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:32px">No visits yet today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Visits -->
    <div class="card">
        <div class="card-header">
            <h2>🕐 Recent Visits</h2>
            <a href="{{ route('admin.visits.index') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rep</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentVisits as $visit)
                    <tr>
                        <td style="font-weight:500">{{ $visit->salesperson->name }}</td>
                        <td class="text-muted">{{ $visit->client->name }}</td>
                        <td>
                            @if($visit->status === 'completed')
                                <span class="badge badge-green">✓ Done</span>
                            @elseif($visit->status === 'in_progress')
                                <span class="badge badge-blue">⟳ Active</span>
                            @else
                                <span class="badge badge-red">✕ Missed</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $visit->checkin_at?->format('H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:32px">No visits today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="kpi-grid mt-24">
    <a href="{{ route('admin.map') }}" class="kpi-card" style="text-decoration:none;cursor:pointer">
        <span class="kpi-icon">🗺</span>
        <div class="kpi-value" style="font-size:18px;color:var(--accent-light)">Live Map</div>
        <div class="kpi-label">Track all reps in real time</div>
    </a>
    <a href="{{ route('admin.salespersons.index') }}" class="kpi-card" style="text-decoration:none;cursor:pointer">
        <span class="kpi-icon">🚶</span>
        <div class="kpi-value" style="font-size:18px;color:var(--blue)">Team</div>
        <div class="kpi-label">View all salespersons</div>
    </a>
    <a href="{{ route('admin.feedback.index') }}" class="kpi-card" style="text-decoration:none;cursor:pointer">
        <span class="kpi-icon">📋</span>
        <div class="kpi-value" style="font-size:18px;color:var(--green)">Feedback</div>
        <div class="kpi-label">Today's market intelligence</div>
    </a>
    <a href="{{ route('admin.targets.index') }}" class="kpi-card" style="text-decoration:none;cursor:pointer">
        <span class="kpi-icon">🎯</span>
        <div class="kpi-value" style="font-size:18px;color:var(--amber)">Targets</div>
        <div class="kpi-label">Set and review goals</div>
    </a>
</div>
@endsection
