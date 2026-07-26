@extends('layouts.app')

@section('title', 'Réservation ' . $reservation->reservation_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0 topbar-title">Réservation {{ $reservation->reservation_number }}</h4>
            <small class="text-muted">Créée le {{ $reservation->created_at->format('d/m/Y à H:i') }} par {{ $reservation->creator->name }}</small>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.reservations.invoice', $reservation) }}" class="btn btn-outline-secondary" target="_blank">
            <i class="bi bi-receipt me-1"></i>Facture
        </a>
        <a href="{{ route('admin.reservations.invoice.pdf', $reservation) }}" class="btn btn-outline-danger">
            <i class="bi bi-file-pdf me-1"></i>PDF
        </a>
        @if(!in_array($reservation->status, ['checked_out', 'cancelled']))
        <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        @endif
    </div>
</div>

<!-- Status Bar -->
<div class="card mb-4" style="border-color: {{ $reservation->status === 'cancelled' ? '#dc2626' : '#2563eb' }}20;">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-{{ $reservation->getStatusBadge() }} px-3 py-2" style="font-size:14px;">
                    {{ $reservation->getStatusLabel() }}
                </span>
                <span class="badge bg-{{ $reservation->getPaymentStatusBadge() }} px-3 py-2" style="font-size:14px;">
                    Paiement: {{ $reservation->getPaymentStatusLabel() }}
                </span>
            </div>
            <div class="d-flex gap-2">
                @if($reservation->status === 'pending')
                <form method="POST" action="{{ route('admin.reservations.confirm', $reservation) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Confirmer</button>
                </form>
                @endif
                @if($reservation->status === 'confirmed')
                <form method="POST" action="{{ route('admin.reservations.checkin', $reservation) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-info text-white"><i class="bi bi-box-arrow-in-right me-1"></i>Check-in</button>
                </form>
                @endif
                @if($reservation->status === 'checked_in')
                <form method="POST" action="{{ route('admin.reservations.checkout', $reservation) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-warning"><i class="bi bi-box-arrow-right me-1"></i>Check-out</button>
                </form>
                @endif
                @if(!in_array($reservation->status, ['checked_out', 'cancelled']))
                <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}"
                      onsubmit="return confirm('Annuler cette réservation?')">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Annuler</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-column gap-4">
    <!-- Reservation Details -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom-0"><i class="bi bi-info-circle me-2 text-primary"></i>Détails de la réservation</div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="p-4 bg-light rounded-4 text-center h-100 d-flex flex-column justify-content-center border-0">
                        <div class="text-muted small mb-1 text-uppercase fw-bold ls-1">Check-in</div>
                        <div class="fw-bold fs-3 text-dark">{{ $reservation->check_in->format('d/m/Y') }}</div>
                        <div class="text-muted text-capitalize">{{ $reservation->check_in->translatedFormat('l') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 bg-light rounded-4 text-center h-100 d-flex flex-column justify-content-center border-0">
                        <div class="text-muted small mb-1 text-uppercase fw-bold ls-1">Check-out</div>
                        <div class="fw-bold fs-3 text-dark">{{ $reservation->check_out->format('d/m/Y') }}</div>
                        <div class="text-muted text-capitalize">{{ $reservation->check_out->translatedFormat('l') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-sm-3 text-center">
                            <div class="p-3 border rounded-4 h-100 d-flex flex-column justify-content-center">
                                <div class="text-muted small">Nuits</div>
                                <div class="fw-bold fs-4 text-primary">{{ $reservation->nights }}</div>
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">
                            <div class="p-3 border rounded-4 h-100 d-flex flex-column justify-content-center">
                                <div class="text-muted small">Personnes</div>
                                <div class="fw-bold fs-4">{{ $reservation->guests_count }}</div>
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">
                            <div class="p-3 border rounded-4 h-100 d-flex flex-column justify-content-center">
                                <div class="text-muted small">Prix/nuit</div>
                                <div class="fw-bold fs-5 text-dark">{{ number_format($reservation->price_per_night, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">
                            <div class="p-3 border rounded-4 h-100 d-flex flex-column justify-content-center">
                                <div class="text-muted small">Source</div>
                                <div class="fw-bold text-dark">{{ ucfirst($reservation->source) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($reservation->notes)
            <div class="mt-4 p-3 bg-warning bg-opacity-10 rounded-4 border-start border-4 border-warning">
                <i class="bi bi-sticky me-2 text-warning fs-5"></i><strong>Notes :</strong> {{ $reservation->notes }}
            </div>
            @endif
        </div>
    </div>

    <!-- Info Row: Logement & Client -->
    <div class="row g-4">
        <!-- Property Info -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0"><i class="bi bi-building me-2 text-primary"></i>Logement</div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-4">
                        @if($reservation->property->photos->first())
                        <img src="{{ asset('storage/' . $reservation->property->photos->first()->path) }}"
                             style="width:120px;height:90px;object-fit:cover;border-radius:12px;" class="shadow-sm">
                        @else
                        <div style="width:120px;height:90px;background:#f8fafc;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:40px;" class="border">
                            🏠
                        </div>
                        @endif
                        <div>
                            <h5 class="fw-bold mb-1">{{ $reservation->property->name }}</h5>
                            <div class="text-muted mb-2">{{ $reservation->property->getTypeLabel() }} • {{ $reservation->property->location }}</div>
                            <a href="{{ route('admin.properties.show', $reservation->property) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Voir le logement <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Info -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0"><i class="bi bi-person me-2 text-primary"></i>Client</div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-4">
                        <div style="width:70px;height:70px;background:linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;color:#2563eb;" class="shadow-sm">
                            {{ strtoupper(substr($reservation->client->first_name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $reservation->client->full_name }}</h5>
                            <div class="d-flex gap-3 flex-wrap small">
                                <div class="d-flex gap-2 align-items-center">
                                    <i class="bi bi-telephone text-muted"></i>
                                    <a href="tel:{{ $reservation->client->phone }}" class="text-decoration-none fw-semibold">{{ $reservation->client->phone }}</a>
                                </div>
                                @if($reservation->client->email)
                                <div class="d-flex gap-2 align-items-center">
                                    <i class="bi bi-envelope text-muted"></i>
                                    <a href="mailto:{{ $reservation->client->email }}" class="text-decoration-none">{{ $reservation->client->email }}</a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('admin.clients.show', $reservation->client) }}" class="btn btn-sm btn-link text-primary text-decoration-none p-0">
                            Profil <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Additionnels -->
    @if($reservation->services->isNotEmpty())
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom-0"><i class="bi bi-patch-plus me-2 text-primary"></i>Services additionnels</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-muted">
                        <tr>
                            <th class="ps-4">SERVICE</th>
                            <th>DÉTAILS</th>
                            <th>PRIX UNIT.</th>
                            <th class="text-end pe-4">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservation->services as $rService)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $rService->service->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">
                                    {{ $rService->quantity }} unité(s) • {{ $rService->period }} jour(s)
                                </span>
                            </td>
                            <td>{{ number_format($rService->price, 2) }} DT</td>
                            <td class="text-end pe-4 fw-bold text-primary">{{ number_format($rService->total, 2) }} DT</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Payments -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom-0 d-flex align-items-center justify-content-between">
            <span><i class="bi bi-cash-coin me-2 text-primary"></i>Historique des paiements</span>
            @if($reservation->amount_remaining > 0 && !in_array($reservation->status, ['cancelled']))
            <button class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="bi bi-plus-lg me-1"></i>Enregistrer un paiement
            </button>
            @endif
        </div>
        <div class="card-body">
            <!-- Financial Summary Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="p-4 bg-light rounded-4 text-center border-0 h-100">
                        <div class="text-muted small mb-1 text-uppercase fw-bold ls-1">Total à payer</div>
                        <div class="fw-bold fs-3">{{ number_format($reservation->final_amount, 2) }} <small>DT</small></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-success bg-opacity-10 rounded-4 text-center border-0 h-100">
                        <div class="text-success small mb-1 text-uppercase fw-bold ls-1">Montant Payé</div>
                        <div class="fw-bold fs-3 text-success">{{ number_format($reservation->amount_paid, 2) }} <small>DT</small></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-{{ $reservation->amount_remaining > 0 ? 'danger' : 'success' }} bg-opacity-10 rounded-4 text-center border-0 h-100">
                        <div class="text-{{ $reservation->amount_remaining > 0 ? 'danger' : 'success' }} small mb-1 text-uppercase fw-bold ls-1">Reste à régler</div>
                        <div class="fw-bold fs-3 text-{{ $reservation->amount_remaining > 0 ? 'danger' : 'success' }}">
                            {{ number_format($reservation->amount_remaining, 2) }} <small>DT</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress bar -->
            @php $pct = $reservation->final_amount > 0 ? min(100, ($reservation->amount_paid / $reservation->final_amount) * 100) : 0; @endphp
            <div class="progress mb-4 rounded-pill" style="height:12px;">
                <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
            </div>

            <!-- Payment table -->
            @if($reservation->payments->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted">
                        <tr>
                            <th class="border-0">DATE</th>
                            <th class="border-0">MONTANT</th>
                            <th class="border-0">MÉTHODE</th>
                            <th class="border-0">NOTES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservation->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td class="fw-bold text-success">{{ number_format($payment->amount, 2) }} DT</td>
                            <td><span class="badge bg-light text-dark border fw-normal">{{ $payment->getMethodLabel() }}</span></td>
                            <td class="text-muted small">{{ $payment->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <img src="https://cdn-icons-png.flaticon.com/512/3757/3757835.png" style="width:48px; opacity:0.3; filter:grayscale(1);">
                <p class="text-muted mt-2">Aucun paiement enregistré pour le moment.</p>
            </div>
            @endif
        </div>
    </div>
</div>


<!-- Detailed Invoice Summary -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Récapitulatif de la facture</h5>
                <small class="text-muted">Détail complet des prestations et services pour la réservation {{ $reservation->reservation_number }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reservations.invoice', $reservation) }}" class="btn btn-outline-primary" target="_blank">
                    <i class="bi bi-printer me-1"></i>Imprimer la facture
                </a>
                <a href="{{ route('admin.reservations.invoice.pdf', $reservation) }}" class="btn btn-outline-danger">
                    <i class="bi bi-file-pdf me-1"></i>Télécharger PDF
                </a>
            </div>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-4">DESCRIPTION</th>
                        <th class="text-center">QUANTITÉ</th>
                        <th class="text-end pe-4">PRIX UNITAIRE</th>
                        <th class="text-end pe-4">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-bottom">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">Hébergement - {{ $reservation->property->name }}</div>
                            <div class="small text-muted">Du {{ $reservation->check_in->format('d/m/Y') }} au {{ $reservation->check_out->format('d/m/Y') }}</div>
                        </td>
                        <td class="text-center">{{ $reservation->nights }} nuits</td>
                        <td class="text-end pe-4">{{ number_format($reservation->price_per_night, 2) }} DT</td>
                        <td class="text-end pe-4 fw-bold">{{ number_format($reservation->nights * $reservation->price_per_night, 2) }} DT</td>
                    </tr>
                    @foreach($reservation->services as $rs)
                    <tr class="border-bottom">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ $rs->service->name }}</div>
                            <div class="small text-muted">Service additionnel</div>
                        </td>
                        <td class="text-center">{{ $rs->quantity }} ({{ $rs->period }}j)</td>
                        <td class="text-end pe-4">{{ number_format($rs->price, 2) }} DT</td>
                        <td class="text-end pe-4 fw-bold">{{ number_format($rs->total, 2) }} DT</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6 offset-md-6">
                <div class="p-4 bg-light rounded-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sous-total</span>
                        <span class="fw-semibold">{{ number_format($reservation->total_amount, 2) }} DT</span>
                    </div>
                    @if($reservation->discount > 0)
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Remise</span>
                        <span class="fw-semibold">-{{ number_format($reservation->discount, 2) }} DT</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h4 fw-bold mb-0">TOTAL À PAYER</span>
                        <span class="h3 fw-bold mb-0 text-primary">{{ number_format($reservation->final_amount, 2) }} DT</span>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between mb-1 text-success small">
                            <span>Montant déjà payé</span>
                            <span>{{ number_format($reservation->amount_paid, 2) }} DT</span>
                        </div>
                        @if($reservation->amount_remaining > 0)
                        <div class="d-flex justify-content-between text-danger fw-bold">
                            <span>Reste à régler</span>
                            <span>{{ number_format($reservation->amount_remaining, 2) }} DT</span>
                        </div>
                        @else
                        <div class="text-center text-success fw-bold mt-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Facture entièrement réglée
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Enregistrer un paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.reservations.payments.add', $reservation) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        Reste à payer: <strong>{{ number_format($reservation->amount_remaining, 2) }} DT</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant (DT) *</label>
                        <input type="number" name="amount" class="form-control"
                               max="{{ $reservation->amount_remaining }}" step="0.5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Méthode de paiement *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">💵 Espèces</option>
                            <option value="card">💳 Carte bancaire</option>
                            <option value="transfer">🏦 Virement</option>
                            <option value="check">📄 Chèque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date du paiement *</label>
                        <input type="date" name="payment_date" class="form-control"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Notes optionnelles...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
