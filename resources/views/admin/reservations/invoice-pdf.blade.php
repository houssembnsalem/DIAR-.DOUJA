<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#1e293b; background:#fff; }
.page { padding:30px; }
.header { display:table; width:100%; margin-bottom:30px; }
.header-left { display:table-cell; width:50%; vertical-align:top; }
.header-right { display:table-cell; width:50%; vertical-align:top; text-align:right; }
.company-name { font-size:22px; font-weight:bold; color:#2563eb; }
.invoice-title { font-size:28px; font-weight:bold; color:#94a3b8; text-transform:uppercase; }
.meta-table { margin-left:auto; }
.meta-table td { padding:2px 8px; }
.meta-label { color:#64748b; }
.section-row { display:table; width:100%; margin-bottom:25px; }
.section-cell { display:table-cell; width:48%; vertical-align:top; }
.section-gap { display:table-cell; width:4%; }
.box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px; }
.box-label { font-size:10px; font-weight:bold; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
.box-name { font-size:15px; font-weight:bold; margin-bottom:4px; }
table.items { width:100%; border-collapse:collapse; margin-bottom:15px; }
table.items th { background:#1e293b; color:#fff; padding:8px 12px; text-align:left; font-size:11px; }
table.items th.right { text-align:right; }
table.items td { padding:10px 12px; border-bottom:1px solid #e2e8f0; }
table.items td.right { text-align:right; }
table.items tr:last-child td { border-bottom:none; }
.totals-row { display:table; width:100%; }
.totals-spacer { display:table-cell; width:55%; }
.totals-box { display:table-cell; width:45%; }
table.totals { width:100%; border-collapse:collapse; }
table.totals td { padding:5px 10px; }
table.totals .label { color:#64748b; text-align:right; }
table.totals .value { text-align:right; font-weight:bold; }
.total-final { background:#ca8a04; color:#fff; }
.total-final td { padding:10px; }
.paid { color:#16a34a; }
.due { color:#dc2626; }
.footer { margin-top:30px; border-top:1px solid #e2e8f0; padding-top:15px; color:#64748b; font-size:10px; }
.badge-paid { background:#dcfce7; color:#16a34a; padding:2px 8px; border-radius:10px; font-size:11px; }
.badge-partial { background:#fef9c3; color:#ca8a04; padding:2px 8px; border-radius:10px; font-size:11px; }
.badge-unpaid { background:#fee2e2; color:#dc2626; padding:2px 8px; border-radius:10px; font-size:11px; }
</style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('img/logo.png') }}" style="height:80px; margin-bottom:10px;">
            <div style="color:#64748b; margin-top:8px;">
                <span style="font-weight:bold; color:#1e293b; font-size:14px;">DD Tazarka Beach Bungalows</span><br>
                MF: 000/M/A/1960281Z<br>
                TAZARKA PLAGE 8024, NABEUL, TUNISIA<br>
                Tél: +216 92 560 510 / 92 560 501<br>
                Email: CONTACT@DIARDOUJA.TN<br>
                Web: WWW.DIARDOUJA.TN | Social: @DIARDOUJA.TN
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">Facture</div>
            <table class="meta-table" style="margin-top:10px;">
                <tr><td class="meta-label">N° :</td><td><strong>{{ $reservation->number }}</strong></td></tr>
                <tr><td class="meta-label">Date :</td><td>{{ now()->format('d/m/Y') }}</td></tr>
                <tr><td class="meta-label">Statut :</td><td>
                    @if($reservation->payment_status==='paid')
                        <span class="badge-paid">Payée</span>
                    @elseif($reservation->payment_status==='partial')
                        <span class="badge-partial">Partielle</span>
                    @else
                        <span class="badge-unpaid">Non payée</span>
                    @endif
                </td></tr>
            </table>
        </div>
    </div>

    <div class="section-row">
        <div class="section-cell">
            <div class="box">
                <div class="box-label">Facturer à :</div>
                <div class="box-name">{{ $reservation->client->full_name ?? 'Client' }}</div>
                @if($reservation->client)
                <div style="color:#64748b;">
                    {{ $reservation->client->phone }}<br>
                    {{ $reservation->client->email }}<br>
                    @if($reservation->client->cin)CIN : {{ $reservation->client->cin }}@endif
                </div>
                @endif
            </div>
        </div>
        <div class="section-gap"></div>
        <div class="section-cell">
            <div class="box">
                <div class="box-label">Détails du séjour :</div>
                <div class="box-name">{{ $reservation->property->name }}</div>
                <div style="color:#64748b;">
                    Arrivée : <strong>{{ $reservation->check_in->format('d/m/Y') }}</strong><br>
                    Départ : <strong>{{ $reservation->check_out->format('d/m/Y') }}</strong><br>
                    Durée : <strong>{{ $reservation->nights }} nuit(s)</strong>
                </div>
            </div>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th style="width:80px; text-align:center;">Quantité</th>
                <th class="right" style="width:130px;">Prix unitaire</th>
                <th class="right" style="width:130px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $reservation->property->name }}</strong><br>
                    <span style="color:#64748b; font-size:11px;">Hébergement — {{ $reservation->check_in->format('d/m/Y') }} au {{ $reservation->check_out->format('d/m/Y') }}</span>
                </td>
                <td style="text-align:center;">{{ $reservation->nights }} nuit(s)</td>
                <td class="right">{{ number_format($reservation->property->price_per_night, 2, ',', ' ') }} DT</td>
                <td class="right"><strong>{{ number_format($reservation->nights * $reservation->price_per_night, 2, ',', ' ') }} DT</strong></td>
            </tr>
            @foreach($reservation->services as $rs)
            <tr>
                <td>
                    <strong>{{ $rs->service->name }}</strong><br>
                    <span style="color:#64748b; font-size:11px;">Service additionnel</span>
                </td>
                <td style="text-align:center;">{{ $rs->quantity }} ({{ $rs->period }}j)</td>
                <td class="right">{{ number_format($rs->price, 2, ',', ' ') }} DT</td>
                <td class="right"><strong>{{ number_format($rs->total, 2, ',', ' ') }} DT</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-row">
        <div class="totals-spacer"></div>
        <div class="totals-box">
            <table class="totals">
                <tr><td class="label">Sous-total :</td><td class="value">{{ number_format($reservation->total_amount, 2, ',', ' ') }} DT</td></tr>
                @if($reservation->discount > 0)
                <tr><td class="label" style="color:#dc2626;">Remise :</td><td class="value" style="color:#dc2626;">-{{ number_format($reservation->discount, 2, ',', ' ') }} DT</td></tr>
                @endif
                <tr class="total-final"><td style="text-align:right; font-weight:bold; font-size:14px;">TOTAL :</td><td style="text-align:right; font-weight:bold; font-size:14px;">{{ number_format($reservation->final_amount, 2, ',', ' ') }} DT</td></tr>
                <tr><td class="label paid">Montant payé :</td><td class="value paid">{{ number_format($reservation->amount_paid, 2, ',', ' ') }} DT</td></tr>
                @if($reservation->amount_remaining > 0)
                <tr><td class="label due">Reste à payer :</td><td class="value due">{{ number_format($reservation->amount_remaining, 2, ',', ' ') }} DT</td></tr>
                @endif
            </table>
        </div>
    </div>

    @if($reservation->payments->count())
    <div style="margin-top:20px;">
        <div style="font-weight:bold; margin-bottom:8px;">Historique des paiements :</div>
        <table class="items">
            <thead><tr><th>Date</th><th>Méthode</th><th>Référence</th><th class="right">Montant</th></tr></thead>
            <tbody>
                @foreach($reservation->payments as $p)
                <tr>
                    <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                    <td>{{ $p->getMethodLabel() }}</td>
                    <td>{{ $p->reference ?? '—' }}</td>
                    <td class="right paid"><strong>{{ number_format($p->amount, 2, ',', ' ') }} DT</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <div style="display:table; width:100%;">
            <div style="display:table-cell; width:60%;">
                <strong>Conditions :</strong> Paiement dû à la réception. Annulation 48h avant l'arrivée sans frais.
            </div>
            <div style="display:table-cell; width:40%; text-align:right;">
                Signature & Cachet<br><br>
                <div style="border-bottom:1px solid #ccc; width:150px; display:inline-block; margin-top:30px;"></div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
