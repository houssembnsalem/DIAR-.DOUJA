<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('reservations');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query->orderBy('first_name')->paginate(15)->withQueryString();
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'id_number' => 'nullable|string',
            'nationality' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $client = Client::create($validated);
        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client créé avec succès!');
    }

    public function show(Client $client)
    {
        $client->load(['reservations.property', 'reservations.payments']);
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'id_number' => 'nullable|string',
            'nationality' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);
        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client mis à jour!');
    }

    public function destroy(Client $client)
    {
        if ($client->reservations()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return back()->with('error', 'Impossible de supprimer un client avec des réservations actives.');
        }
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client supprimé!');
    }

    public function reservations(Client $client)
    {
        $client->load(['reservations.property']);
        return view('admin.clients.reservations', compact('client'));
    }
}
