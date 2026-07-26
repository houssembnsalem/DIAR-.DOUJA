@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-700 mb-1" style="font-weight:700;">Bonjour, {{ auth()->user()->name }} 👋</h4>
        <p class="text-muted mb-0" style="font-size:14px;">{{ now()->translatedFormat('l d F Y') }}</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalProperties }}</div>
                <div class="stat-label">Logements</div>
                <small class="text-success"><i class="bi bi-circle-fill" style="font-size:8px;"></i> {{ $availableProperties }} disponibles</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4; color:#16a34a;">
                <i class="bi bi-journal-check"></i>
            </div>
            <div>
                <div class="stat-value">{{ $activeReservations }}</div>
                <div class="stat-label">Réservations actives</div>
                <small class="text-warning"><i class="bi bi-arrow-down"></i> {{ $todayCheckOuts }} check-out aujourd'hui</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fefce8; color:#ca8a04;">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div>
                <div class="stat-value">{{ number_format($monthlyRevenue, 0, ',', ' ') }} DT</div>
                <div class="stat-label">Revenus du mois</div>
                <small class="text-muted">Dépenses: {{ number_format($monthlyExpenses, 0, ',', ' ') }} DT</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:{{ $monthlyProfit >= 0 ? '#f0fdf4' : '#fef2f2' }}; color:{{ $monthlyProfit >= 0 ? '#16a34a' : '#dc2626' }};">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <div class="stat-value" style="color:{{ $monthlyProfit >= 0 ? '#16a34a' : '#dc2626' }};">{{ number_format($monthlyProfit, 0, ',', ' ') }} DT</div>
                <div class="stat-label">Bénéfice du mois</div>
                <small class="text-muted">{{ $totalClients }} clients total</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Revenue Chart -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-bar-chart me-2 text-primary"></i>Revenus & Dépenses (6 derniers mois)</span>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Properties Status -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-house-check me-2 text-primary"></i>État des logements
            </div>
            <div class="card-body p-0">
                @foreach($propertiesOccupancy as $prop)
                <div class="d-flex align-items-center px-4 py-3 border-bottom">
                    <div class="me-3">
                        @if($prop->is_occupied)
                            <span class="badge bg-success">Occupé</span>
                        @elseif($prop->status === 'available')
                            <span class="badge bg-primary">Libre</span>
                        @else
                            <span class="badge bg-secondary">{{ $prop->getStatusLabel() }}</span>
                        @endif
                    </div>
                    <div class="flex-1" style="flex:1; min-width:0;">
                        <div style="font-size:14px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $prop->name }}</div>
                        <div style="font-size:12px; color:#64748b;">{{ $prop->getTypeLabel() }} • {{ $prop->price_per_night }} DT/nuit</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Reservations -->
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Réservations récentes</span>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Logement</th>
                                <th>Dates</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReservations as $res)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.reservations.show', $res) }}" class="text-decoration-none fw-500">
                                        {{ $res->client->full_name }}
                                    </a>
                                    <div style="font-size:11px; color:#94a3b8;">{{ $res->reservation_number }}</div>
                                </td>
                                <td style="font-size:13px;">{{ $res->property->name }}</td>
                                <td style="font-size:12px;">
                                    {{ $res->check_in->format('d/m') }} → {{ $res->check_out->format('d/m/Y') }}
                                </td>
                                <td style="font-weight:600;">{{ number_format($res->final_amount, 2) }} DT</td>
                                <td><span class="badge bg-{{ $res->getStatusBadge() }}">{{ $res->getStatusLabel() }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Aucune réservation</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Check-ins -->
    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-event me-2 text-success"></i>Arrivées prochaines (7 jours)
            </div>
            <div class="card-body p-0">
                @forelse($upcomingCheckIns as $res)
                <div class="d-flex align-items-center px-4 py-3 border-bottom">
                    <div class="me-3 text-center" style="min-width:45px;">
                        <div style="font-size:20px; font-weight:700; color:#2563eb; line-height:1;">{{ $res->check_in->format('d') }}</div>
                        <div style="font-size:10px; color:#64748b; text-transform:uppercase;">{{ $res->check_in->translatedFormat('M') }}</div>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:14px; font-weight:500;">{{ $res->client->full_name }}</div>
                        <div style="font-size:12px; color:#64748b;">{{ $res->property->name }} • {{ $res->nights }} nuits</div>
                    </div>
                    <a href="{{ route('admin.reservations.show', $res) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x" style="font-size:32px; opacity:0.3;"></i>
                    <p class="mt-2 mb-0" style="font-size:14px;">Aucune arrivée prévue</p>
                </div>
                @endforelse
            </div>
        </div>

        @if($pendingCheckouts->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header" style="background:#fff3cd; border-color:#ffc107;">
                <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                Check-outs en attente ({{ $pendingCheckouts->count() }})
            </div>
            <div class="card-body p-0">
                @foreach($pendingCheckouts as $res)
                <div class="d-flex align-items-center px-4 py-3 border-bottom">
                    <div style="flex:1;">
                        <div style="font-size:14px; font-weight:500;">{{ $res->client->full_name }}</div>
                        <div style="font-size:12px; color:#64748b;">{{ $res->property->name }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.reservations.checkout', $res) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-warning">Check-out</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartData = @json($chartData);
const labels = chartData.map(d => d.month);
const revenues = chartData.map(d => d.revenue);
const expenses = chartData.map(d => d.expenses);
const profits = chartData.map(d => d.profit);

const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Revenus',
                data: revenues,
                backgroundColor: 'rgba(37, 99, 235, 0.8)',
                borderRadius: 6,
            },
            {
                label: 'Dépenses',
                data: expenses,
                backgroundColor: 'rgba(220, 38, 38, 0.7)',
                borderRadius: 6,
            },
            {
                label: 'Bénéfice',
                data: profits,
                type: 'line',
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#16a34a',
                yAxisID: 'y',
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ' + ctx.raw.toLocaleString('fr-FR') + ' DT'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: val => val.toLocaleString('fr-FR') + ' DT'
                }
            }
        }
    }
});
</script>
@endpush
