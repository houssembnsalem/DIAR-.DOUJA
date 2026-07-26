@extends('layouts.app')

@section('title', 'Gestion des Services')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Liste des Services</h5>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.services.index') }}" method="GET" class="d-flex" style="max-width: 250px;">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Filtrer..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="bi bi-plus-lg me-1"></i>Ajouter un Service
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nom du Service</th>
                            <th>Prix</th>
                            <th>Unité</th>
                            <th>Description</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $service->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ number_format($service->price, 2) }} DT</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $service->unit }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($service->description, 60) }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-light border-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $service->id }}" title="Modifier">
                                            <i class="bi bi-pencil text-primary"></i>
                                        </button>
                                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light border-0" title="Supprimer">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modifier le Service</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.services.update', $service) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nom du service</label>
                                                            <input type="text" class="form-control" name="name" value="{{ $service->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Prix (DT)</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" class="form-control" name="price" value="{{ $service->price }}" required>
                                                                <span class="input-group-text">DT</span>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Unité / Base de calcul</label>
                                                            <select class="form-select" name="unit" required>
                                                                <option value="Par Personne" {{ $service->unit == 'Par Personne' ? 'selected' : '' }}>Par Personne</option>
                                                                <option value="Par Jour" {{ $service->unit == 'Par Jour' ? 'selected' : '' }}>Par Jour</option>
                                                                <option value="Unitaire" {{ $service->unit == 'Unitaire' ? 'selected' : '' }}>Unitaire (Fixe)</option>
                                                                <option value="Par Nuit" {{ $service->unit == 'Par Nuit' ? 'selected' : '' }}>Par Nuit</option>
                                                                <option value="Sur mesure" {{ $service->unit == 'Sur mesure' ? 'selected' : '' }}>Sur mesure</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Description</label>
                                                            <textarea class="form-control" name="description" rows="3">{{ $service->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-3"><i class="bi bi-info-circle fs-1 opacity-25"></i></div>
                                    Aucun service trouvé. Cliquez sur "Ajouter un Service" pour commencer.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($services->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Nouveau Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.services.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nom du service</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: Petit Déjeuner">
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label fw-bold">Prix (DT)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required placeholder="0.00">
                            <span class="input-group-text">DT</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="unit" class="form-label fw-bold">Unité / Base de calcul</label>
                        <select class="form-select" id="unit" name="unit" required>
                            <option value="Par Personne">Par Personne</option>
                            <option value="Par Jour">Par Jour</option>
                            <option value="Unitaire" selected>Unitaire (Fixe)</option>
                            <option value="Par Nuit">Par Nuit</option>
                            <option value="Sur mesure">Sur mesure</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description (Optionnel)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Détails supplémentaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer le service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
