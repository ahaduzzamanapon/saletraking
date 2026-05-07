@extends('layouts.admin')
@section('title', 'Visit Detail')
@section('page-title', '🤝 Visit Detail')

@section('content')
<div class="page-header">
    <div>
        <h1>Visit #{{ $visit->id }}</h1>
        <p>{{ $visit->salesperson->name }} → {{ $visit->client->name }}</p>
    </div>
    <a href="{{ route('admin.visits.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="detail-grid">
    <div class="card">
        <div class="card-header"><h2>Visit Information</h2></div>
        <div class="card-body">
            <div class="detail-row"><div class="detail-label">Salesperson</div><div class="detail-value">{{ $visit->salesperson->name }}</div></div>
            <div class="detail-row"><div class="detail-label">Client</div><div class="detail-value">{{ $visit->client->name }}</div></div>
            <div class="detail-row"><div class="detail-label">Check-in</div><div class="detail-value">{{ $visit->checkin_at?->format('d M Y, H:i') ?? '—' }}</div></div>
            <div class="detail-row"><div class="detail-label">Check-out</div><div class="detail-value">{{ $visit->checkout_at?->format('d M Y, H:i') ?? '—' }}</div></div>
            <div class="detail-row"><div class="detail-label">Duration</div><div class="detail-value">{{ $visit->durationMinutes() ? $visit->durationMinutes().' min' : 'In progress' }}</div></div>
            <div class="detail-row"><div class="detail-label">Method</div><div class="detail-value"><span class="badge {{ $visit->checkin_method === 'auto' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($visit->checkin_method) }}</span></div></div>
            <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value">
                @if($visit->status === 'completed') <span class="badge badge-green">Completed</span>
                @elseif($visit->status === 'in_progress') <span class="badge badge-blue">In Progress</span>
                @else <span class="badge badge-red">Missed</span> @endif
            </div></div>
            <div class="detail-row"><div class="detail-label">GPS</div><div class="detail-value" style="font-family:monospace;font-size:12px">{{ $visit->checkin_lat }}, {{ $visit->checkin_lng }}</div></div>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:16px">
            <div class="card-header"><h2>Outcome</h2></div>
            <div class="card-body">
                <div class="detail-row"><div class="detail-label">Outcome</div><div class="detail-value">
                    @if($visit->outcome) <span class="badge badge-green">{{ str_replace('_',' ',ucfirst($visit->outcome)) }}</span>
                    @else <span class="text-muted">—</span> @endif
                </div></div>
                <div class="detail-row"><div class="detail-label">Rating</div><div class="detail-value">
                    @if($visit->rating) <span class="stars">{{ str_repeat('★',$visit->rating) }}{{ str_repeat('☆',5-$visit->rating) }}</span>
                    @else <span class="text-muted">—</span> @endif
                </div></div>
                <div class="detail-row"><div class="detail-label">Order Value</div><div class="detail-value">{{ $visit->order_value ? 'BDT '.number_format($visit->order_value) : '—' }}</div></div>
                <div class="detail-row"><div class="detail-label">Notes</div><div class="detail-value text-muted">{{ $visit->notes ?? 'No notes.' }}</div></div>
            </div>
        </div>

        @if($visit->feedback)
        <div class="card">
            <div class="card-header"><h2>📋 Feedback</h2><span class="badge badge-green">Submitted</span></div>
            <div class="card-body">
                @foreach($visit->feedback->data ?? [] as $key => $value)
                <div class="detail-row">
                    <div class="detail-label">{{ str_replace('_',' ',ucfirst($key)) }}</div>
                    <div class="detail-value">{{ $value }}</div>
                </div>
                @endforeach
                @if($visit->feedback->competitor_activity)
                <div class="detail-row"><div class="detail-label">Competitor Activity</div><div class="detail-value text-muted">{{ $visit->feedback->competitor_activity }}</div></div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
