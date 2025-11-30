<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Liste des bénéficiaires.
     */
    public function index(Request $request)
    {
        // Recherche simple
        $query = Client::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->search.'%')
                  ->orWhere('last_name', 'like', '%'.$request->search.'%');
            });
        }

        $clients = $query->orderBy('last_name')
            ->paginate(10)
            ->withQueryString() // Garde la recherche dans l'URL lors de la pagination
            ->through(fn($client) => [
                'id' => $client->id,
                'name' => $client->full_name,
                'age' => $client->birth_date ? $client->birth_date->age . ' ans' : 'N/A',
                'address' => $client->address,
                'is_active' => $client->is_active,
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return Inertia::render('Clients/Form');
    }

    /**
     * Enregistrement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'medical_info' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Dossier créé avec succès.');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Client $client)
    {
        return Inertia::render('Clients/Form', [
            'client' => $client
        ]);
    }

    /**
     * Mise à jour.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'medical_info' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Dossier mis à jour.');
    }

    /**
     * Suppression (Soft delete ou définitive, ici définitive pour simplifier).
     */
    public function destroy(Client $client)
    {
        // On vérifie s'il a des interventions pour éviter de casser l'historique
        if ($client->interventions()->exists()) {
            return back()->with('error', 'Impossible de supprimer un bénéficiaire ayant déjà des interventions. Désactivez-le plutôt.');
        }

        $client->delete();
        return back()->with('success', 'Dossier supprimé.');
    }
}