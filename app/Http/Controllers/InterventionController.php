<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Models\Intervention;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use App\Services\AiReportGenerator;
use Illuminate\Support\Facades\Auth;

class InterventionController extends Controller
{
    /**
     * Liste des interventions de l'utilisateur connecté.
     */
    public function index()
    {
        $interventions = Auth::user()->interventions()
            ->with('client') // On charge les infos de l'enfant
            ->latest('start_at') // Les plus récentes en premier
            ->paginate(10) // Pagination automatique
            ->through(fn ($intervention) => [
                'id' => $intervention->id,
                'client_name' => $intervention->client->full_name,
                'start_at_formatted' => $intervention->start_at->format('d/m/Y H:i'),
                'end_at_time' => $intervention->end_at->format('H:i'),
                'duration' => $intervention->duration_minutes . ' min',
                'type' => $intervention->intervention_type,
                'status' => $intervention->status,
            ]);

        return Inertia::render('Interventions/Index', [
            'interventions' => $interventions,
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return Inertia::render('Interventions/Create', [
            // On envoie la liste des enfants actifs pour le menu déroulant
            'clients' => Client::where('is_active', true)
                ->orderBy('last_name')
                ->get()
                ->map(fn ($client) => [
                    'id' => $client->id,
                    'name' => $client->full_name,
                ]),
        ]);
    }
    public function show(Intervention $intervention)
    {
        // Sécurité : On vérifie que l'user a le droit de voir (policy à faire plus tard, ici check simple)
        if ($intervention->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return Inertia::render('Interventions/Show', [
            'intervention' => $intervention->load('client'), // On charge les infos client
            'can_validate' => in_array($intervention->status, ['draft', 'pending']),
        ]);
    }
    public function validateIntervention(Intervention $intervention)
    {
        if ($intervention->user_id !== Auth::id()) {
            abort(403);
        }

        $intervention->update([
            'status' => 'validated'
        ]);

        return back()->with('success', 'Intervention validée et verrouillée pour la paie.');
    }

    public function downloadPdf(Intervention $intervention)
    {
        if ($intervention->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $parsedReport = Str::markdown($intervention->ai_report ?? '');

        $pdf = Pdf::loadView('pdfs.intervention', [
            'intervention' => $intervention->load(['client', 'user']),
            'parsedReport' => $parsedReport // On passe la variable convertie
        ]);

        $pdf->setOption(['isRemoteEnabled' => true]);

        return $pdf->download('intervention-' . $intervention->id . '.pdf');
    }
    /**
     * Enregistrement (Stockage).
     */
    public function store(Request $request)
    {
        // 1. On ajoute 'ai_report' à la validation (nullable car pas obligatoire)
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'intervention_type' => 'required|string',
            'location_type' => 'required|string',
            'raw_notes' => 'nullable|string',
            'ai_report' => 'nullable|string', // <--- AJOUTER CETTE LIGNE
        ]);

        // 2. Création
        $request->user()->interventions()->create($validated);

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention enregistrée avec succès.');
    }

    public function generateReport(Request $request, AiReportGenerator $aiService): JsonResponse
    {
        $request->validate([
            'raw_notes' => 'required|string|min:5',
            'intervention_type' => 'required|string',
            'client_id' => 'required|exists:clients,id',
        ]);

        $client = \App\Models\Client::find($request->client_id);

        $report = $aiService->generate(
            $request->raw_notes,
            $request->intervention_type,
            $client->full_name
        );

        return response()->json(['report' => $report]);
    }
}