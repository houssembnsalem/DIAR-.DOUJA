<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::withCount('reservations');

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $properties = $query->orderBy('sort_order')->orderBy('name')->paginate(12);

        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('admin.properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bungalow,apartment,room',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'summer_price' => 'nullable|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'bedrooms' => 'required|integer|min:1',
            'surface' => 'nullable|string',
            'amenities' => 'nullable|array',
            'status' => 'required|in:available,unavailable,maintenance',
            'location' => 'nullable|string',
        ]);

        $validated['amenities'] = $request->amenities ?? [];
        $property = Property::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $i => $photo) {
                $path = $photo->store('properties/' . $property->id, 'public');
                PropertyPhoto::create([
                    'property_id' => $property->id,
                    'path' => $path,
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.properties.index')
            ->with('success', 'Logement créé avec succès!');
    }

    public function show(Property $property)
    {
        $property->load(['photos', 'reservations.client']);
        $occupancyRate = $property->getOccupancyRate(
            now()->startOfMonth()->format('Y-m-d'),
            now()->endOfMonth()->format('Y-m-d')
        );
        $monthlyRevenue = $property->reservations()
            ->whereMonth('check_in', now()->month)
            ->whereNotIn('status', ['cancelled'])
            ->sum('final_amount');

        return view('admin.properties.show', compact('property', 'occupancyRate', 'monthlyRevenue'));
    }

    public function edit(Property $property)
    {
        $property->load('photos');
        return view('admin.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bungalow,apartment,room',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'summer_price' => 'nullable|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'bedrooms' => 'required|integer|min:1',
            'surface' => 'nullable|string',
            'amenities' => 'nullable|array',
            'status' => 'required|in:available,unavailable,maintenance',
            'location' => 'nullable|string',
        ]);

        $validated['amenities'] = $request->amenities ?? [];
        $property->update($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $i => $photo) {
                $path = $photo->store('properties/' . $property->id, 'public');
                PropertyPhoto::create([
                    'property_id' => $property->id,
                    'path' => $path,
                    'is_primary' => false,
                    'sort_order' => $property->photos()->count() + $i,
                ]);
            }
        }

        return redirect()->route('admin.properties.show', $property)
            ->with('success', 'Logement mis à jour avec succès!');
    }

    public function destroy(Property $property)
    {
        if ($property->reservations()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return back()->with('error', 'Impossible de supprimer un logement avec des réservations actives.');
        }
        $property->delete();
        return redirect()->route('admin.properties.index')
            ->with('success', 'Logement supprimé avec succès!');
    }

    public function toggleStatus(Property $property)
    {
        $property->status = $property->status === 'available' ? 'unavailable' : 'available';
        $property->save();
        return back()->with('success', 'Statut mis à jour!');
    }

    public function uploadPhoto(Request $request, Property $property)
    {
        $request->validate(['photo' => 'required|image|max:5120']);
        $path = $request->file('photo')->store('properties/' . $property->id, 'public');
        $photo = PropertyPhoto::create([
            'property_id' => $property->id,
            'path' => $path,
            'is_primary' => $property->photos()->count() === 0,
            'sort_order' => $property->photos()->count(),
        ]);
        return response()->json(['success' => true, 'photo' => $photo]);
    }

    public function deletePhoto(Property $property, PropertyPhoto $photo)
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();
        return response()->json(['success' => true]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'type' => 'nullable|in:bungalow,apartment,room',
        ]);

        $properties = Property::where('status', 'available')
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->get()
            ->filter(fn($p) => $p->isAvailableForDates($request->check_in, $request->check_out));

        return response()->json($properties->values());
    }
}
