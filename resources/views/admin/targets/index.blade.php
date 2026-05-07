@extends('layouts.admin')
@section('title', 'Targets')
@section('page-title', '🎯 Targets')

@section('content')
<div class="page-header">
    <div><h1>Sales Targets</h1><p>Set and track daily goals per salesperson</p></div>
</div>

<div class="detail-grid">
    <!-- Set Target Form -->
    <div class="card" style="align-self:start">
        <div class="card-header"><h2>Set New Target</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.targets.store') }}">
                @csrf
                <div class="form-group" style="margin-bottom:14px">
                    <label>Salesperson *</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">Select rep…</option>
                        @foreach($reps as $rep)
                            <option value="{{ $rep->id }}">{{ $rep->name }} ({{ $rep->employee_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:14px">
                    <label>Type *</label>
                    <select name="type" class="form-control" required>
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:14px">
                    <label>Period Date *</label>
                    <input type="date" name="period_date" class="form-control" value="{{ today()->toDateString() }}" required>
                </div>
                <div class="form-group" style="margin-bottom:14px">
                    <label>Visits Target</label>
                    <input type="number" name="visits_target" class="form-control" value="6" min="0">
                </div>
                <div class="form-group" style="margin-bottom:14px">
                    <label>Sales Target (BDT)</label>
                    <input type="number" name="sales_target" class="form-control" value="100000" min="0">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">Set Target</button>
            </form>
        </div>
    </div>

    <!-- Today's Progress -->
    <div class="card">
        <div class="card-header"><h2>Today's Progress — {{ today()->format('d M Y') }}</h2></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Salesperson</th>
                        <th>Visits Done / Target</th>
                        <th>Visits %</th>
                        <th>Sales Done / Target</th>
                        <th>Sales %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reps as $rep)
                    @php $t = $rep->targets->first(); @endphp
                    <tr>
                        <td style="font-weight:500">{{ $rep->name }}</td>
                        @if($t)
                        <td>{{ $t->visits_achieved }} / {{ $t->visits_target }}</td>
                        <td style="min-width:120px">
                            <div class="progress-wrap">
                                <div class="progress-bar {{ $t->visitsProgress() >= 100 ? 'green' : ($t->visitsProgress() >= 60 ? '' : 'amber') }}"
                                     style="width:{{ $t->visitsProgress() }}%"></div>
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ $t->visitsProgress() }}%</div>
                        </td>
                        <td>{{ number_format($t->sales_achieved) }} / {{ number_format($t->sales_target) }}</td>
                        <td style="min-width:120px">
                            <div class="progress-wrap">
                                <div class="progress-bar {{ $t->salesProgress() >= 100 ? 'green' : ($t->salesProgress() >= 60 ? '' : 'amber') }}"
                                     style="width:{{ $t->salesProgress() }}%"></div>
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ $t->salesProgress() }}%</div>
                        </td>
                        @else
                        <td colspan="4" class="text-muted" style="font-style:italic">No target set for today</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
