@extends('layouts.app')
@section('title', 'Modifier le client')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Modifier : {{ $client->full_name }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.show', $client) }}">{{ $client->full_name }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol></nav>
    </div>
    <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<form action="{{ route('admin.clients.update', $client) }}" method="POST">
@csrf @method('PUT')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-person me-2"></i>Informations du client</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $client->first_name) }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $client->last_name) }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $client->phone) }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CIN / Passeport</label>
                        <input type="text" name="cin" class="form-control" value="{{ old('cin', $client->cin) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nationalité</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $client->nationality) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Adresse</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $client->address) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $client->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width:64px;height:64px;font-size:22px;">
                    {{ strtoupper(substr($client->first_name,0,1).substr($client->last_name,0,1)) }}
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-check-lg me-2"></i>Enregistrer</button>
                <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-secondary w-100">Annuler</a>
            </div>
        </div>
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <h6 class="text-danger mb-2"><i class="bi bi-exclamation-triangle me-1"></i>Zone dangereuse</h6>
                <p class="text-muted small">La suppression est irréversible.</p>
                <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce client ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-trash me-1"></i>Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
