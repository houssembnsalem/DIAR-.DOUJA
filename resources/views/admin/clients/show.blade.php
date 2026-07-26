@extends('layouts.app')
@section('title', $client->full_name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $client->full_name }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active">{{ $client->full_name }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
        <a href="{{ route('admin.reservations.create', ['client_id'=>$client->id]) }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Réservation</a>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width:72px;height:72px;font-size:26px;">
                    {{ strtoupper(substr($client->first_name,0,1).substr($client->last_name,0,1)) }}
                </div>
                <h5 class="mb-1">{{ $client->full_name }}</h5>
                @if($client->nationality)<span class="badge bg-light text-dark">{{ $client->nationality }}</span>@endif
                <hr>
                <div class="text-start">
                    <div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-telephone text-primary"></i><span>{{ $client->phone }}</span></div>
                    @if($client->email)<div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-envelope text-primary"></i><span>{{ $client->email }}</span></div>@endif
                    @if($client->cin)<div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-credit-card text-primary"></i><span>CIN: {{ $client->cin }}</span></div>@endif
                    @if($client->address)<div class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-geo-alt text-primary mt-1"></i><span>{{ $client->address }}</span></div>@endif
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-6">
                <div class="card shadow-sm text-center p-3">
                    <div class="fs-3 fw-bold text-primary">{{ $client->reservations->count() }}</div>
                    <div class="text-muted small">Séjours</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card shadow-sm text-center p-3">
                    <div class="fs-4 fw-bold text-success">{{ number_format($client->total_spent,0,'.',',') }}</div>
                    <div class="text-muted small">Total DT</div>
                </div>
            </div>
        </div>
        @if($client->notes)
        <div class="card shadow-sm mt-3">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Notes</h6></div>
            <div class="card-body"><p class="mb-0 text-muted">{{ $client->notes }}</p></div>
        </div>
        @endif
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Historique des réservations</h6>
                <span class="badge bg-primary">{{ $client->reservations->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>N°</th><th>Logement</th><th>Arrivée</th><th>Départ</th><th>Montant</th><th>Statut</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($client->reservations()->with('property')->latest('check_in')->get() as $res)
                        <tr>
                            <td><span class="fw-semibold text-primary">{{ $res->number }}</span></td>
                            <td>{{ $res->property->name ?? '—' }}</td>
                            <td>{{ $res->check_in->format('d/m/Y') }}</td>
                            <td>{{ $res->check_out->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ number_format($res->total_amount,0,'.',',') }} DT</td>
                            <td>
                                @php $sc=['pending'=>'warning','confirmed'=>'info','checked_in'=>'primary','checked_out'=>'success','cancelled'=>'danger']; @endphp
                                <span class="badge bg-{{ $sc[$res->status] ?? 'secondary' }}">{{ $res->getStatusLabel() }}</span>
                            </td>
                            <td><a href="{{ route('admin.reservations.show', $res) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">Aucune réservation</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
