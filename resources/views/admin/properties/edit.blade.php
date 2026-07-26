@extends('layouts.app')
@section('title', 'Modifier le logement')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Modifier : {{ $property->name }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.properties.index') }}">Logements</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol></nav>
    </div>
    <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<form action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informations générales</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nom du logement <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $property->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="bungalow" {{ old('type',$property->type)=='bungalow'?'selected':'' }}>🏡 Bungalow</option>
                            <option value="apartment" {{ old('type',$property->type)=='apartment'?'selected':'' }}>🏢 Appartement</option>
                            <option value="room" {{ old('type',$property->type)=='room'?'selected':'' }}>🛏️ Chambre</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $property->description) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Prix 1 (DT) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="price_per_night" class="form-control @error('price_per_night') is-invalid @enderror" value="{{ old('price_per_night', $property->price_per_night) }}" step="0.5" min="0" required>
                            <span class="input-group-text">DT</span>
                        </div>
                        @error('price_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Prix 2 (DT)</label>
                        <div class="input-group">
                            <input type="number" name="weekend_price" class="form-control @error('weekend_price') is-invalid @enderror" value="{{ old('weekend_price', $property->weekend_price) }}" step="0.5" min="0">
                            <span class="input-group-text">DT</span>
                        </div>
                        @error('weekend_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Prix 3 (DT)</label>
                        <div class="input-group">
                            <input type="number" name="summer_price" class="form-control @error('summer_price') is-invalid @enderror" value="{{ old('summer_price', $property->summer_price) }}" step="0.5" min="0">
                            <span class="input-group-text">DT</span>
                        </div>
                        @error('summer_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Capacité (personnes)</label>
                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $property->capacity) }}" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nombre de chambres</label>
                        <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms', $property->bedrooms) }}" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="status" class="form-select">
                            <option value="available" {{ old('status',$property->status)=='available'?'selected':'' }}>✅ Disponible</option>
                            <option value="unavailable" {{ old('status',$property->status)=='unavailable'?'selected':'' }}>❌ Indisponible</option>
                            <option value="maintenance" {{ old('status',$property->status)=='maintenance'?'selected':'' }}>🔧 Maintenance</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-check2-square me-2"></i>Équipements</h6></div>
            <div class="card-body">
                @php 
                    $amenities = old('amenities', $property->amenities ?? []); 
                    if (is_string($amenities)) $amenities = json_decode($amenities, true) ?: [];
                    if (!is_array($amenities)) $amenities = [];
                @endphp
                <div class="row g-2">
                    @foreach(['WiFi','Climatisation','Piscine','Parking','Cuisine équipée','TV','Terrasse','Barbecue','Jacuzzi','Vue mer','Animaux acceptés','Petit-déjeuner'] as $amenity)
                    <div class="col-md-4 col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity }}" id="am_{{ Str::slug($amenity) }}" {{ in_array($amenity, $amenities) ? 'checked' : '' }}>
                            <label class="form-check-label" for="am_{{ Str::slug($amenity) }}">{{ $amenity }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-images me-2"></i>Ajouter des photos</h6></div>
            <div class="card-body">
                @if($property->photos->count())
                <h6 class="text-muted mb-3">Photos actuelles</h6>
                <div class="row g-2 mb-4">
                    @foreach($property->photos as $photo)
                    <div class="col-md-3 col-4" id="photo-{{ $photo->id }}">
                        <div class="position-relative">
                            <img src="{{ asset('storage/'.$photo->path) }}" class="img-fluid rounded" style="height:100px;width:100%;object-fit:cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-1" onclick="deletePhoto({{ $photo->id }})" style="width:24px;height:24px;line-height:1;">
                                <i class="bi bi-x" style="font-size:12px;"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <input type="file" name="photos[]" class="form-control" multiple accept="image/*" onchange="previewPhotos(this)">
                <div class="form-text">JPG, PNG, WebP — max 2MB par photo</div>
                <div id="preview-container" class="row g-2 mt-2"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm sticky-top" style="top:80px;">
            <div class="card-body text-center p-4">
                <i class="bi bi-house-check text-primary" style="font-size:3rem;"></i>
                <h5 class="mt-3">Enregistrer les modifications</h5>
                <p class="text-muted small">Vérifiez les informations avant de sauvegarder.</p>
                <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-check-lg me-2"></i>Enregistrer</button>
                <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-outline-secondary w-100">Annuler</a>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
@push('scripts')
<script>
function previewPhotos(input) {
    const container = document.getElementById('preview-container');
    container.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            container.innerHTML += `<div class="col-md-3 col-4"><img src="${e.target.result}" class="img-fluid rounded" style="height:80px;width:100%;object-fit:cover;"></div>`;
        };
        reader.readAsDataURL(file);
    });
}
function deletePhoto(id) {
    if(!confirm('Supprimer cette photo ?')) return;
    fetch(`/admin/properties/photos/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})
        .then(r=>r.json()).then(d=>{ if(d.success) document.getElementById('photo-'+id)?.remove(); });
}
</script>
@endpush
