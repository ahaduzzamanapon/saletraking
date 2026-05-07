@extends('layouts.admin')
@section('title', 'Feedback Detail')
@section('page-title', '📋 Feedback Detail')

@section('content')
<div class="page-header">
    <div>
        <h1>Feedback #{{ $feedback->id }}</h1>
        <p>{{ $feedback->salesperson->name }} · {{ $feedback->visit->client->name ?? '—' }}</p>
    </div>
    <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="detail-grid">
    <div class="card">
        <div class="card-header"><h2>Form Responses</h2></div>
        <div class="card-body">
            @foreach($feedback->data ?? [] as $key => $value)
            <div class="detail-row">
                <div class="detail-label">{{ str_replace('_', ' ', ucfirst($key)) }}</div>
                <div class="detail-value">{{ $value }}</div>
            </div>
            @endforeach
            @if($feedback->satisfaction_rating)
            <div class="detail-row">
                <div class="detail-label">Satisfaction Rating</div>
                <div class="detail-value"><span class="stars">{{ str_repeat('★', $feedback->satisfaction_rating) }}{{ str_repeat('☆', 5 - $feedback->satisfaction_rating) }}</span></div>
            </div>
            @endif
            @if($feedback->competitor_activity)
            <div class="detail-row">
                <div class="detail-label">Competitor Activity</div>
                <div class="detail-value" style="color:var(--amber)">⚠ {{ $feedback->competitor_activity }}</div>
            </div>
            @endif
            @if($feedback->order_value)
            <div class="detail-row">
                <div class="detail-label">Order Value</div>
                <div class="detail-value text-green fw-600">BDT {{ number_format($feedback->order_value) }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>Context</h2></div>
        <div class="card-body">
            <div class="detail-row"><div class="detail-label">Salesperson</div>
                <div class="detail-value"><a href="{{ route('admin.salespersons.show', $feedback->salesperson) }}" style="color:var(--accent-light)">{{ $feedback->salesperson->name }}</a></div></div>
            <div class="detail-row"><div class="detail-label">Client</div>
                <div class="detail-value">{{ $feedback->visit->client->name ?? '—' }}</div></div>
            <div class="detail-row"><div class="detail-label">Related Visit</div>
                <div class="detail-value"><a href="{{ route('admin.visits.show', $feedback->visit) }}" style="color:var(--accent-light)">View Visit #{{ $feedback->visit_id }}</a></div></div>
            <div class="detail-row"><div class="detail-label">Form Template</div>
                <div class="detail-value">{{ $feedback->template->name ?? 'Default Form' }}</div></div>
            <div class="detail-row"><div class="detail-label">Submitted At</div>
                <div class="detail-value">{{ $feedback->created_at->format('d M Y, H:i') }}</div></div>
        </div>
    </div>
</div>
@endsection
