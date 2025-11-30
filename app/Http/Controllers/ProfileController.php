<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Affiche la page de profil.
     */
    public function edit(Request $request)
    {
        return Inertia::render('Profile/Edit', [
            'status' => session('status'),
            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'avatar' => Auth::user()->avatar_path ? Storage::url(Auth::user()->avatar_path) : null,
            ]
        ]);
    }

    /**
     * Met à jour les infos générales (Nom, Email, Avatar).
     */
    // public function update(Request $request)
    // {
    //     $user = Auth::user();

    //     $validated = $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
    //         'photo' => ['nullable', 'image', 'max:1024'], // 1MB Max
    //     ]);

    //     // Gestion de l'avatar
    //     if ($request->hasFile('photo')) {
    //         // Suppression de l'ancien si existe
    //         if ($user->avatar_path) {
    //             Storage::disk('public')->delete($user->avatar_path);
    //         }

    //         // Upload du nouveau
    //         $path = $request->file('photo')->store('avatars', 'public');
    //         $user->avatar_path = $path;
    //     }

    //     $user->name = $validated['name'];
    //     $user->email = $validated['email'];
    //     $user->save();

    //     return back()->with('success', 'Profil mis à jour.');
    // }
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:5120'], // 5MB Max
        ]);

        // Gestion de l'avatar (FIX WINDOWS)
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // Suppression de l'ancien si existe
            if ($user->avatar_path) {
                if (Storage::disk('public')->exists($user->avatar_path)) {
                    Storage::disk('public')->delete($user->avatar_path);
                }
            }

            try {
                // 1. Génération du nom unique
                $extension = $file->getClientOriginalExtension();
                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $extension;
                $destinationPath = 'avatars/' . $filename;

                // 2. Écriture manuelle
                Storage::disk('public')->put($destinationPath, file_get_contents($file));

                $user->avatar_path = $destinationPath;

            } catch (\Exception $e) {
                return back()->withErrors(['photo' => "Erreur lors de l'upload de l'image."]);
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return back()->with('success', 'Profil mis à jour.');
    }

    /**
     * Met à jour le mot de passe.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}