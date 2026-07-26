@extends('layouts.app')
@section('title', 'Modifier l\'utilisateur')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Modifier : {{ $user->name }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol></nav>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-person me-2"></i>Modifier les informations</h6></div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nouveau mot de passe <small class="text-muted">(laisser vide pour ne pas changer)</small></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Confirmer</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rôle</label>
                        <select name="role" class="form-select">
                            <option value="admin" {{ old('role',$user->role)==='admin'?'selected':'' }}>🔑 Admin</option>
                            <option value="assistant" {{ old('role',$user->role)==='assistant'?'selected':'' }}>👩‍💼 Assistante</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active',$user->is_active)?'selected':'' }}>✅ Actif</option>
                            <option value="0" {{ !old('is_active',$user->is_active)?'selected':'' }}>❌ Inactif</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Enregistrer</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
