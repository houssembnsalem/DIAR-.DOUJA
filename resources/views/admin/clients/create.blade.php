@extends('layouts.app')
@section('title', 'Nouveau client')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Nouveau client</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active">Nouveau</li>
        </ol></nav>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<form action="{{ route('admin.clients.store') }}" method="POST">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-person me-2"></i>Informations du client</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CIN / Passeport</label>
                        <input type="text" name="cin" class="form-control" value="{{ old('cin') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nationalité</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', 'Tunisienne') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Adresse</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body text-center p-4">
                <i class="bi bi-person-circle text-primary" style="font-size:3rem;"></i>
                <h5 class="mt-3">Enregistrer</h5>
                <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-check-lg me-2"></i>Créer le client</button>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary w-100">Annuler</a>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
