<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Mail\WelcomeStaffMember;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs (L'équipe).
     */
    public function index()
    {
        if (!Auth::user()->hasRole(['admin', 'accountant'])) abort(403);

        $users = User::with('roles')
            ->orderBy('name')
            ->paginate(10)
            ->through(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_path' => $user->avatar_path ? \Illuminate\Support\Facades\Storage::url($user->avatar_path) : null,
                'role' => $user->roles->first() ? $user->roles->first()->name : 'Aucun',
                'created_at' => $user->created_at->format('d/m/Y'),

                // NOUVEAU : Format "Il y a 5 min"
                'last_login' => $user->last_login_at
                    ? $user->last_login_at->diffForHumans()
                    : 'Jamais',
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

    public function edit(User $user)
    {
        if (!Auth::user()->hasRole(['admin'])) abort(403); // Seul admin modifie les autres

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first() ? $user->roles->first()->name : 'educator',
            ],
            'roles' => [
                ['value' => 'educator', 'label' => 'Éducateur Spécialisé'],
                ['value' => 'accountant', 'label' => 'Comptabilité / Paie'],
                ['value' => 'admin', 'label' => 'Administrateur'],
            ]
        ]);
    }
    public function update(Request $request, User $user)
    {
        if (!Auth::user()->hasRole(['admin'])) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            // On ignore l'email de l'utilisateur actuel pour la vérification d'unicité
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,educator,accountant',
            'password' => 'nullable|string|min:8|confirmed', // Optionnel
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // On ne change le mot de passe que s'il est rempli
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Mise à jour du rôle
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour.');
    }
    public function destroy(User $user)
    {
        if (!Auth::user()->hasRole(['admin'])) abort(403);

        // On s'empêche de se supprimer soi-même
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Soft Delete (Archivage)
        $user->delete();

        return back()->with('success', 'Compte désactivé et archivé (Historique conservé).');
    }
}