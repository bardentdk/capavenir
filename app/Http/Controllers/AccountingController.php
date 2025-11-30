<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // <--- Import
use App\Mail\ExpenseStatusUpdated;   // <--- Import

class AccountingController extends Controller
{
    /**
     * Dashboard principal de la compta.
     */
    public function index(Request $request)
    {
        // Sécurité : Seul Admin ou Compta passe (On utilise Spatie plus tard, ici check simple)
        if (!Auth::user()->hasRole(['admin', 'accountant'])) {
            abort(403, 'Accès réservé au service comptabilité.');
        }

        // Filtre par mois (par défaut le mois courant)
        $month = $request->input('month', now()->format('Y-m'));

        // 1. Récupérer les Frais en attente (tous)
        $pendingExpenses = Expense::with(['user', 'client'])
            ->where('status', 'pending')
            ->latest('expense_date')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'user_name' => $e->user->name,
                'type' => $e->type, // mileage / purchase
                'amount' => $e->amount,
                'date' => $e->expense_date->format('d/m/Y'),
                'description' => $e->type === 'mileage'
                    ? "Trajet {$e->distance_km} km ({$e->start_address} -> {$e->end_address})"
                    : "Achat (Justificatif fourni)",
                'proof_url' => $e->proof_path ? \Illuminate\Support\Facades\Storage::url($e->proof_path) : null,
            ]);

        // 2. Récupérer les totaux d'heures par Éducateur pour le mois sélectionné
        // C'est crucial pour la paie : combien d'heures à payer ce mois-ci ?
        $payrollSummary = User::role('educator') // Suppose que tu as bien le rôle
            ->withSum(['interventions' => function ($query) use ($month) {
                $query->where('status', 'validated') // Seulement celles validées par l'éduc
                      ->where('start_at', 'like', "$month%");
            }], 'duration_minutes')
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'total_hours' => floor(($u->interventions_sum_duration_minutes ?? 0) / 60),
                'total_minutes' => ($u->interventions_sum_duration_minutes ?? 0) % 60,
                'raw_minutes' => $u->interventions_sum_duration_minutes ?? 0,
            ]);

        return Inertia::render('Accounting/Dashboard', [
            'pendingExpenses' => $pendingExpenses,
            'payrollSummary' => $payrollSummary,
            'currentMonth' => $month,
        ]);
    }

    /**
     * Action : Approuver un frais
     */
    public function approveExpense(Expense $expense)
    {
        $expense->update(['status' => 'approved']);

        // ENVOI MAIL
        // On vérifie que l'user a un email valide pour ne pas faire planter l'app
        if ($expense->user->email) {
            Mail::to($expense->user)->send(new ExpenseStatusUpdated($expense));
        }

        return back()->with('success', 'Frais validé. E-mail envoyé.');
    }

    /**
     * Action : Rejeter un frais
     */
    public function rejectExpense(Request $request, Expense $expense)
    {
        $request->validate(['reason' => 'required|string|min:3']);

        $expense->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);

        // ENVOI MAIL
        if ($expense->user->email) {
            Mail::to($expense->user)->send(new ExpenseStatusUpdated($expense));
        }

        return back()->with('success', 'Frais rejeté. E-mail envoyé.');
    }
}