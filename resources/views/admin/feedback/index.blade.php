@extends('layouts.admin')
@section('title', 'Feedback')
@section('page-title', '📋 Feedback')

@section('content')
<div class="page-header">
    <div><h1>Post-Visit Feedback</h1><p>Market intelligence collected from the field</p></div>
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
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline">Reset</a>
</form>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Salesperson</th>
                    <th>Client</th>
                    <th>Outcome</th>
                    <th>Satisfaction</th>
                    <th>Order Value</th>
                    <th>Competitor</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbacks as $fb)
                <tr>
                    <td style="font-weight:500">{{ $fb->salesperson->name }}</td>
                    <td class="text-muted">{{ $fb->visit->client->name ?? '—' }}</td>
                    <td>
                        @php $outcome = $fb->data['outcome'] ?? null; @endphp
                        @if($outcome)
                            <span class="badge {{ str_contains(strtolower($outcome),'order') ? 'badge-green' : (str_contains(strtolower($outcome),'follow') ? 'badge-blue' : 'badge-gray') }}">
                                {{ $outcome }}
                            </span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td>
                        @if($fb->satisfaction_rating)
                            <span class="stars" style="font-size:12px">{{ str_repeat('★', $fb->satisfaction_rating) }}</span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td>{{ $fb->order_value ? 'BDT '.number_format($fb->order_value) : '—' }}</td>
                    <td>
                        @if($fb->competitor_activity)
                            <span class="badge badge-amber" title="{{ $fb->competitor_activity }}">⚠ Yes</span>
                        @else <span class="text-muted">No</span> @endif
                    </td>
                    <td class="text-muted">{{ $fb->created_at->format('H:i') }}</td>
                    <td><a href="{{ route('admin.feedback.show', $fb) }}" class="btn btn-outline btn-sm">View</a></td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:48px;color:var(--text-muted)">No feedback found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
