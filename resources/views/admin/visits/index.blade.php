@extends('layouts.admin')
@section('title', 'Visits')
@section('page-title', '🤝 Visits')

@section('content')
<div class="page-header">
    <div><h1>Client Visits</h1><p>All field visit records with filters</p></div>
</div>

<form method="GET" class="filter-bar">
    <div class="form-group">
        <label>Date</label>
        <input type="date" name="date" class="form-control" value="{{ request('date', today()->toDateString()) }}">
    </div>
    <div class="form-group">
        <label>Salesperson</label>
        <select name="rep" class="form-control">
            <option value="">All Reps</option>
            @foreach($reps as $rep)
                <option value="{{ $rep->id }}" {{ request('rep') == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="">All</option>
            <option value="completed"  {{ request('status') === 'completed'  ? 'selected' : '' }}>Completed</option>
            <option value="in_progress"{{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="missed"     {{ request('status') === 'missed'      ? 'selected' : '' }}>Missed</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="{{ route('admin.visits.index') }}" class="btn btn-outline">Reset</a>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Salesperson</th>
                    <th>Client</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Duration</th>
                    <th>Outcome</th>
                    <th>Rating</th>
                    <th>Order (BDT)</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $visit)
                <tr>
                    <td style="font-weight:500">{{ $visit->salesperson->name }}</td>
                    <td class="text-muted">{{ $visit->client->name }}</td>
                    <td class="text-muted">{{ $visit->checkin_at?->format('H:i') }}</td>
                    <td class="text-muted">{{ $visit->checkout_at?->format('H:i') ?? '—' }}</td>
                    <td class="text-muted">{{ $visit->durationMinutes() ? $visit->durationMinutes().'m' : '—' }}</td>
                    <td>
                        @if($visit->outcome)
                            <span class="badge {{ match($visit->outcome) {
                                'order_placed' => 'badge-green',
                                'follow_up' => 'badge-blue',
                                'not_interested' => 'badge-red',
                                'demo_requested' => 'badge-purple',
                                default => 'badge-gray'
                            } }}">{{ str_replace('_', ' ', ucfirst($visit->outcome)) }}</span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td>
                        @if($visit->rating)
                            <span class="stars" style="font-size:12px">{{ str_repeat('★', $visit->rating) }}</span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td>{{ $visit->order_value ? number_format($visit->order_value) : '—' }}</td>
                    <td>
                        @if($visit->status === 'completed') <span class="badge badge-green">Done</span>
                        @elseif($visit->status === 'in_progress') <span class="badge badge-blue">Active</span>
                        @else <span class="badge badge-red">Missed</span> @endif
                    </td>
                    <td><a href="{{ route('admin.visits.show', $visit) }}" class="btn btn-outline btn-sm">View</a></td>
                </tr>
                @empty
                <tr><td colspan="10" style="text-align:center;padding:48px;color:var(--text-muted)">No visits found for this filter</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($visits->hasPages())
    <div class="pagination">
        {{ $visits->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>
@endsection
