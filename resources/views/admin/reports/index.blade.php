@extends('layouts.app')
@section('title', 'Rapports & Statistiques')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Rapports & Statistiques</h1>
        <p class="text-muted mb-0">{{ ucfirst($period) }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.export-pdf', request()->query()) }}" class="btn btn-danger"><i class="bi bi-file-pdf me-1"></i>Export PDF</a>
        <a href="{{ route('admin.reports.export-excel', request()->query()) }}" class="btn btn-success"><i class="bi bi-file-excel me-1"></i>Export Excel</a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="period" class="form-select" onchange="this.form.submit()">
                    <option value="day" {{ $period=='day'?'selected':'' }}>Aujourd'hui</option>
                    <option value="week" {{ $period=='week'?'selected':'' }}>Cette semaine</option>
                    <option value="month" {{ $period=='month'?'selected':'' }}>Ce mois</option>
                    <option value="year" {{ $period=='year'?'selected':'' }}>Cette année</option>
                    <option value="custom" {{ $period=='custom'?'selected':'' }}>Personnalisé</option>
                </select>
            </div>
            @if($period==='custom')
            <div class="col-auto"><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
            <div class="col-auto"><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
            <div class="col-auto"><button type="submit" class="btn btn-outline-primary">Appliquer</button></div>
            @endif
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    @php $cards = [
        ['label'=>'Revenus','value'=>number_format($stats['revenue'],0,'.',',').' DT','icon'=>'bi-cash-coin','color'=>'primary'],
        ['label'=>'Dépenses','value'=>number_format($stats['expenses'],0,'.',',').' DT','icon'=>'bi-receipt','color'=>'danger'],
        ['label'=>'Bénéfice','value'=>number_format($stats['profit'],0,'.',',').' DT','icon'=>'bi-graph-up-arrow','color'=>'success'],
        ['label'=>'Réservations','value'=>$stats['total_reservations'],'icon'=>'bi-calendar-check','color'=>'info'],
        ['label'=>'Taux d\'occupation','value'=>$stats['occupancy_rate'].'%','icon'=>'bi-percent','color'=>'warning'],
        ['label'=>'Nuits louées','value'=>$stats['total_nights'],'icon'=>'bi-moon-stars','color'=>'secondary'],
    ]; @endphp
    @foreach($cards as $card)
    <div class="col-md-4 col-6">
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-{{ $card['color'] }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;">
                        <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }} fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                        <div class="fw-bold fs-5">{{ $card['value'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Évolution mensuelle ({{ now()->year }})</h6></div>
            <div class="card-body"><canvas id="monthlyChart" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top logements</h6></div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($stats['top_properties'] as $i => $prop)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $i===0?'warning':($i===1?'secondary':'light text-dark') }} rounded-circle d-flex align-items-center justify-content-center" style="width:24px;height:24px;">{{ $i+1 }}</span>
                            <span class="small fw-semibold">{{ $prop['name'] }}</span>
                        </div>
                        <span class="text-success fw-semibold small">{{ number_format($prop['revenue'],0,'.',',') }} DT</span>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted py-4">Aucune donnée</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-house me-2"></i>Taux d'occupation par logement</h6></div>
            <div class="card-body">
                @foreach($stats['properties_stats'] as $ps)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold">{{ $ps['name'] }}</span>
                        <span class="small text-muted">{{ $ps['occupancy'] }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-primary" style="width:{{ $ps['occupancy'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-table me-2"></i>Résumé par logement</h6></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light"><tr><th>Logement</th><th class="text-center">Rés.</th><th class="text-center">Nuits</th><th class="text-end">Revenus</th></tr></thead>
                    <tbody>
                        @foreach($stats['properties_stats'] as $ps)
                        <tr>
                            <td class="fw-semibold">{{ $ps['name'] }}</td>
                            <td class="text-center">{{ $ps['reservations'] }}</td>
                            <td class="text-center">{{ $ps['nights'] }}</td>
                            <td class="text-end text-success fw-semibold">{{ number_format($ps['revenue'],0,'.',',') }} DT</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const monthlyData = @json($stats['monthly_data'] ?? []);
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.month),
        datasets: [
            { label: 'Revenus', data: monthlyData.map(d => d.revenue), backgroundColor: '#2563eb', borderRadius: 4 },
            { label: 'Dépenses', data: monthlyData.map(d => d.expenses), backgroundColor: '#ef4444', borderRadius: 4 },
            { label: 'Bénéfice', data: monthlyData.map(d => d.profit), type: 'line', borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.1)', fill: true, tension: 0.3 }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
