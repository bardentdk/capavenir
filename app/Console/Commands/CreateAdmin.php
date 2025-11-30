<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'user:create-admin';
    protected $description = 'Créer un administrateur en production';

    public function handle()
    {
        $this->info('Création du compte Administrateur');

        $name = $this->ask('Nom complet');
        $email = $this->ask('Adresse E-mail');
        // "secret" permet de taper le mot de passe sans qu'il s'affiche à l'écran
        $password = $this->secret('Mot de passe');

        if (User::where('email', $email)->exists()) {
            $this->error('Cet email existe déjà !');
            return;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->assignRole('admin');

        $this->info("Succès ! L'utilisateur {$name} ({$email}) est maintenant Admin.");
    }
}