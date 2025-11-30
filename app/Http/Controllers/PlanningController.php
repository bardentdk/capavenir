<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPlanningEvent;

class PlanningController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isAccounting = $currentUser->hasRole(['admin', 'accountant']);

        // Si Compta : on regarde l'user sélectionné, sinon l'user connecté
        $targetUserId = $isAccounting
            ? ($request->input('user_id') ?? $currentUser->id)
            : $currentUser->id;

        // Récupération des events pour le calendrier
        $events = Planning::where('user_id', $targetUserId)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'start' => $e->start_at->toIso8601String(),
                'end' => $e->end_at->toIso8601String(),
                // Couleur selon le type (Pastel style iOS)
                'backgroundColor' => match($e->type) {
                    'reunion' => '#e0f2fe', // Sky 100
                    'formation' => '#fce7f3', // Pink 100
                    default => '#dcfce7', // Green 100
                },
                'borderColor' => 'transparent',
                'textColor' => match($e->type) {
                    'reunion' => '#0369a1', // Sky 700
                    'formation' => '#be185d', // Pink 700
                    default => '#15803d', // Green 700
                },
                'extendedProps' => [
                    'description' => $e->description,
                    'type' => $e->type
                ]
            ]);

        // Liste des employés (seulement pour la compta)
        $users = $isAccounting ? User::role('educator')->orderBy('name')->get() : [];

        return Inertia::render('Planning/Index', [
            'events' => $events,
            'users' => $users,
            'selectedUserId' => (int) $targetUserId,
            'canEdit' => $isAccounting // Seul la compta peut modifier
        ]);
    }

    public function store(Request $request)
    {
        // Seule la compta peut créer
        if (!Auth::user()->hasRole(['admin', 'accountant'])) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'type' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $event = Planning::create([
            ...$validated,
            'creator_id' => Auth::id()
        ]);

        // Notification Mail
        $educator = User::find($validated['user_id']);
        if ($educator && $educator->email) {
            try {
                Mail::to($educator)->send(new NewPlanningEvent($event));
            } catch (\Exception $e) {}
        }

        return back()->with('success', 'Événement ajouté et notifié.');
    }

    // Ajoute une méthode destroy si tu veux supprimer aussi
    public function destroy(Planning $planning) {
         if (!Auth::user()->hasRole(['admin', 'accountant'])) abort(403);
         $planning->delete();
         return back()->with('success', 'Événement supprimé.');
    }
}