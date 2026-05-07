@extends('layouts.admin')
@section('title', 'Territories')
@section('page-title', '🗾 Territories')

@section('content')
<div class="page-header">
    <div><h1>Territories</h1><p>Define sales regions and assign managers</p></div>
    <a href="{{ route('admin.territories.create') }}" class="btn btn-primary">+ Add Territory</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Territory Name</th>
                    <th>Description</th>
                    <th>Manager</th>
                    <th>Reps</th>
                    <th>Clients</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($territories as $territory)
                <tr>
                    <td style="font-weight:600">{{ $territory->name }}</td>
                    <td class="text-muted">{{ $territory->description ?? '—' }}</td>
                    <td>
                        @if($territory->manager)
                            <span class="badge badge-blue">{{ $territory->manager->name }}</span>
                        @else
                            <span class="text-muted">Unassigned</span>
                        @endif
                    </td>
                    <td><span class="badge badge-purple">{{ $territory->users_count }} reps</span></td>
                    <td><span class="badge badge-green">{{ $territory->clients_count }} clients</span></td>
                    <td style="display:flex;gap:8px">
                        <a href="{{ route('admin.territories.edit', $territory) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.territories.destroy', $territory) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                data-confirm="Delete '{{ $territory->name }}'? Reps and clients will be unassigned.">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:var(--text-muted)">
                        No territories yet. <a href="{{ route('admin.territories.create') }}" style="color:var(--accent-light)">Create one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
