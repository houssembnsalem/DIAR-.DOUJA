@extends('layouts.app')
@section('title', 'Facture '.$reservation->reservation_number)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <h1 class="h3 mb-0">Facture {{ $reservation->reservation_number }}</h1>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>Imprimer</button>
        <a href="{{ route('admin.reservations.invoice.pdf', $reservation) }}" class="btn btn-danger"><i class="bi bi-file-pdf me-1"></i>PDF</a>
        <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
    </div>
</div>

<div class="card shadow-sm" id="invoice">
    <div class="card-body p-5">
        <div class="row mb-5">
            <div class="col-6">
                <img src="{{ asset('img/logo.png') }}" alt="DIAR DOUJA Logo" style="height: 100px; margin-bottom: 15px;">
                <div class="text-muted">
                    <div class="fw-bold">DD Tazarka Beach Bungalows</div>
                    <div>Matricule Fiscal: 000/M/A/1960281Z</div>
                    <div><i class="bi bi-geo-alt me-1"></i> TAZARKA PLAGE 8024, NABEUL, TUNISIA</div>
                    <div><i class="bi bi-telephone me-1"></i> +216 92 560 510 / 92 560 501</div>
                    <div><i class="bi bi-envelope me-1"></i> CONTACT@DIARDOUJA.TN</div>
                    <div><i class="bi bi-globe me-1"></i> WWW.DIARDOUJA.TN</div>
                    <div><i class="bi bi-instagram me-1"></i> @DIARDOUJA.TN</div>
                </div>
            </div>
            <div class="col-6 text-end">
                <h1 class="display-6 fw-bold text-uppercase text-muted">FACTURE</h1>
                <table class="ms-auto" style="min-width:220px;">
                    <tr><td class="text-muted pe-3">N° Facture :</td><td class="fw-bold">{{ $reservation->reservation_number }}</td></tr>
                    <tr><td class="text-muted pe-3">Date :</td><td>{{ now()->format('d/m/Y') }}</td></tr>
                    <tr><td class="text-muted pe-3">Statut :</td><td>
                        @if($reservation->payment_status === 'paid')
                            <span class="badge bg-success">Payée</span>
                        @elseif($reservation->payment_status === 'partial')
                            <span class="badge bg-warning">Partielle</span>
                        @else
                            <span class="badge bg-danger">Non payée</span>
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-6">
                <div class="bg-light p-3 rounded">
                    <div class="text-muted small fw-semibold mb-2">FACTURER À :</div>
                    <div class="fw-bold fs-5">{{ $reservation->client->full_name ?? 'Client' }}</div>
                    @if($reservation->client)
                    <div>{{ $reservation->client->phone }}</div>
                    <div>{{ $reservation->client->email }}</div>
                    @if($reservation->client->address)<div>{{ $reservation->client->address }}</div>@endif
                    @if($reservation->client->cin)<div>CIN : {{ $reservation->client->cin }}</div>@endif
                    @endif
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded">
                    <div class="text-muted small fw-semibold mb-2">DÉTAILS DU SÉJOUR :</div>
                    <div class="fw-bold fs-5">{{ $reservation->property->name }}</div>
                    <div class="text-muted">{{ $reservation->property->getTypeLabel() }}</div>
                    <div class="mt-2">
                        <span class="text-muted">Arrivée :</span> <strong>{{ $reservation->check_in->format('d/m/Y') }}</strong><br>
                        <span class="text-muted">Départ :</span> <strong>{{ $reservation->check_out->format('d/m/Y') }}</strong><br>
                        <span class="text-muted">Durée :</span> <strong>{{ $reservation->nights }} nuit(s)</strong>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered mb-4">
            <thead class="table-dark">
                <tr>
                    <th>Description</th>
                    <th class="text-center" style="width:100px;">Qté</th>
                    <th class="text-end" style="width:150px;">Prix unitaire</th>
                    <th class="text-end" style="width:150px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $reservation->property->name }}</strong><br>
                        <small class="text-muted">Hébergement — {{ $reservation->check_in->format('d/m/Y') }} au {{ $reservation->check_out->format('d/m/Y') }}</small>
                    </td>
                    <td class="text-center">{{ $reservation->nights }} nuit(s)</td>
                    <td class="text-end">{{ number_format($reservation->price_per_night, 2, ',', ' ') }} DT</td>
                    <td class="text-end fw-bold">{{ number_format($reservation->nights * $reservation->price_per_night, 2, ',', ' ') }} DT</td>
                </tr>
                @foreach($reservation->services as $rs)
                <tr>
                    <td>
                        <strong>{{ $rs->service->name }}</strong><br>
                        <small class="text-muted">Service additionnel</small>
                    </td>
                    <td class="text-center">{{ $rs->quantity }} ({{ $rs->period }}j)</td>
                    <td class="text-end">{{ number_format($rs->price, 2, ',', ' ') }} DT</td>
                    <td class="text-end fw-bold">{{ number_format($rs->total, 2, ',', ' ') }} DT</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr><td colspan="3" class="text-end fw-semibold">Sous-total :</td><td class="text-end">{{ number_format($reservation->total_amount, 2, ',', ' ') }} DT</td></tr>
                @if($reservation->discount > 0)
                <tr><td colspan="3" class="text-end text-danger fw-semibold">Remise :</td><td class="text-end text-danger">-{{ number_format($reservation->discount, 2, ',', ' ') }} DT</td></tr>
                @endif
                <tr class="table-primary"><td colspan="3" class="text-end fw-bold fs-5">TOTAL :</td><td class="text-end fw-bold fs-5">{{ number_format($reservation->final_amount, 2, ',', ' ') }} DT</td></tr>
                <tr><td colspan="3" class="text-end text-success fw-semibold">Montant payé :</td><td class="text-end text-success">{{ number_format($reservation->amount_paid, 2, ',', ' ') }} DT</td></tr>
                @if($reservation->amount_remaining > 0)
                <tr><td colspan="3" class="text-end text-danger fw-semibold">Reste à payer :</td><td class="text-end text-danger fw-bold">{{ number_format($reservation->amount_remaining, 2, ',', ' ') }} DT</td></tr>
                @endif
            </tfoot>
        </table>

        @if($reservation->payments->count())
        <h6 class="fw-bold mb-3">Historique des paiements :</h6>
        <table class="table table-sm table-bordered mb-4">
            <thead class="table-secondary">
                <tr><th>Date</th><th>Méthode</th><th>Référence</th><th class="text-end">Montant</th></tr>
            </thead>
            <tbody>
                @foreach($reservation->payments as $p)
                <tr>
                    <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                    <td>{{ $p->getMethodLabel() }}</td>
                    <td>{{ $p->reference ?? '—' }}</td>
                    <td class="text-end fw-semibold text-success">{{ number_format($p->amount, 2, ',', ' ') }} DT</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($reservation->notes)
        <div class="alert alert-light border mb-4"><strong>Notes :</strong> {{ $reservation->notes }}</div>
        @endif

        <hr>
        <div class="row">
            <div class="col-6 text-muted small">
                <strong>Conditions :</strong> Paiement dû à la réception. Annulation 48h avant l'arrivée.
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small mb-1">Signature & Cachet</div>
                <div style="height:60px;border-bottom:1px solid #ccc;width:200px;margin-left:auto;"></div>
            </div>
        </div>
    </div>
</div>
@endsection
