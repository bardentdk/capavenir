<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpenseController;

// Redirection racine
Route::get('/', function () {
    return redirect()->route('login');
});

// --- AUTHENTIFICATION ---
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// --- APP PROTÉGÉE ---
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // GESTION UTILISATEURS (Déplacé ici pour la sécurité + Full CRUD)
    // On enlève "->only" pour avoir accès à edit/update/destroy
    Route::resource('users', UserController::class);

    // SECTION COMPTABILITÉ
    Route::prefix('accounting')->group(function () {
        Route::get('/', [AccountingController::class, 'index'])->name('accounting.index');
        Route::patch('/expenses/{expense}/approve', [AccountingController::class, 'approveExpense'])->name('accounting.approve');
        Route::patch('/expenses/{expense}/reject', [AccountingController::class, 'rejectExpense'])->name('accounting.reject');
    });

    // PROFIL UTILISATEUR
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ROUTES INTERVENTIONS
    Route::get('/interventions', [InterventionController::class, 'index'])->name('interventions.index');
    Route::get('/interventions/create', [InterventionController::class, 'create'])->name('interventions.create');
    Route::post('/interventions', [InterventionController::class, 'store'])->name('interventions.store');
    Route::post('/interventions/generate-ai', [InterventionController::class, 'generateReport'])->name('interventions.generate');
    Route::get('/interventions/{intervention}', [InterventionController::class, 'show'])->name('interventions.show');
    Route::patch('/interventions/{intervention}/validate', [InterventionController::class, 'validateIntervention'])->name('interventions.validate');
    Route::get('/interventions/{intervention}/pdf', [InterventionController::class, 'downloadPdf'])->name('interventions.pdf');

    // GESTION DES PLANNINGS
    Route::resource('planning', PlanningController::class);

    // GESTION DOCUMENTAIRE (GED)
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // NOTIFICATIONS
    Route::post('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markRead');

    // GESTION DES FRAIS
    Route::post('/expenses/calculate-distance', [ExpenseController::class, 'calculateDistance'])->name('expenses.distance');
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store']);

    // GESTION BÉNÉFICIAIRES
    Route::resource('clients', ClientController::class);
});