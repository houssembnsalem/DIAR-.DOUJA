@extends('layouts.app')
@section('title', 'Calendrier')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Calendrier des réservations</h1>
        <p class="text-muted mb-0">Vue globale des disponibilités</p>
    </div>
    <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nouvelle réservation</a>
</div>

<div class="row g-4">
    <div class="col-lg-9">
        <div class="card shadow-sm">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-palette me-2"></i>Légende</h6></div>
            <div class="card-body">
                @php $legend = [
                    ['color'=>'#f59e0b','label'=>'En attente'],
                    ['color'=>'#0ea5e9','label'=>'Confirmée'],
                    ['color'=>'#2563eb','label'=>'En cours'],
                    ['color'=>'#16a34a','label'=>'Terminée'],
                    ['color'=>'#dc2626','label'=>'Annulée'],
                ]; @endphp
                @foreach($legend as $item)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded" style="width:16px;height:16px;background:{{ $item['color'] }};flex-shrink:0;"></div>
                    <span class="small">{{ $item['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtrer par logement</h6></div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <label class="list-group-item d-flex gap-2 align-items-center cursor-pointer">
                        <input class="form-check-input" type="checkbox" id="filterAll" checked>
                        <span class="small fw-semibold">Tous les logements</span>
                    </label>
                    @foreach($properties as $prop)
                    <label class="list-group-item d-flex gap-2 align-items-center cursor-pointer">
                        <input class="form-check-input property-filter" type="checkbox" value="{{ $prop->id }}" checked>
                        <span class="small">{{ $prop->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Sélection</h6></div>
            <div class="card-body" id="eventDetail">
                <p class="text-muted small mb-0">Cliquez sur une réservation pour voir les détails.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Détails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" id="modalViewBtn" class="btn btn-primary">Voir la réservation</a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const filterAll = document.getElementById('filterAll');
    const propertyFilters = document.querySelectorAll('.property-filter');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: '{{ app()->getLocale() }}',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: function(info, successCallback, failureCallback) {
            const ids = Array.from(document.querySelectorAll('.property-filter:checked')).map(cb => cb.value);
            
            // Build URL with parameters
            let url = new URL('{{ route("admin.calendar.events") }}', window.location.origin);
            url.searchParams.append('start', info.startStr);
            url.searchParams.append('end', info.endStr);
            url.searchParams.append('filtering', '1'); // Flag to indicate filtering is active
            
            // Add property IDs as property_ids[]
            ids.forEach(id => url.searchParams.append('property_ids[]', id));

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            const e = info.event;
            const p = e.extendedProps;
            document.getElementById('modalTitle').textContent = e.title;
            document.getElementById('modalBody').innerHTML = `
                <div class="mb-2"><strong>Logement:</strong> ${p.property || '—'}</div>
                <div class="mb-2"><strong>Client:</strong> ${p.client || '—'}</div>
                <div class="mb-2"><strong>Arrivée:</strong> ${new Date(e.start).toLocaleDateString('fr-FR')}</div>
                <div class="mb-2"><strong>Départ:</strong> ${new Date(e.end).toLocaleDateString('fr-FR')}</div>
                <div class="mb-2"><strong>Nuits:</strong> ${p.nights}</div>
                <div class="mb-2"><strong>Montant:</strong> <span class="text-success fw-bold">${p.amount} DT</span></div>
                <div class="mb-2"><strong>Statut:</strong> <span class="badge" style="background:${e.backgroundColor}">${p.status}</span></div>
            `;
            document.getElementById('modalViewBtn').href = `/admin/reservations/${p.id}`;
            new bootstrap.Modal(document.getElementById('eventModal')).show();
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        }
    });
    calendar.render();

    // Select/Deselect All
    filterAll.addEventListener('change', function() {
        propertyFilters.forEach(cb => {
            cb.checked = this.checked;
        });
        calendar.refetchEvents();
    });

    // Individual filters
    propertyFilters.forEach(cb => {
        cb.addEventListener('change', function() {
            // Update filterAll state
            const allChecked = Array.from(propertyFilters).every(c => c.checked);
            const noneChecked = Array.from(propertyFilters).every(c => !c.checked);
            
            filterAll.checked = allChecked;
            filterAll.indeterminate = !allChecked && !noneChecked;
            
            calendar.refetchEvents();
        });
    });
});
</script>
@endpush
