@extends('layouts.app')
@section('title', 'Clients')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Clients</h1>
        <p class="text-muted mb-0">{{ $clients->total() }} client(s) enregistré(s)</p>
    </div>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Nouveau client</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Nom, téléphone, email, CIN..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto"><button type="submit" class="btn btn-outline-primary">Filtrer</button></div>
            @if(request('search'))
            <div class="col-auto"><a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">Réinitialiser</a></div>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th class="text-center">Réservations</th>
                    <th class="text-end">Total dépensé</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:38px;height:38px;font-size:14px;flex-shrink:0;">
                                {{ strtoupper(substr($client->first_name,0,1).substr($client->last_name,0,1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $client->full_name }}</div>
                                @if($client->cin)<small class="text-muted">CIN: {{ $client->cin }}</small>@endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $client->phone }}</td>
                    <td>{{ $client->email ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $client->reservations_count }}</span>
                    </td>
                    <td class="text-end fw-semibold text-success">{{ number_format($client->total_spent,0,'.',',') }} DT</td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-sm btn-outline-info" title="Voir"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Supprimer ce client ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-people display-4 d-block mb-2"></i>
                    Aucun client trouvé
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">{{ $clients->firstItem() }}–{{ $clients->lastItem() }} sur {{ $clients->total() }}</small>
        {{ $clients->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
