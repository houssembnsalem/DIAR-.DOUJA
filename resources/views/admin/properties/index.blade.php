@extends('layouts.app')

@section('title', 'Logements')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Logements</h4>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Ajouter un logement
    </a>
    @endif
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">Tous les types</option>
                    <option value="bungalow" {{ request('type') === 'bungalow' ? 'selected' : '' }}>Bungalows</option>
                    <option value="apartment" {{ request('type') === 'apartment' ? 'selected' : '' }}>Appartements</option>
                    <option value="room" {{ request('type') === 'room' ? 'selected' : '' }}>Chambres</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Disponible</option>
                    <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Property Grid -->
<div class="row g-3">
    @forelse($properties as $property)
    <div class="col-sm-6 col-xl-4">
        <div class="property-card bg-white h-100">
            <!-- Photo -->
            @if($property->photos->first())
                <img src="{{ asset('storage/' . $property->photos->first()->path) }}"
                     alt="{{ $property->name }}" class="property-img">
            @else
                <div class="property-img-placeholder">
                    @if($property->type === 'bungalow') 🏠
                    @elseif($property->type === 'apartment') 🏢
                    @else 🛏️
                    @endif
                </div>
            @endif

            <div class="p-3">
                <!-- Header -->
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <h6 class="fw-bold mb-0">{{ $property->name }}</h6>
                        <span class="badge bg-light text-dark border" style="font-size:11px;">
                            {{ $property->getTypeLabel() }}
                        </span>
                    </div>
                    <span class="badge bg-{{ $property->getStatusBadge() }}">{{ $property->getStatusLabel() }}</span>
                </div>

                <!-- Details -->
                <div class="d-flex gap-3 mb-3" style="font-size:13px; color:#64748b;">
                    <span><i class="bi bi-people me-1"></i>{{ $property->capacity }} pers.</span>
                    <span><i class="bi bi-door-closed me-1"></i>{{ $property->bedrooms }} ch.</span>
                    @if($property->surface)
                    <span><i class="bi bi-arrows-angle-expand me-1"></i>{{ $property->surface }} m²</span>
                    @endif
                </div>

                @if($property->location)
                <div style="font-size:12px; color:#94a3b8;" class="mb-2">
                    <i class="bi bi-geo-alt me-1"></i>{{ $property->location }}
                </div>
                @endif

                <!-- Amenities -->
                @php
                    $amenities = is_array($property->amenities) ? $property->amenities : (is_string($property->amenities) ? json_decode($property->amenities, true) : []);
                @endphp
                @if($amenities && is_array($amenities) && count($amenities) > 0)
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach(array_slice($amenities, 0, 3) as $amenity)
                    <span class="badge bg-light text-secondary border" style="font-size:11px;">{{ $amenity }}</span>
                    @endforeach
                    @if(count($amenities) > 3)
                    <span class="badge bg-light text-secondary border" style="font-size:11px;">+{{ count($amenities) - 3 }}</span>
                    @endif
                </div>
                @endif

                <!-- Price & Actions -->
                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <div>
                        <span style="font-size:20px; font-weight:700; color:#2563eb;">{{ number_format($property->price_per_night, 0) }}</span>
                        <span style="font-size:12px; color:#94a3b8;"> DT (Prix 1)</span>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.reservations.create', ['property_id' => $property->id]) }}"
                           class="btn btn-sm btn-success" title="Réserver">
                            <i class="bi bi-calendar-plus"></i>
                        </a>
                        <a href="{{ route('admin.properties.show', $property) }}"
                           class="btn btn-sm btn-outline-primary" title="Voir">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.properties.edit', $property) }}"
                           class="btn btn-sm btn-outline-secondary" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer"
                            onclick="confirmDelete('{{ route('admin.properties.destroy', $property) }}', '{{ addslashes($property->name) }}')">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="bi bi-building" style="font-size:48px; color:#cbd5e1;"></i>
            <h5 class="mt-3 text-muted">Aucun logement trouvé</h5>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.properties.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-lg me-1"></i>Ajouter le premier logement
            </a>
            @endif
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $properties->withQueryString()->links() }}
</div>
{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">

      <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#fee2e2,#fecaca); padding:24px 24px 12px;">
        <div class="d-flex align-items-center gap-3">
          <div style="width:48px;height:48px;background:#ef4444;border-radius:12px;display:flex;align-items:center;justify-content:center;">
            <i class="bi bi-trash3-fill text-white" style="font-size:20px;"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="deleteModalLabel" style="color:#991b1b;">Supprimer le logement</h5>
            <p class="mb-0" style="font-size:13px;color:#b91c1c;">Cette action est irréversible</p>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <div class="modal-body" style="padding:24px;">
        <p style="color:#374151;font-size:15px;" class="mb-1">Vous êtes sur le point de supprimer :</p>
        <p class="fw-bold mb-3" style="font-size:16px;color:#111827;" id="deletePropertyName"></p>
        <div class="rounded-3 p-3 d-flex align-items-start gap-2" style="background:#fef3c7;">
          <i class="bi bi-exclamation-triangle-fill text-warning mt-1"></i>
          <span style="font-size:13px;color:#92400e;">Toutes les données associées à ce logement (photos, réservations liées) seront définitivement perdues.</span>
        </div>
      </div>

      <div class="modal-footer border-0" style="padding:16px 24px 24px; gap:8px;">
        <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal"
          style="border-radius:10px;">
          <i class="bi bi-x-lg me-1"></i>Annuler
        </button>
        <form id="deleteForm" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger fw-medium px-4" style="border-radius:10px;">
            <i class="bi bi-trash3 me-1"></i>Oui, supprimer
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
function confirmDelete(action, name) {
    document.getElementById('deleteForm').action = action;
    document.getElementById('deletePropertyName').textContent = '"' + name + '"';
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush

@endsection
