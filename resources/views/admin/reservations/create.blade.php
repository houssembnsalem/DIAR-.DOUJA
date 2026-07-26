@extends('layouts.app')

@section('title', 'Nouvelle réservation')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="fw-bold mb-0">Nouvelle réservation</h4>
</div>

<form method="POST" action="{{ route('admin.reservations.store') }}" id="reservationForm">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Property Selection -->
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-building me-2 text-primary"></i>Logement</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Logement *</label>
                            <select name="property_id" id="propertySelect" class="form-select @error('property_id') is-invalid @enderror" required>
                                <option value="">Choisir un logement...</option>
                                @foreach($properties as $prop)
                                <option value="{{ $prop->id }}"
                                        data-price="{{ $prop->price_per_night }}"
                                        data-price2="{{ $prop->weekend_price }}"
                                        data-price3="{{ $prop->summer_price }}"
                                        data-capacity="{{ $prop->capacity }}"
                                        data-type="{{ $prop->getTypeLabel() }}"
                                        {{ (old('property_id', $selectedProperty?->id) == $prop->id) ? 'selected' : '' }}>
                                    {{ $prop->name }} — {{ $prop->getTypeLabel() }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4" id="priceSelectionRow" style="display: none;">
                            <label class="form-label">Tarif à appliquer *</label>
                            <select name="applied_price" id="appliedPriceSelect" class="form-select border-primary fw-bold">
                                <!-- Options populated by JS -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date d'arrivée (Check-in) *</label>
                            <input type="date" name="check_in" id="checkIn" class="form-control @error('check_in') is-invalid @enderror"
                                   value="{{ old('check_in', request('check_in')) }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date de départ (Check-out) *</label>
                            <input type="date" name="check_out" id="checkOut" class="form-control @error('check_out') is-invalid @enderror"
                                   value="{{ old('check_out', request('check_out')) }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nombre de personnes *</label>
                            <input type="number" name="guests_count" id="guestsCount" class="form-control"
                                   value="{{ old('guests_count', 1) }}" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Source</label>
                            <select name="source" class="form-select">
                                <option value="direct">Direct</option>
                                <option value="online">En ligne</option>
                                <option value="agency">Agence</option>
                                <option value="referral">Recommandation</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Remise (DT)</label>
                            <input type="number" name="discount" id="discountInput" class="form-control"
                                   value="{{ old('discount', 0) }}" min="0" step="0.5">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client -->
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-person me-2 text-primary"></i>Client</div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input type="radio" name="client_type" value="existing" id="existingClient"
                                       class="form-check-input" {{ !old('new_client_first_name') ? 'checked' : '' }}>
                                <label class="form-check-label" for="existingClient">Client existant</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="client_type" value="new" id="newClient"
                                       class="form-check-input" {{ old('new_client_first_name') ? 'checked' : '' }}>
                                <label class="form-check-label" for="newClient">Nouveau client</label>
                            </div>
                        </div>

                        <div id="existingClientForm">
                            <select name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                                <option value="">Choisir un client existant...</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->full_name }} — {{ $client->phone }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="newClientForm" style="{{ old('new_client_first_name') ? '' : 'display:none;' }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" name="new_client_first_name" class="form-control"
                                           value="{{ old('new_client_first_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" name="new_client_last_name" class="form-control"
                                           value="{{ old('new_client_last_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone *</label>
                                    <input type="text" name="new_client_phone" class="form-control"
                                           value="{{ old('new_client_phone') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="new_client_email" class="form-control"
                                           value="{{ old('new_client_email') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-patch-plus me-2 text-primary"></i>Service</span>
                    <button type="button" class="btn btn-sm btn-primary" id="addServiceBtn">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div id="servicesContainer">
                        <!-- Service lines will be added here -->
                    </div>
                    <div id="noServicesMsg" class="text-center py-2 text-muted small">
                        Aucun service additionnel sélectionné.
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <div class="card">
                <div class="card-header"><i class="bi bi-cash me-2 text-primary"></i>Paiement initial (optionnel)</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Montant versé</label>
                            <div class="input-group">
                                <input type="number" name="initial_payment" id="initialPayment" class="form-control"
                                       value="{{ old('initial_payment', 0) }}" min="0" step="0.5">
                                <span class="input-group-text">DT</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Moyen de paiement</label>
                            <select name="payment_method" class="form-select">
                                <option value="cash">💵 Espèces</option>
                                <option value="card">💳 Carte bancaire</option>
                                <option value="transfer">🏦 Virement</option>
                                <option value="check">📄 Chèque</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top:80px;">
                <div class="card-header"><i class="bi bi-receipt me-2 text-primary"></i>Récapitulatif</div>
                <div class="card-body">
                    <div id="summaryContent">
                        <p class="text-muted text-center py-4">Sélectionnez un logement et des dates pour voir le récapitulatif.</p>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-check-lg me-1"></i>Confirmer la réservation
                    </button>
                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary w-100 mt-2">Annuler</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Client type toggle
document.querySelectorAll('input[name="client_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('existingClientForm').style.display = this.value === 'existing' ? '' : 'none';
        document.getElementById('newClientForm').style.display = this.value === 'new' ? '' : 'none';
    });
});

// Services management
const servicesData = @json($services);
const servicesContainer = document.getElementById('servicesContainer');
const noServicesMsg = document.getElementById('noServicesMsg');
const addServiceBtn = document.getElementById('addServiceBtn');
let serviceLineIndex = 0;

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
                    ${servicesData.map(s => `<option value="${s.id}" data-price="${s.price}" data-unit="${s.unit}">${s.name}</option>`).join('')}
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

    // Events for this line
    const select = div.querySelector('.service-select');
    const qty = div.querySelector('.qty-input');
    const period = div.querySelector('.period-input');
    const removeBtn = div.querySelector('.remove-service-btn');

    [select, qty, period].forEach(el => {
        el.addEventListener('change', updateSummary);
        el.addEventListener('input', updateSummary);
    });

    removeBtn.addEventListener('click', () => {
        div.remove();
        if (servicesContainer.children.length === 0) {
            noServicesMsg.style.display = 'block';
        }
        updateSummary();
    });

    updateSummary();
}

addServiceBtn.addEventListener('click', createServiceLine);

// Summary calculation
function updateSummary() {
    const propSelect = document.getElementById('propertySelect');
    const priceSelect = document.getElementById('appliedPriceSelect');
    const checkIn = document.getElementById('checkIn').value;
    const checkOut = document.getElementById('checkOut').value;
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const initialPayment = parseFloat(document.getElementById('initialPayment').value) || 0;

    if (!propSelect.value) {
        document.getElementById('priceSelectionRow').style.display = 'none';
        document.getElementById('summaryContent').innerHTML = '<p class="text-muted text-center py-4">Sélectionnez un logement pour voir les détails.</p>';
        return;
    }

    const option = propSelect.options[propSelect.selectedIndex];
    const p1 = parseFloat(option.dataset.price) || 0;
    const p2 = parseFloat(option.dataset.price2) || 0;
    const p3 = parseFloat(option.dataset.price3) || 0;

    // Populate price dropdown if changed or empty
    if (priceSelect.dataset.currentProp !== propSelect.value) {
        priceSelect.innerHTML = `<option value="${p1}">Prix 1 (${p1.toFixed(2)} DT)</option>`;
        if (p2 > 0) priceSelect.innerHTML += `<option value="${p2}">Prix 2 (${p2.toFixed(2)} DT)</option>`;
        if (p3 > 0) priceSelect.innerHTML += `<option value="${p3}">Prix 3 (${p3.toFixed(2)} DT)</option>`;
        priceSelect.dataset.currentProp = propSelect.value;
        document.getElementById('priceSelectionRow').style.display = 'block';
        
        // Auto-select season based on check-in date
        if (checkIn) {
            const checkInDate = new Date(checkIn);
            const month = checkInDate.getMonth(); // 0 = Jan, 11 = Dec
            if ((month === 5 || month === 8) && p2 > 0) {
                priceSelect.value = p2;
            } else if ((month === 6 || month === 7) && p3 > 0) {
                priceSelect.value = p3;
            } else {
                priceSelect.value = p1;
            }
        }
    }

    // Auto-select season when check-in date changes
    if (checkIn && priceSelect.dataset.lastCheckIn !== checkIn) {
        priceSelect.dataset.lastCheckIn = checkIn;
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

    if (!checkIn || !checkOut) {
        document.getElementById('summaryContent').innerHTML = '<p class="text-muted text-center py-4">Sélectionnez des dates pour voir le récapitulatif.</p>';
        return;
    }

    const price = parseFloat(priceSelect.value) || p1;
    const nights = Math.round((new Date(checkOut) - new Date(checkIn)) / 86400000);

    if (nights <= 0) {
        document.getElementById('summaryContent').innerHTML = '<p class="text-danger text-center py-4">Dates invalides.</p>';
        return;
    }

    const accommodationTotal = nights * price;
    
    // Services calculation
    let servicesTotal = 0;
    let servicesHtml = '';
    
    document.querySelectorAll('.service-line').forEach(line => {
        const select = line.querySelector('.service-select');
        const qty = parseInt(line.querySelector('.qty-input').value) || 0;
        const period = parseInt(line.querySelector('.period-input').value) || 0;
        
        if (select.value) {
            const opt = select.options[select.selectedIndex];
            const sPrice = parseFloat(opt.dataset.price);
            const sName = opt.text.split(' / ')[0];
            const sTotal = sPrice * qty * period;
            servicesTotal += sTotal;
            line.querySelector('.line-total').innerText = sTotal.toFixed(2) + ' DT';
            
            servicesHtml += `
                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>${sName} / ${qty} / ${period}</span>
                    <span>${sTotal.toFixed(2)} DT</span>
                </div>
            `;
        }
    });

    const subtotal = accommodationTotal + servicesTotal;
    const final = subtotal - discount;
    const remaining = final - initialPayment;

    document.getElementById('summaryContent').innerHTML = `
        <div class="mb-3 p-3 bg-light rounded">
            <div class="fw-bold mb-1">${option.text.split(' — ')[0]}</div>
            <div class="text-muted small">${option.dataset.type}</div>
        </div>

        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Arrivée</span>
            <strong>${new Date(checkIn).toLocaleDateString('fr-FR')}</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Départ</span>
            <strong>${new Date(checkOut).toLocaleDateString('fr-FR')}</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Nuits</span>
            <strong>${nights}</strong>
        </div>
        <div class="d-flex justify-content-between mb-2 text-primary fw-bold">
            <span class="text-muted">Prix appliqué</span>
            <strong>${price.toFixed(2)} DT</strong>
        </div>
        
        ${servicesHtml ? `
            <hr class="my-2">
            <div class="mb-2 fw-bold small text-primary">Services</div>
            ${servicesHtml}
        ` : ''}

        <hr>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Sous-total</span>
            <strong>${subtotal.toFixed(2)} DT</strong>
        </div>
        ${discount > 0 ? `
        <div class="d-flex justify-content-between mb-2 text-danger">
            <span>Remise</span>
            <strong>-${discount.toFixed(2)} DT</strong>
        </div>` : ''}
        <div class="d-flex justify-content-between mb-2 fs-5 fw-bold">
            <span>Total</span>
            <span class="text-primary">${final.toFixed(2)} DT</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-2 text-success">
            <span>Versement initial</span>
            <strong>${initialPayment.toFixed(2)} DT</strong>
        </div>
        <div class="d-flex justify-content-between fw-bold ${remaining > 0 ? 'text-danger' : 'text-success'}">
            <span>Reste à payer</span>
            <span>${remaining.toFixed(2)} DT</span>
        </div>
    `;
}

['propertySelect', 'appliedPriceSelect', 'checkIn', 'checkOut', 'discountInput', 'initialPayment'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', updateSummary);
    document.getElementById(id)?.addEventListener('input', updateSummary);
});

updateSummary();
</script>
@endpush
