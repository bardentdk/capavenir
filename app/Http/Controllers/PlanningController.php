<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPlanningEvent;
use Carbon\Carbon;

class PlanningController extends Controller
{
    /**
     * Affiche le planning.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isAccounting = $currentUser->hasRole(['admin', 'accountant']);

        $targetUserId = $isAccounting ? ($request->input('user_id') ?? $currentUser->id) : $currentUser->id;

        $events = Planning::whereHas('users', function($q) use ($targetUserId) {
                $q->where('users.id', $targetUserId);
            })
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->title,
                // FullCalendar gère automatiquement la conversion si on lui donne du ISO8601 UTC
                'start' => $e->start_at->toIso8601String(),
                'end' => $e->end_at->toIso8601String(),
                'backgroundColor' => match($e->type) {
                    'reunion' => '#e0f2fe',
                    'formation' => '#fce7f3',
                    default => '#dcfce7',
                },
                'borderColor' => 'transparent',
                'textColor' => match($e->type) {
                    'reunion' => '#0369a1',
                    'formation' => '#be185d',
                    default => '#15803d',
                },
                'extendedProps' => [
                    'description' => $e->description,
                    'type' => $e->type
                ]
            ]);

        $users = $isAccounting ? User::role('educator')->orderBy('name')->get() : [];

        return Inertia::render('Planning/Index', [
            'events' => $events,
            'users' => $users,
            'selectedUserId' => (int) $targetUserId,
            'canEdit' => $isAccounting
        ]);
    }

    /**
     * Création d'événements.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'accountant'])) {
            abort(403);
        }

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'recurrence' => 'nullable|in:none,daily,weekly,monthly',
            // CORRECTION : Date de fin obligatoire si récurrence activée
            'recurrence_end' => 'nullable|required_if:recurrence,daily,weekly,monthly|date|after:start_at',
        ]);

        // --- CORRECTION TIMEZONE (La Réunion) ---
        // On considère que l'heure reçue du formulaire est en heure locale (Réunion)
        // On la convertit en UTC pour le stockage en base
        $timezone = 'Indian/Reunion';

        $start = Carbon::parse($validated['start_at'], $timezone)->setTimezone('UTC');
        $end = Carbon::parse($validated['end_at'], $timezone)->setTimezone('UTC');

        $durationInMinutes = $start->diffInMinutes($end);

        // --- GESTION RÉCURRENCE ---
        $recurrence = $request->input('recurrence', 'none');
        $recurrenceEnd = $request->input('recurrence_end')
            ? Carbon::parse($request->recurrence_end, $timezone)->endOfDay()->setTimezone('UTC')
            : null;

        $datesToCreate = [$start]; // On commence par la première date

        if ($recurrence !== 'none' && $recurrenceEnd) {
            $currentDate = $start->copy();

            // Sécurité : Max 50 événements pour éviter boucle infinie
            $loopLimit = 50;
            $i = 0;

            while ($i < $loopLimit) {
                if ($recurrence === 'daily') $currentDate->addDay();
                elseif ($recurrence === 'weekly') $currentDate->addWeek();
                elseif ($recurrence === 'monthly') $currentDate->addMonth();

                if ($currentDate->gt($recurrenceEnd)) break;

                $datesToCreate[] = $currentDate->copy();
                $i++;
            }
        }

        // --- CRÉATION EN BOUCLE ---
        $createdCount = 0;
        foreach ($datesToCreate as $dateStart) {
            $dateEnd = $dateStart->copy()->addMinutes($durationInMinutes);

            $event = Planning::create([
                'creator_id' => Auth::id(),
                'title' => $validated['title'],
                'start_at' => $dateStart,
                'end_at' => $dateEnd,
                'type' => $validated['type'],
                'description' => $validated['description'],
            ]);

            $event->users()->attach($validated['user_ids']);
            $createdCount++;
        }

        // Notification (sur le dernier event créé pour l'exemple)
        $usersToNotify = User::whereIn('id', $validated['user_ids'])->get();
        foreach ($usersToNotify as $user) {
            if ($user->email && isset($event)) {
                try {
                    Mail::to($user)->send(new NewPlanningEvent($event));
                } catch (\Exception $e) {}
            }
        }

        return back()->with('success', "$createdCount créneaux ajoutés.");
    }

    public function destroy(Planning $planning)
    {
        if (!Auth::user()->hasRole(['admin', 'accountant'])) abort(403);
        $planning->delete();
        return back()->with('success', 'Événement supprimé.');
    }
}