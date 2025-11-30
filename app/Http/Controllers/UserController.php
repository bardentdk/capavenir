<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeStaffMember;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs (L'équipe).
     */
    public function index()
    {
        // Sécurité : Vérifie que l'user est Admin ou Compta
        if (!Auth::user()->hasRole(['admin', 'accountant'])) {
            abort(403);
        }

        $users = User::with('roles')
            ->orderBy('name')
            ->paginate(10)
            ->through(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // On récupère le premier rôle (ex: "educator") et on le traduit pour l'affichage
                'role' => $user->roles->first() ? $user->roles->first()->name : 'Aucun',
                'created_at' => $user->created_at->format('d/m/Y'),
            ]);

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        if (!Auth::user()->hasRole(['admin', 'accountant'])) {
            abort(403);
        }

        return Inertia::render('Users/Create', [
            // On envoie la liste des rôles disponibles pour le select
            'roles' => [
                ['value' => 'educator', 'label' => 'Éducateur Spécialisé'],
                ['value' => 'accountant', 'label' => 'Comptabilité / Paie'],
                ['value' => 'admin', 'label' => 'Administrateur'],
            ]
        ]);
    }

    /**
     * Enregistrement du nouvel utilisateur.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'accountant'])) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,educator,accountant', // Sécurité des rôles
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assignation du rôle (Package Spatie)
        $user->assignRole($request->role);

        try {
            Mail::to($user)->send(new WelcomeStaffMember($user, $request->password));
        } catch (\Exception $e) {
            // On ne fait pas planter la création si le mail échoue, mais on peut logger l'erreur
            \Illuminate\Support\Facades\Log::error("Erreur envoi mail bienvenue : " . $e->getMessage());
        }

        return redirect()->route('users.index')->with('success', 'Compte créé et invitation envoyée par e-mail.');

        // return redirect()->route('users.index')->with('success', 'Nouveau collaborateur créé avec succès.');
    }
}