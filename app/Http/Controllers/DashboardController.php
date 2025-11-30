<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Intervention;
use App\Models\Expense;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $stats = [];
        $shortcuts = [];

        // --- CAS 1 : ÉDUCATEUR (Vue Personnelle) ---
        if ($user->hasRole('educator')) {

            // 1. Interventions du mois
            $interventionsMonth = $user->interventions()
                ->whereYear('start_at', $now->year)
                ->whereMonth('start_at', $now->month)
                ->count();

            // 2. Heures validées (pour sa paie)
            $minutesValidated = $user->interventions()
                ->where('status', 'validated')
                ->whereYear('start_at', $now->year)
                ->whereMonth('start_at', $now->month)
                ->sum('duration_minutes');

            $hours = floor($minutesValidated / 60);
            $minutes = $minutesValidated % 60;

            // 3. Frais en attente
            $pendingExpenses = $user->expenses()->where('status', 'pending')->count();

            $stats = [
                ['label' => 'Interventions (Mois)', 'value' => $interventionsMonth, 'color' => 'text-blue-600'],
                ['label' => 'Heures Validées', 'value' => "{$hours}h{$minutes}", 'color' => 'text-green-600'],
                ['label' => 'Frais en attente', 'value' => $pendingExpenses, 'color' => 'text-orange-600'],
            ];

            // Raccourcis utiles
            $shortcuts = [
                ['label' => 'Nouvelle Intervention', 'url' => route('interventions.create'), 'icon' => 'PlusIcon', 'color' => 'bg-sky-600'],
                ['label' => 'Déclarer un frais', 'url' => route('expenses.create'), 'icon' => 'BanknotesIcon', 'color' => 'bg-indigo-600'],
            ];
        }

        // --- CAS 2 : ADMIN / COMPTA (Vue Globale) ---
        else {
            // 1. Total Dossiers Actifs
            $activeClients = Client::where('is_active', true)->count();

            // 2. Frais à valider (Global)
            $expensesToApprove = Expense::where('status', 'pending')->count();

            // 3. Interventions totales du mois
            $totalInterventions = Intervention::whereYear('start_at', $now->year)
                ->whereMonth('start_at', $now->month)
                ->count();

            $stats = [
                ['label' => 'Dossiers Actifs', 'value' => $activeClients, 'color' => 'text-purple-600'],
                ['label' => 'Frais à valider', 'value' => $expensesToApprove, 'color' => 'text-red-600'],
                ['label' => 'Interventions (Global)', 'value' => $totalInterventions, 'color' => 'text-slate-600'],
            ];

            $shortcuts = [
                ['label' => 'Gérer l\'équipe', 'url' => route('users.index'), 'icon' => 'UserGroupIcon', 'color' => 'bg-slate-700'],
                ['label' => 'Comptabilité', 'url' => route('accounting.index'), 'icon' => 'ChartPieIcon', 'color' => 'bg-orange-600'],
                ['label' => 'Nouveau Bénéficiaire', 'url' => route('clients.create'), 'icon' => 'UserPlusIcon', 'color' => 'bg-green-600'],
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'shortcuts' => $shortcuts,
            'role' => $user->getRoleNames()->first() ?? 'user',
            'user' => Auth::user(),
        ]);
    }
}