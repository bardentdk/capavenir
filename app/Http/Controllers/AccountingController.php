<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Mail; // Plus besoin de la façade Mail directe
use App\Notifications\ExpenseProcessed; // <--- On utilise la Notification

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'accountant'])) {
            abort(403, 'Accès réservé au service comptabilité.');
        }

        $month = $request->input('month', now()->format('Y-m'));

        // 1. Frais en attente
        $pendingExpenses = Expense::with(['user', 'client'])
            ->where('status', 'pending')
            ->latest('expense_date')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'user_name' => $e->user->name,
                'type' => $e->type,
                'amount' => $e->amount,
                'date' => $e->expense_date->format('d/m/Y'),
                'description' => $e->type === 'mileage'
                    ? "Trajet {$e->distance_km} km"
                    : "Achat",
                'proof_url' => $e->proof_path ? \Illuminate\Support\Facades\Storage::url($e->proof_path) : null,
            ]);

        // 2. Synthèse Paie
        $payrollSummary = User::role('educator')
            ->withSum(['interventions' => function ($query) use ($month) {
                $query->where('status', 'validated')
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

        // 3. Historique
        $history = Expense::with(['user'])
            ->where('expense_date', 'like', "$month%")
            ->latest('expense_date')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'date' => $e->expense_date->format('d/m/Y'),
                'user_name' => $e->user->name,
                'type_label' => $e->type === 'mileage' ? '🚗 Kilométrique' : '💶 Achat',
                'description' => $e->type === 'mileage'
                    ? "{$e->distance_km} km ({$e->start_address} → {$e->end_address})"
                    : "Achat divers",
                'amount' => number_format($e->amount, 2, ',', ' '),
                'status' => $e->status,
                'proof_url' => $e->proof_path ? \Illuminate\Support\Facades\Storage::url($e->proof_path) : null,
            ]);

        return Inertia::render('Accounting/Dashboard', [
            'pendingExpenses' => $pendingExpenses,
            'payrollSummary' => $payrollSummary,
            'history' => $history,
            'currentMonth' => $month,
        ]);
    }

    public function approveExpense(Expense $expense)
    {
        $expense->update(['status' => 'approved']);

        // --- NOTIFICATION HYBRIDE ---
        if ($expense->user) {
            // Ceci envoie le mail ET crée la notif dans la base de données (cloche)
            $expense->user->notify(new ExpenseProcessed($expense));
        }

        return back()->with('success', 'Frais validé. Notification envoyée.');
    }

    public function rejectExpense(Request $request, Expense $expense)
    {
        $request->validate(['reason' => 'required|string|min:3']);

        $expense->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);

        // --- NOTIFICATION HYBRIDE ---
        if ($expense->user) {
            $expense->user->notify(new ExpenseProcessed($expense));
        }

        return back()->with('success', 'Frais rejeté. Notification envoyée.');
    }
}