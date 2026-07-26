<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $properties = Property::orderBy('name')->get();
        return view('admin.calendar.index', compact('properties'));
    }

    public function events(Request $request)
    {
        $start = $request->get('start', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end = $request->get('end', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $propertyId = $request->get('property_id');

        $query = Reservation::with(['property', 'client'])
            ->where('check_out', '>=', $start)
            ->where('check_in', '<=', $end)
            ->whereNotIn('status', ['cancelled']);

        if ($request->has('filtering')) {
            $ids = (array) $request->get('property_ids', []);
            $query->whereIn('property_id', $ids);
        } elseif ($propertyId = $request->get('property_id')) {
            $query->where('property_id', $propertyId);
        }

        $events = $query->get()->map(function ($reservation) {
            $color = match($reservation->status) {
                'pending' => '#f59e0b',
                'confirmed' => '#0ea5e9',
                'checked_in' => '#2563eb',
                'checked_out' => '#16a34a',
                default => '#6c757d',
            };

            return [
                'id' => $reservation->id,
                'title' => $reservation->client->full_name . ' - ' . $reservation->property->name,
                'start' => $reservation->check_in->format('Y-m-d'),
                'end' => $reservation->check_out->format('Y-m-d'),
                'color' => $color,
                'url' => route('admin.reservations.show', $reservation),
                'extendedProps' => [
                    'reservation_number' => $reservation->reservation_number,
                    'client' => $reservation->client->full_name,
                    'property' => $reservation->property->name,
                    'status' => $reservation->getStatusLabel(),
                    'nights' => $reservation->nights,
                    'amount' => $reservation->final_amount,
                    'paid' => $reservation->amount_paid,
                ],
            ];
        });

        return response()->json($events);
    }
}
