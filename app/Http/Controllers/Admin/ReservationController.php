<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Property;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ReservationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['property', 'client'])
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reservation_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('client', fn($c) => $c->where('first_name', 'like', '%' . $request->search . '%')
                      ->orWhere('last_name', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->property_id) {
            $query->where('property_id', $request->property_id);
        }
        if ($request->date_from) {
            $query->where('check_in', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('check_out', '<=', $request->date_to);
        }
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $reservations = $query->paginate(15)->withQueryString();
        $properties = Property::where('status', 'available')->orderBy('name')->get();

        return view('admin.reservations.index', compact('reservations', 'properties'));
    }

    public function create(Request $request)
    {
        $properties = Property::where('status', 'available')->orderBy('name')->get();
        $clients = Client::orderBy('first_name')->get();
        $services = Service::orderBy('name')->get();
        $selectedProperty = $request->property_id ? Property::find($request->property_id) : null;

        return view('admin.reservations.create', compact('properties', 'clients', 'services', 'selectedProperty'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'client_type' => 'required|in:existing,new',
            'client_id' => 'required_if:client_type,existing|nullable|exists:clients,id',
            'new_client_first_name' => 'required_if:client_type,new|nullable|string|max:255',
            'new_client_last_name' => 'required_if:client_type,new|nullable|string|max:255',
            'new_client_phone' => 'required_if:client_type,new|nullable|string',
            'new_client_email' => 'nullable|email',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests_count' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'initial_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'source' => 'nullable|string',
        ]);

        // Create new client if needed
        if ($request->client_type === 'new') {
            $client = Client::create([
                'first_name' => $request->new_client_first_name,
                'last_name' => $request->new_client_last_name,
                'phone' => $request->new_client_phone,
                'email' => $request->new_client_email,
            ]);
            $clientId = $client->id;
        } else {
            $clientId = $request->client_id;
        }

        $property = Property::findOrFail($request->property_id);

        // Check availability
        if (!$property->isAvailableForDates($request->check_in, $request->check_out)) {
            return back()->withErrors(['check_in' => 'Ce logement n\'est pas disponible pour ces dates.'])->withInput();
        }

        $nights = Carbon::parse($request->check_in)->diffInDays($request->check_out);
        $pricePerNight = $request->input('applied_price', $property->price_per_night);
        $accommodationTotal = $nights * $pricePerNight;
        
        // Services calculation
        $servicesTotal = 0;
        $serviceLines = [];
        if ($request->has('services')) {
            foreach ($request->services as $sData) {
                if (empty($sData['service_id'])) continue;
                $service = Service::find($sData['service_id']);
                if ($service) {
                    $qty = $sData['quantity'] ?? 1;
                    $period = $sData['period'] ?? 1;
                    $lineTotal = $service->price * $qty * $period;
                    $servicesTotal += $lineTotal;
                    $serviceLines[] = [
                        'service_id' => $service->id,
                        'quantity' => $qty,
                        'period' => $period,
                        'price' => $service->price,
                        'total' => $lineTotal
                    ];
                }
            }
        }

        $totalAmount = $accommodationTotal + $servicesTotal;
        $discount = $request->discount ?? 0;
        $finalAmount = $totalAmount - $discount;
        $initialPayment = min($request->initial_payment ?? 0, $finalAmount);

        $reservation = Reservation::create([
            'reservation_number' => Reservation::generateNumber(),
            'property_id' => $property->id,
            'client_id' => $clientId,
            'created_by' => auth()->id(),
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'guests_count' => $request->guests_count,
            'price_per_night' => $pricePerNight,
            'total_amount' => $totalAmount,
            'discount' => $discount,
            'final_amount' => $finalAmount,
            'amount_paid' => $initialPayment,
            'payment_status' => $initialPayment >= $finalAmount ? 'paid' : ($initialPayment > 0 ? 'partial' : 'pending'),
            'status' => 'confirmed',
            'notes' => $request->notes,
            'source' => $request->source ?? 'direct',
        ]);

        // Save service lines
        foreach ($serviceLines as $line) {
            $reservation->services()->create($line);
        }

        if ($initialPayment > 0) {
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $initialPayment,
                'payment_method' => $request->payment_method ?? 'cash',
                'payment_date' => now()->format('Y-m-d'),
                'notes' => 'Paiement initial',
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Réservation créée avec succès! N° ' . $reservation->reservation_number);
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['property.photos', 'client', 'payments.creator', 'creator']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        if (in_array($reservation->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', 'Cette réservation ne peut pas être modifiée.');
        }
        $properties = Property::orderBy('name')->get();
        $clients = Client::orderBy('first_name')->get();
        $services = Service::orderBy('name')->get();
        $reservation->load('services');
        return view('admin.reservations.edit', compact('reservation', 'properties', 'clients', 'services'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'applied_price' => 'required|numeric|min:0',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'guests_count' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:confirmed,pending,checked_in,checked_out,cancelled',
        ]);

        $property = Property::findOrFail($request->property_id);

        // Check availability (excluding current reservation)
        if (!$property->isAvailableForDates($request->check_in, $request->check_out, $reservation->id)) {
            return back()->withErrors(['check_in' => 'Le logement n\'est pas disponible pour ces nouvelles dates.'])->withInput();
        }

        $nights = Carbon::parse($request->check_in)->diffInDays($request->check_out);
        $pricePerNight = $request->input('applied_price', $reservation->price_per_night);
        $accommodationTotal = $nights * $pricePerNight;
        
        // Services calculation
        $servicesTotal = 0;
        $serviceLines = [];
        if ($request->has('services')) {
            foreach ($request->services as $sData) {
                if (empty($sData['service_id'])) continue;
                $service = Service::find($sData['service_id']);
                if ($service) {
                    $qty = $sData['quantity'] ?? 1;
                    $period = $sData['period'] ?? 1;
                    $lineTotal = $service->price * $qty * $period;
                    $servicesTotal += $lineTotal;
                    $serviceLines[] = [
                        'service_id' => $service->id,
                        'quantity' => $qty,
                        'period' => $period,
                        'price' => $service->price,
                        'total' => $lineTotal
                    ];
                }
            }
        }

        $totalAmount = $accommodationTotal + $servicesTotal;
        $discount = $request->discount ?? 0;
        $finalAmount = $totalAmount - $discount;

        $reservation->update([
            'property_id' => $property->id,
            'price_per_night' => $pricePerNight,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'guests_count' => $request->guests_count,
            'total_amount' => $totalAmount,
            'discount' => $discount,
            'final_amount' => $finalAmount,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        // Sync services
        $reservation->services()->delete();
        foreach ($serviceLines as $line) {
            $reservation->services()->create($line);
        }

        $reservation->updatePaymentStatus();

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Réservation mise à jour!');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->route('admin.reservations.index')
            ->with('success', 'Réservation supprimée!');
    }

    public function cancel(Reservation $reservation)
    {
        $reservation->update(['status' => 'cancelled']);
        return back()->with('success', 'Réservation annulée!');
    }

    public function confirm(Reservation $reservation)
    {
        $reservation->update(['status' => 'confirmed']);
        return back()->with('success', 'Réservation confirmée!');
    }

    public function checkIn(Reservation $reservation)
    {
        $reservation->update(['status' => 'checked_in', 'actual_check_in' => now()]);
        return back()->with('success', 'Check-in effectué!');
    }

    public function checkOut(Reservation $reservation)
    {
        $reservation->update(['status' => 'checked_out', 'actual_check_out' => now()]);
        return back()->with('success', 'Check-out effectué!');
    }

    public function addPayment(Request $request, Reservation $reservation)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $reservation->amount_remaining,
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        $reservation->increment('amount_paid', $request->amount);
        $reservation->updatePaymentStatus();

        return back()->with('success', 'Paiement de ' . number_format($request->amount, 2) . ' DT enregistré!');
    }

    public function invoice(Reservation $reservation)
    {
        $reservation->load(['property', 'client', 'payments']);
        return view('admin.reservations.invoice', compact('reservation'));
    }

    public function invoicePdf(Reservation $reservation)
    {
        $reservation->load(['property', 'client', 'payments']);
        $pdf = Pdf::loadView('admin.reservations.invoice-pdf', compact('reservation'));
        return $pdf->download('facture-' . $reservation->reservation_number . '.pdf');
    }
}
