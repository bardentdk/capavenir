<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InterventionController;

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
Route::resource('users', \App\Http\Controllers\UserController::class)->only(['index', 'create', 'store']);
// SECTION COMPTABILITÉ
Route::prefix('accounting')->middleware(['auth'])->group(function () {
    Route::get('/', [AccountingController::class, 'index'])->name('accounting.index');
    Route::patch('/expenses/{expense}/approve', [AccountingController::class, 'approveExpense'])->name('accounting.approve');
    Route::patch('/expenses/{expense}/reject', [AccountingController::class, 'rejectExpense'])->name('accounting.reject');
});
// --- APP PROTÉGÉE ---
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // ROUTES INTERVENTIONS (CRUD)
    Route::get('/interventions', [InterventionController::class, 'index'])->name('interventions.index');
    Route::get('/interventions/create', [InterventionController::class, 'create'])->name('interventions.create');
    Route::post('/interventions', [InterventionController::class, 'store'])->name('interventions.store');
    Route::post('/interventions/generate-ai', [InterventionController::class, 'generateReport'])->name('interventions.generate');
    Route::get('/interventions/{intervention}', [InterventionController::class, 'show'])->name('interventions.show');
    Route::patch('/interventions/{intervention}/validate', [InterventionController::class, 'validateIntervention'])->name('interventions.validate');
    Route::get('/interventions/{intervention}/pdf', [InterventionController::class, 'downloadPdf'])->name('interventions.pdf');

    // Placeholders restants
    // Route::get('/expenses', function () { return Inertia::render('Dashboard'); });
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class)->only(['index', 'create', 'store']);
    Route::get('/clients', function () { return Inertia::render('Dashboard'); });
    Route::resource('clients', ClientController::class);
});