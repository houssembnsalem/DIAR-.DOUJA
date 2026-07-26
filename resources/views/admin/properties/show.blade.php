@extends('layouts.app')

@section('title', $property->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">{{ $property->name }}</h4>
            <span class="text-muted">{{ $property->getTypeLabel() }} • {{ $property->location }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reservations.create', ['property_id' => $property->id]) }}" class="btn btn-success">
            <i class="bi bi-calendar-plus me-1"></i>Réserver
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <form method="POST" action="{{ route('admin.properties.destroy', $property) }}" onsubmit="return confirm('Supprimer ce logement?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Photos -->
        <div class="card mb-4">
            <div class="card-body p-0">
                @if($property->photos->isNotEmpty())
                <div id="photoCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($property->photos as $i => $photo)
                        <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $photo->path) }}"
                                 style="width:100%;height:350px;object-fit:cover;border-radius:12px;">
                        </div>
                        @endforeach
                    </div>
                    @if($property->photos->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    @endif
                </div>
                @else
                <div style="height:250px;background:#f1f5f9;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:60px;">
                    @if($property->type === 'bungalow') 🏠 @elseif($property->type === 'apartment') 🏢 @else 🛏️ @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-text-paragraph me-2 text-primary"></i>Description</div>
            <div class="card-body">
                <p class="mb-0">{{ $property->description ?? 'Aucune description.' }}</p>

                <hr>
                <div class="row g-3">
                    <div class="col-6 col-md-3 text-center">
                        <div style="font-size:28px;">👥</div>
                        <div class="fw-bold">{{ $property->capacity }}</div>
                        <div class="text-muted small">Personnes</div>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <div style="font-size:28px;">🛏️</div>
                        <div class="fw-bold">{{ $property->bedrooms }}</div>
                        <div class="text-muted small">Chambres</div>
                    </div>
                    @if($property->surface)
                    <div class="col-6 col-md-3 text-center">
                        <div style="font-size:28px;">📐</div>
                        <div class="fw-bold">{{ $property->surface }} m²</div>
                        <div class="text-muted small">Surface</div>
                    </div>
                    @endif
                    <div class="col-6 col-md-3 text-center">
                        <div style="font-size:28px;">💰</div>
                        <div class="fw-bold">{{ number_format($property->price_per_night, 2) }} DT</div>
                        <div class="text-muted small mb-1">Prix 1</div>
                        @if($property->weekend_price)
                            <div class="badge bg-soft-primary text-primary border-primary border mt-1" style="font-size: 11px;">
                                Prix 2: {{ number_format($property->weekend_price, 0) }} DT
                            </div>
                        @endif
                        @if($property->summer_price)
                            <div class="badge bg-soft-warning text-warning border-warning border mt-1" style="font-size: 11px;">
                                Prix 3: {{ number_format($property->summer_price, 0) }} DT
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenities -->
        @if($property->amenities)
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-stars me-2 text-primary"></i>Équipements</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @php 
                        $propAmenities = $property->amenities;
                        if (is_string($propAmenities)) $propAmenities = json_decode($propAmenities, true) ?: [];
                        if (!is_array($propAmenities)) $propAmenities = [];
                    @endphp
                    @foreach($propAmenities as $amenity)
                    <span class="badge bg-light text-dark border px-3 py-2" style="font-size:13px;">✓ {{ $amenity }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Reservations -->
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-journal-check me-2 text-primary"></i>Réservations récentes</span>
                <a href="{{ route('admin.reservations.create', ['property_id' => $property->id]) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg me-1"></i>Nouvelle
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($property->reservations->take(10) as $res)
                        <tr>
                            <td>
                                <a href="{{ route('admin.reservations.show', $res) }}" class="text-decoration-none">
                                    {{ $res->client->full_name }}
                                </a>
                            </td>
                            <td>{{ $res->check_in->format('d/m/Y') }}</td>
                            <td>{{ $res->check_out->format('d/m/Y') }}</td>
                            <td>{{ number_format($res->final_amount, 2) }} DT</td>
                            <td><span class="badge bg-{{ $res->getStatusBadge() }}">{{ $res->getStatusLabel() }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Aucune réservation</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <span class="badge bg-{{ $property->getStatusBadge() }} px-4 py-2 mb-3" style="font-size:15px;">
                    {{ $property->getStatusLabel() }}
                </span>
                <div class="d-grid gap-2">
                    @if(auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('admin.properties.toggle-status', $property) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-toggle-on me-1"></i>
                            {{ $property->status === 'available' ? 'Marquer indisponible' : 'Marquer disponible' }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-graph-up me-2"></i>Statistiques du mois</div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Taux d'occupation</span>
                    <strong>{{ $occupancyRate }}%</strong>
                </div>
                <div class="progress mb-3" style="height:8px;">
                    <div class="progress-bar bg-primary" style="width:{{ $occupancyRate }}%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Revenus du mois</span>
                    <strong class="text-success">{{ number_format($monthlyRevenue, 2) }} DT</strong>
                </div>
            </div>
        </div>

        <!-- Quick Reserve -->
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-check me-2 text-success"></i>Vérifier disponibilité</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Arrivée</label>
                    <input type="date" id="checkIn" class="form-control" min="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Départ</label>
                    <input type="date" id="checkOut" class="form-control" min="{{ date('Y-m-d') }}">
                </div>
                <div id="availabilityResult"></div>
                <button onclick="checkAvailability()" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Vérifier
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkAvailability() {
    const checkIn = document.getElementById('checkIn').value;
    const checkOut = document.getElementById('checkOut').value;
    const resultDiv = document.getElementById('availabilityResult');

    if (!checkIn || !checkOut) {
        resultDiv.innerHTML = '<div class="alert alert-warning py-2">Sélectionnez les deux dates.</div>';
        return;
    }

    fetch(`/api/properties/availability?check_in=${checkIn}&check_out=${checkOut}&property_id={{ $property->id }}`)
        .then(r => r.json())
        .then(data => {
            const isAvailable = data.some(p => p.id === {{ $property->id }});
            if (isAvailable) {
                const nights = Math.round((new Date(checkOut) - new Date(checkIn)) / 86400000);
                const total = nights * {{ $property->price_per_night }};
                resultDiv.innerHTML = `
                    <div class="alert alert-success py-2 mb-3">
                        ✅ Disponible! ${nights} nuits = ${total.toFixed(2)} DT
                    </div>
                    <a href="/admin/reservations/create?property_id={{ $property->id }}&check_in=${checkIn}&check_out=${checkOut}"
                       class="btn btn-success w-100 mb-2">
                        <i class="bi bi-calendar-plus me-1"></i>Créer la réservation
                    </a>`;
            } else {
                resultDiv.innerHTML = '<div class="alert alert-danger py-2 mb-3">❌ Non disponible pour ces dates.</div>';
            }
        });
}
</script>
@endpush
