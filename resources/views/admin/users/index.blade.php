@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Utilisateurs</h1>
        <p class="text-muted mb-0">Gestion des accès</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Nouvel utilisateur</a>
</div>

<div class="row g-4">
    @foreach($users as $user)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle bg-{{ $user->role==='admin'?'primary':'info' }} text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width:60px;height:60px;font-size:22px;">
                    {{ strtoupper(substr($user->name,0,2)) }}
                </div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <div class="text-muted small mb-2">{{ $user->email }}</div>
                <span class="badge bg-{{ $user->role==='admin'?'primary':'info' }} mb-3">{{ $user->getRoleLabel() }}</span>
                @if(!$user->is_active)<span class="badge bg-danger mb-3 ms-1">Inactif</span>@endif
                <div class="d-flex gap-2 justify-content-center">
                    @if($user->id !== auth()->id())
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @else
                    <span class="badge bg-light text-dark">Vous</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
