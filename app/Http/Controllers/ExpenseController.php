<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Client;
use App\Models\Expense;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Liste des frais.
     */
    public function index()
    {
        $expenses = Auth::user()->expenses()
            ->with('client') // Pour savoir pour quel enfant c'était
            ->latest('expense_date')
            ->paginate(10)
            ->through(fn ($expense) => [
                'id' => $expense->id,
                'type' => $expense->type, // 'mileage' ou 'purchase'
                'date_formatted' => $expense->expense_date->format('d/m/Y'),
                'amount' => number_format($expense->amount, 2, ',', ' ') . ' €',
                'description' => $expense->type === 'mileage'
                    ? "Trajet : {$expense->distance_km} km"
                    : "Achat pour " . ($expense->client ? $expense->client->full_name : 'Autre'),
                'status' => $expense->status,
                'proof_url' => $expense->proof_path ? Storage::url($expense->proof_path) : null,
            ]);

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return Inertia::render('Expenses/Create', [
            'clients' => Client::where('is_active', true)->orderBy('last_name')->get()
                ->map(fn($c) => ['id' => $c->id, 'name' => $c->full_name]),
        ]);
    }

    /**
     * Enregistrement.
     */
    public function store(Request $request)
    {
        // 1. Validation des règles de base
        $rules = [
            'type' => 'required|in:mileage,purchase',
            'expense_date' => 'required|date',
            'client_id' => 'nullable|exists:clients,id',
        ];

        // 2. Règles spécifiques
        if ($request->input('type') === 'mileage') {
            $rules['start_address'] = 'required|string';
            $rules['end_address'] = 'required|string';
            $rules['distance_km'] = 'required|numeric|min:0.1';
        } else {
            $rules['amount'] = 'required|numeric|min:0.1';
            // On laisse 'nullable' ici pour gérer l'erreur manuellement et éviter le crash
            $rules['proof'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120';
        }

        $validated = $request->validate($rules);

        // 3. Gestion du fichier (BLINDÉE)
        $path = null;

        if ($request->input('type') === 'purchase') {

            if (!$request->hasFile('proof')) {
                return back()->withErrors(['proof' => 'Le justificatif est obligatoire.']);
            }

            $file = $request->file('proof');

            if (!$file->isValid()) {
                return back()->withErrors(['proof' => 'Erreur upload.']);
            }

            // --- NOUVELLE MÉTHODE DE SAUVEGARDE ---
            try {
                // 1. On définit le nom et le chemin complet manuellement
                $extension = $file->getClientOriginalExtension();
                $filename = 'receipt_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $extension;
                $destinationPath = 'receipts/' . $filename;

                // 2. On utilise la Facade Storage directement (Bypass le bug "storeAs")
                // On lit le fichier temporaire et on l'écrit sur le disque 'public'
                \Illuminate\Support\Facades\Storage::disk('public')->put($destinationPath, file_get_contents($file));

                // 3. On enregistre le chemin pour la base de données
                $path = $destinationPath;

            } catch (\Exception $e) {
                return back()->withErrors(['proof' => 'Erreur sauvegarde disque : ' . $e->getMessage()]);
            }
        }

        // 4. Calcul Montant
        $finalAmount = $request->amount;
        if ($request->input('type') === 'mileage') {
            $bareme = 0.50;
            $finalAmount = $validated['distance_km'] * $bareme;
        }

        // 5. Sauvegarde en base
        Auth::user()->expenses()->create([
            'type' => $validated['type'],
            'expense_date' => $validated['expense_date'],
            'client_id' => $validated['client_id'] ?? null,
            'start_address' => $validated['start_address'] ?? null,
            'end_address' => $validated['end_address'] ?? null,
            'distance_km' => $validated['distance_km'] ?? null,
            'amount' => $finalAmount,
            'proof_path' => $path,
            'status' => 'pending'
        ]);

        return redirect()->route('expenses.index')->with('success', 'Note de frais enregistrée.');
    }

    public function calculateDistance(Request $request)
    {
        $request->validate([
            'start' => 'required|string|min:3',
            'end' => 'required|string|min:3',
        ]);

        $apiKey = config('services.google.maps_key');

        // Appel à l'API Google
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $request->start,
            'destinations' => $request->end,
            'key' => $apiKey,
            'language' => 'fr', // Pour avoir les erreurs en français si besoin
            'units' => 'metric'
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Erreur de connexion Google'], 500);
        }

        $data = $response->json();

        // Vérification de la réponse Google (Status global)
        if ($data['status'] !== 'OK') {
            return response()->json(['error' => 'Erreur API : ' . $data['status']], 400);
        }

        // Vérification du trajet spécifique (Status element)
        $element = $data['rows'][0]['elements'][0];
        if ($element['status'] !== 'OK') {
             // ZERO_RESULTS = Pas de route trouvée (ex: océan entre les deux)
            return response()->json(['distance' => 0, 'error' => 'Trajet introuvable']);
        }

        // Google renvoie des mètres, on convertit en KM (ex: 12500 -> 12.5)
        $distanceKm = round($element['distance']['value'] / 1000, 2);

        return response()->json([
            'distance' => $distanceKm,
            'text' => $element['distance']['text'] // "12.5 km"
        ]);
    }
}