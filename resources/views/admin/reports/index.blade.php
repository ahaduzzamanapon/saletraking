@extends('layouts.admin')
@section('title', 'Reports')
@section('page-title', '📊 Reports')

@section('content')
<div class="page-header">
    <div><h1>Performance Reports</h1><p>Daily summaries and monthly leaderboard</p></div>
</div>

<form method="GET" class="filter-bar" style="margin-bottom:24px">
    <div class="form-group">
        <label>Report Date</label>
        <input type="date" name="date" class="form-control" value="{{ $date->toDateString() }}">
    </div>
    <div class="form-group">
        <label>Month (Leaderboard)</label>
        <input type="month" name="month" class="form-control" value="{{ $month }}">
    </div>
    <button type="submit" class="btn btn-primary">Generate</button>
</form>

<!-- Daily Report -->
<div class="card" style="margin-bottom:24px">
    <div class="card-header">
        <h2>📅 Daily Report — {{ $date->format('d F Y') }}</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Salesperson</th>
                    <th>Visits Done</th>
                    <th>Target</th>
                    <th>Achievement</th>
                    <th>Revenue (BDT)</th>
                    <th>Sales Target</th>
                    <th>Avg Rating</th>
                    <th>Last Active</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailyReps as $rep)
                <tr>
                    <td style="font-weight:600">{{ $rep['name'] }}<div style="font-size:11px;color:var(--text-muted)">{{ $rep['employee_id'] }}</div></td>
                    <td><span class="badge {{ $rep['visits_done'] > 0 ? 'badge-blue' : 'badge-gray' }}">{{ $rep['visits_done'] }}</span></td>
                    <td class="text-muted">{{ $rep['visits_target'] ?: '—' }}</td>
                    <td>
                        @if($rep['visits_target'] > 0)
                            @php $pct = min(100, round($rep['visits_done'] / $rep['visits_target'] * 100)); @endphp
                            <div class="progress-wrap" style="min-width:80px">
                                <div class="progress-bar {{ $pct >= 100 ? 'green' : ($pct >= 60 ? '' : 'amber') }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <div style="font-size:11px;color:var(--text-muted)">{{ $pct }}%</div>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="{{ $rep['sales_achieved'] > 0 ? 'text-green fw-600' : 'text-muted' }}">
                        {{ $rep['sales_achieved'] > 0 ? number_format($rep['sales_achieved']) : '—' }}
                    </td>
                    <td class="text-muted">{{ $rep['sales_target'] ? number_format($rep['sales_target']) : '—' }}</td>
                    <td>
                        @if($rep['avg_rating'])
                            <span class="stars" style="font-size:12px">{{ str_repeat('★', round($rep['avg_rating'])) }}</span>
                            <span class="text-muted" style="font-size:11px"> {{ $rep['avg_rating'] }}</span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="text-muted">{{ $rep['last_seen'] ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:48px;color:var(--text-muted)">No data for this date</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Leaderboard -->
<div class="card">
    <div class="card-header">
        <h2>🏆 Monthly Leaderboard — {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Salesperson</th>
                    <th>Completed Visits</th>
                    <th>Total Revenue (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaderboard as $i => $rep)
                <tr>
                    <td>
                        @if($i === 0) <span style="font-size:20px">🥇</span>
                        @elseif($i === 1) <span style="font-size:20px">🥈</span>
                        @elseif($i === 2) <span style="font-size:20px">🥉</span>
                        @else <span class="text-muted fw-600">#{{ $i + 1 }}</span> @endif
                    </td>
                    <td style="font-weight:600">{{ $rep->name }}</td>
                    <td><span class="badge badge-blue">{{ $rep->visits_count }}</span></td>
                    <td class="{{ $rep->sales_sum > 0 ? 'text-green fw-600' : 'text-muted' }}">
                        {{ $rep->sales_sum ? 'BDT '.number_format($rep->sales_sum) : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:48px;color:var(--text-muted)">No data this month</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
