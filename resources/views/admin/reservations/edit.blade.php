@extends('layouts.app')
@section('title', 'Modifier la réservation')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Modifier {{ $reservation->number }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.reservations.index') }}">Réservations</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol></nav>
    </div>
    <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
</div>

<form action="{{ route('admin.reservations.update', $reservation) }}" method="POST">
@csrf @method('PUT')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-house me-2"></i>Logement & Dates</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Logement <span class="text-danger">*</span></label>
                        <select name="property_id" class="form-select @error('property_id') is-invalid @enderror" id="propertySelect" required>
                            @foreach($properties as $p)
                            <option value="{{ $p->id }}" 
                                    data-price="{{ $p->price_per_night }}" 
                                    data-price2="{{ $p->weekend_price }}" 
                                    data-price3="{{ $p->summer_price }}" 
                                    {{ old('property_id',$reservation->property_id)==$p->id?'selected':'' }}>
                                {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('property_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4" id="priceSelectionRow" style="display: none;">
                        <label class="form-label fw-semibold">Tarif à appliquer <span class="text-danger">*</span></label>
                        <select name="applied_price" id="appliedPriceSelect" class="form-select border-primary fw-bold" required>
                            <!-- Options populated by JS -->
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Arrivée <span class="text-danger">*</span></label>
                        <input type="date" name="check_in" id="checkIn" class="form-control @error('check_in') is-invalid @enderror" value="{{ old('check_in', $reservation->check_in->format('Y-m-d')) }}" required>
                        @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Départ <span class="text-danger">*</span></label>
                        <input type="date" name="check_out" id="checkOut" class="form-control @error('check_out') is-invalid @enderror" value="{{ old('check_out', $reservation->check_out->format('Y-m-d')) }}" required>
                        @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre de personnes</label>
                        <input type="number" name="guests_count" class="form-control" value="{{ old('guests_count', $reservation->guests_count) }}" min="1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Statut</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ old('status',$reservation->status)=='pending'?'selected':'' }}>En attente</option>
                            <option value="confirmed" {{ old('status',$reservation->status)=='confirmed'?'selected':'' }}>Confirmée</option>
                            <option value="checked_in" {{ old('status',$reservation->status)=='checked_in'?'selected':'' }}>En cours</option>
                            <option value="checked_out" {{ old('status',$reservation->status)=='checked_out'?'selected':'' }}>Terminée</option>
                            <option value="cancelled" {{ old('status',$reservation->status)=='cancelled'?'selected':'' }}>Annulée</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $reservation->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-patch-plus me-2 text-primary"></i>Services additionnels</h6>
                <button type="button" class="btn btn-sm btn-primary" id="addServiceBtn">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
            <div class="card-body">
                <div id="servicesContainer">
                    @foreach($reservation->services as $index => $rs)
                    <div class="service-line mb-3 pb-3 border-bottom">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small">Service</label>
                                <select name="services[{{ $index }}][service_id]" class="form-select form-select-sm service-select" required>
                                    <option value="">Choisir...</option>
                                    @foreach($services as $s)
                                    <option value="{{ $s->id }}" data-price="{{ $s->price }}" {{ $rs->service_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">QTE</label>
                                <input type="number" name="services[{{ $index }}][quantity]" class="form-control form-control-sm qty-input" value="{{ $rs->quantity }}" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">PERIODE</label>
                                <input type="number" name="services[{{ $index }}][period]" class="form-control form-control-sm period-input" value="{{ $rs->period }}" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <div class="small text-muted mb-1">Total</div>
                                <div class="fw-bold line-total">{{ number_format($rs->total, 2) }} DT</div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-service-btn">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div id="noServicesMsg" class="text-center py-2 text-muted small" style="{{ $reservation->services->count() > 0 ? 'display:none;' : '' }}">
                    Aucun service additionnel sélectionné.
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-person me-2"></i>Client</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Client existant</label>
                        <select name="client_id" class="form-select">
                            <option value="">— Sélectionner un client —</option>
                            @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('client_id',$reservation->client_id)==$c->id?'selected':'' }}>{{ $c->full_name }} — {{ $c->phone }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm bg-primary text-white mb-4">
            <div class="card-body p-4 text-center">
                <div class="fs-1 fw-bold" id="totalAmount">{{ number_format($reservation->total_amount,0,'.',',') }} DT</div>
                <div class="opacity-75"><span id="nightsCount">{{ $reservation->nights }}</span> nuit(s)</div>
                <hr class="border-white opacity-25">
                <div class="d-flex justify-content-between small"><span>Payé</span><span class="fw-bold">{{ number_format($reservation->amount_paid,0,'.',',') }} DT</span></div>
                <div class="d-flex justify-content-between small"><span>Reste</span><span class="fw-bold">{{ number_format($reservation->amount_remaining,0,'.',',') }} DT</span></div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Montant total (DT)</label>
                    <input type="number" name="total_amount" id="totalAmountInput" class="form-control" value="{{ old('total_amount', $reservation->total_amount) }}" step="0.01" min="0">
                    <div class="form-text">Laisser vide pour calcul auto</div>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg me-2"></i>Enregistrer</button>
                <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-outline-secondary w-100 mt-2">Annuler</a>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
@push('scripts')
<script>
const servicesData = @json($services);
const servicesContainer = document.getElementById('servicesContainer');
const noServicesMsg = document.getElementById('noServicesMsg');
const addServiceBtn = document.getElementById('addServiceBtn');
let serviceLineIndex = {{ $reservation->services->count() }};

function createServiceLine() {
    const index = serviceLineIndex++;
    const div = document.createElement('div');
    div.className = 'service-line mb-3 pb-3 border-bottom';
    div.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small">Service</label>
                <select name="services[${index}][service_id]" class="form-select form-select-sm service-select" required>
                    <option value="">Choisir...</option>
                    ${servicesData.map(s => `<option value="${s.id}" data-price="${s.price}">${s.name}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">QTE</label>
                <input type="number" name="services[${index}][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small">PERIODE</label>
                <input type="number" name="services[${index}][period]" class="form-control form-control-sm period-input" value="1" min="1" required>
            </div>
            <div class="col-md-2">
                <div class="small text-muted mb-1">Total</div>
                <div class="fw-bold line-total">0.00 DT</div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-service-btn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;

    servicesContainer.appendChild(div);
    noServicesMsg.style.display = 'none';

    bindEvents(div);
    calc();
}

function bindEvents(line) {
    const select = line.querySelector('.service-select');
    const qty = line.querySelector('.qty-input');
    const period = line.querySelector('.period-input');
    const removeBtn = line.querySelector('.remove-service-btn');

    [select, qty, period].forEach(el => {
        el.addEventListener('change', calc);
        el.addEventListener('input', calc);
    });

    removeBtn.addEventListener('click', () => {
        line.remove();
        if (servicesContainer.children.length === 0) {
            noServicesMsg.style.display = 'block';
        }
        calc();
    });
}

// Bind existing lines
document.querySelectorAll('.service-line').forEach(line => bindEvents(line));

addServiceBtn.addEventListener('click', createServiceLine);

const propSelect = document.getElementById('propertySelect');
const priceSelect = document.getElementById('appliedPriceSelect');
const checkInInput = document.getElementById('checkIn');
const checkOutInput = document.getElementById('checkOut');

const initialAppliedPrice = parseFloat("{{ $reservation->price_per_night }}") || 0;

function updateAppliedPriceDropdown() {
    if (!propSelect.value) {
        document.getElementById('priceSelectionRow').style.display = 'none';
        return;
    }

    const option = propSelect.options[propSelect.selectedIndex];
    const p1 = parseFloat(option.dataset.price) || 0;
    const p2 = parseFloat(option.dataset.price2) || 0;
    const p3 = parseFloat(option.dataset.price3) || 0;
    const checkIn = checkInInput.value;

    // Populate price dropdown if changed or empty
    if (priceSelect.dataset.currentProp !== propSelect.value) {
        priceSelect.innerHTML = `<option value="${p1}">Prix 1 (${p1.toFixed(2)} DT)</option>`;
        if (p2 > 0) priceSelect.innerHTML += `<option value="${p2}">Prix 2 (${p2.toFixed(2)} DT)</option>`;
        if (p3 > 0) priceSelect.innerHTML += `<option value="${p3}">Prix 3 (${p3.toFixed(2)} DT)</option>`;
        priceSelect.dataset.currentProp = propSelect.value;
        document.getElementById('priceSelectionRow').style.display = 'block';

        // Set initial selected price on page load
        if (!priceSelect.dataset.initialized) {
            priceSelect.dataset.initialized = 'true';
            if (initialAppliedPrice === p1 || initialAppliedPrice === p2 || initialAppliedPrice === p3) {
                priceSelect.value = initialAppliedPrice;
            } else {
                priceSelect.innerHTML += `<option value="${initialAppliedPrice}" selected>Tarif personnalisé (${initialAppliedPrice.toFixed(2)} DT)</option>`;
            }
        } else {
            // Auto-select season when property changes
            if (checkIn) {
                const checkInDate = new Date(checkIn);
                const month = checkInDate.getMonth();
                if ((month === 5 || month === 8) && p2 > 0) {
                    priceSelect.value = p2;
                } else if ((month === 6 || month === 7) && p3 > 0) {
                    priceSelect.value = p3;
                } else {
                    priceSelect.value = p1;
                }
            }
        }
    }

    // Auto-select season when check-in date changes
    if (checkIn && priceSelect.dataset.lastCheckIn !== checkIn) {
        const checkInDate = new Date(checkIn);
        const month = checkInDate.getMonth();
        if (priceSelect.dataset.lastCheckIn !== undefined) {
            if ((month === 5 || month === 8) && p2 > 0) {
                priceSelect.value = p2;
            } else if ((month === 6 || month === 7) && p3 > 0) {
                priceSelect.value = p3;
            } else {
                priceSelect.value = p1;
            }
        }
        priceSelect.dataset.lastCheckIn = checkIn;
    }
}

function calc() {
    const ci = checkInInput.value;
    const co = checkOutInput.value;
    const pid = propSelect.value;
    
    updateAppliedPriceDropdown();
    
    if(ci && co && pid) {
        const nights = Math.max(0, Math.round((new Date(co)-new Date(ci))/(1000*60*60*24)));
        const price = parseFloat(priceSelect.value) || 0;
        const accommodationTotal = nights * price;
        
        // Services total
        let servicesTotal = 0;
        document.querySelectorAll('.service-line').forEach(line => {
            const select = line.querySelector('.service-select');
            const qty = parseInt(line.querySelector('.qty-input').value) || 0;
            const period = parseInt(line.querySelector('.period-input').value) || 0;
            
            if (select.value) {
                const opt = select.options[select.selectedIndex];
                const sPrice = parseFloat(opt.dataset.price);
                const sTotal = sPrice * qty * period;
                servicesTotal += sTotal;
                line.querySelector('.line-total').innerText = sTotal.toFixed(2) + ' DT';
            }
        });

        const total = accommodationTotal + servicesTotal;
        document.getElementById('nightsCount').textContent = nights;
        document.getElementById('totalAmount').textContent = total.toLocaleString('fr-TN') + ' DT';
        document.getElementById('totalAmountInput').value = total.toFixed(2);
    }
}

['checkIn', 'checkOut', 'propertySelect', 'appliedPriceSelect'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', calc);
    document.getElementById(id)?.addEventListener('input', calc);
});

// Run initial calculation on page load
calc();
</script>
@endpush
