@extends('layouts.app')
@section('title', 'Finances')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Finances</h1>
        <p class="text-muted mb-0">{{ ucfirst($period) }} — {{ now()->format('Y') }}</p>
    </div>
    <a href="{{ route('admin.finances.create-expense') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouvelle dépense</a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <select name="period" class="form-select" onchange="this.form.submit()">
                    <option value="month" {{ $period=='month'?'selected':'' }}>Ce mois</option>
                    <option value="quarter" {{ $period=='quarter'?'selected':'' }}>Ce trimestre</option>
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

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0" style="background:linear-gradient(135deg,#2563eb,#3b82f6);">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="opacity-75 small mb-1">Revenus</div>
                        <div class="fs-2 fw-bold">{{ number_format($stats['revenue'],0,'.',',') }} DT</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded p-2"><i class="bi bi-arrow-up-circle fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0" style="background:linear-gradient(135deg,#dc2626,#ef4444);">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="opacity-75 small mb-1">Dépenses</div>
                        <div class="fs-2 fw-bold">{{ number_format($stats['expenses'],0,'.',',') }} DT</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded p-2"><i class="bi bi-arrow-down-circle fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0" style="background:linear-gradient(135deg,#16a34a,#22c55e);">
            <div class="card-body text-white p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="opacity-75 small mb-1">Bénéfice net</div>
                        <div class="fs-2 fw-bold">{{ number_format($stats['profit'],0,'.',',') }} DT</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded p-2"><i class="bi bi-graph-up-arrow fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Revenus par logement</h6></div>
            <div class="card-body">
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Dépenses par catégorie</h6></div>
            <div class="card-body">
                <canvas id="expenseChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-0">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Paiements récents</h6></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Réservation</th><th>Méthode</th><th class="text-end">Montant</th></tr></thead>
                    <tbody>
                        @forelse($recentPayments as $p)
                        <tr>
                            <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                            <td><a href="{{ route('admin.reservations.show', $p->reservation) }}" class="text-decoration-none">{{ $p->reservation->number }}</a></td>
                            <td>{{ $p->getMethodLabel() }}</td>
                            <td class="text-end fw-semibold text-success">+{{ number_format($p->amount,0,'.',',') }} DT</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Aucun paiement</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Dépenses récentes</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Description</th><th>Catégorie</th><th class="text-end">Montant</th><th></th></tr></thead>
                    <tbody>
                        @forelse($recentExpenses as $e)
                        <tr>
                            <td>{{ $e->expense_date->format('d/m/Y') }}</td>
                            <td>{{ Str::limit($e->description,30) }}</td>
                            <td><span class="badge bg-light text-dark">{{ $e->category->name ?? '—' }}</span></td>
                            <td class="text-end fw-semibold text-danger">-{{ number_format($e->amount,0,'.',',') }} DT</td>
                            <td>
                                <form action="{{ route('admin.finances.expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger p-1" title="Supprimer"><i class="bi bi-trash" style="font-size:12px;"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">Aucune dépense</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const revenueByProperty = @json($revenueByProperty);
const expensesByCategory = @json($expensesByCategory);

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: revenueByProperty.map(r => r.name),
        datasets: [{
            label: 'Revenus (DT)',
            data: revenueByProperty.map(r => r.revenue),
            backgroundColor: '#2563eb',
            borderRadius: 6,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

const colors = ['#2563eb','#16a34a','#dc2626','#d97706','#7c3aed','#0891b2','#be185d'];
new Chart(document.getElementById('expenseChart'), {
    type: 'doughnut',
    data: {
        labels: expensesByCategory.map(e => e.name),
        datasets: [{
            data: expensesByCategory.map(e => e.total),
            backgroundColor: colors.slice(0, expensesByCategory.length),
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
