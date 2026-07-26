@extends('layouts.app')

@section('title', 'Réservations')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-journal-check me-2 text-primary"></i>Réservations</h4>
    <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouvelle réservation
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Client, N° réservation..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>En cours</option>
                    <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>Terminée</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="property_id" class="form-select">
                    <option value="">Tous logements</option>
                    @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>
                        {{ $prop->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"
                       placeholder="Date début">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"
                       placeholder="Date fin">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Reservations Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>N° Réservation</th>
                        <th>Client</th>
                        <th>Logement</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Nuits</th>
                        <th>Montant</th>
                        <th>Payé</th>
                        <th>Paiement</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                    <tr>
                        <td>
                            <a href="{{ route('admin.reservations.show', $res) }}" class="text-decoration-none fw-500 text-primary">
                                {{ $res->reservation_number }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight:500;">{{ $res->client->full_name }}</div>
                            <small class="text-muted">{{ $res->client->phone }}</small>
                        </td>
                        <td>
                            <span>{{ $res->property->name }}</span>
                            <div style="font-size:11px; color:#94a3b8;">{{ $res->property->getTypeLabel() }}</div>
                        </td>
                        <td>
                            <span {{ $res->check_in->isToday() ? 'class=fw-bold text-success' : '' }}>
                                {{ $res->check_in->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>{{ $res->check_out->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $res->nights }}</td>
                        <td class="fw-bold">{{ number_format($res->final_amount, 2) }} DT</td>
                        <td>
                            <div>{{ number_format($res->amount_paid, 2) }} DT</div>
                            @if($res->amount_remaining > 0)
                            <small class="text-danger">-{{ number_format($res->amount_remaining, 2) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $res->getPaymentStatusBadge() }}">
                                {{ $res->getPaymentStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $res->getStatusBadge() }}">{{ $res->getStatusLabel() }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.reservations.show', $res) }}"
                                   class="btn btn-sm btn-outline-primary" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(!in_array($res->status, ['checked_out', 'cancelled']))
                                @if($res->status === 'confirmed')
                                <form method="POST" action="{{ route('admin.reservations.checkin', $res) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success" title="Check-in">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </button>
                                </form>
                                @elseif($res->status === 'checked_in')
                                <form method="POST" action="{{ route('admin.reservations.checkout', $res) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-warning" title="Check-out">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <i class="bi bi-journal-x" style="font-size:36px;color:#cbd5e1;"></i>
                            <p class="mt-2 text-muted">Aucune réservation trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reservations->hasPages())
    <div class="card-footer">
        {{ $reservations->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
