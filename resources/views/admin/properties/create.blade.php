@extends('layouts.app')

@section('title', 'Ajouter un logement')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">Ajouter un logement</h4>
</div>

<form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Informations générales
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nom du logement *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Ex: Bungalow Jasmin" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Choisir...</option>
                                <option value="bungalow" {{ old('type') === 'bungalow' ? 'selected' : '' }}>🏠 Bungalow</option>
                                <option value="apartment" {{ old('type') === 'apartment' ? 'selected' : '' }}>🏢 Appartement</option>
                                <option value="room" {{ old('type') === 'room' ? 'selected' : '' }}>🛏️ Chambre</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"
                                      placeholder="Décrivez le logement...">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Localisation</label>
                            <input type="text" name="location" class="form-control"
                                   value="{{ old('location') }}" placeholder="Ex: Zone A - Bord de mer">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Surface (m²)</label>
                            <input type="text" name="surface" class="form-control"
                                   value="{{ old('surface') }}" placeholder="Ex: 65">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statut *</label>
                            <select name="status" class="form-select" required>
                                <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Disponible</option>
                                <option value="unavailable" {{ old('status') === 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Capacity & Price -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-currency-exchange me-2 text-primary"></i>Capacité & Tarif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Prix 1 (DT) *</label>
                            <div class="input-group">
                                <input type="number" name="price_per_night" class="form-control @error('price_per_night') is-invalid @enderror"
                                       value="{{ old('price_per_night') }}" min="0" step="0.5" required>
                                <span class="input-group-text">DT</span>
                            </div>
                            @error('price_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prix 2 (DT)</label>
                            <div class="input-group">
                                <input type="number" name="weekend_price" class="form-control @error('weekend_price') is-invalid @enderror"
                                       value="{{ old('weekend_price') }}" min="0" step="0.5">
                                <span class="input-group-text">DT</span>
                            </div>
                            @error('weekend_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prix 3 (DT)</label>
                            <div class="input-group">
                                <input type="number" name="summer_price" class="form-control @error('summer_price') is-invalid @enderror"
                                       value="{{ old('summer_price') }}" min="0" step="0.5">
                                <span class="input-group-text">DT</span>
                            </div>
                            @error('summer_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacité (personnes) *</label>
                            <input type="number" name="capacity" class="form-control"
                                   value="{{ old('capacity', 2) }}" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre de chambres *</label>
                            <input type="number" name="bedrooms" class="form-control"
                                   value="{{ old('bedrooms', 1) }}" min="1" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-stars me-2 text-primary"></i>Équipements & Services
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @php
                            $amenitiesList = ['WiFi', 'Climatisation', 'TV', 'Cuisine équipée', 'Réfrigérateur', 'Micro-ondes',
                                'Lave-linge', 'Parking', 'Terrasse', 'Balcon', 'Jardin', 'BBQ', 'Piscine', 'Minibar',
                                'Sèche-cheveux', 'Coffre-fort', 'Ascenseur', 'Vue mer'];
                            $selectedAmenities = old('amenities', []);
                        @endphp
                        @foreach($amenitiesList as $amenity)
                        <div class="col-sm-6 col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                       id="amenity_{{ $loop->index }}"
                                       class="form-check-input"
                                       {{ in_array($amenity, $selectedAmenities) ? 'checked' : '' }}>
                                <label class="form-check-label" for="amenity_{{ $loop->index }}">{{ $amenity }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Photos -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-images me-2 text-primary"></i>Photos
                </div>
                <div class="card-body">
                    <div id="photo-preview" class="row g-2 mb-3"></div>

                    <label for="photos" class="btn btn-outline-primary w-100">
                        <i class="bi bi-cloud-upload me-1"></i>Ajouter des photos
                    </label>
                    <input type="file" id="photos" name="photos[]" accept="image/*" multiple class="d-none">
                    <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                        JPG, PNG — Max 5 Mo par photo. La 1ère sera la photo principale.
                    </p>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-1"></i>Créer le logement
                </button>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">
                    Annuler
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('photos').addEventListener('change', function(e) {
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    Array.from(e.target.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = (ev) => {
            const div = document.createElement('div');
            div.className = 'col-6';
            div.innerHTML = `
                <div style="position:relative;">
                    <img src="${ev.target.result}" style="width:100%;height:80px;object-fit:cover;border-radius:8px;">
                    ${i === 0 ? '<span class="badge bg-primary" style="position:absolute;top:4px;left:4px;font-size:10px;">Principal</span>' : ''}
                </div>`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
