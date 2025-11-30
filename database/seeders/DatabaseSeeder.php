<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer les rôles
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Créer l'ADMINISTRATEUR
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@socialcare.local',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // 3. Créer un ÉDUCATEUR (Pour tes tests de saisie)
        $educ = User::create([
            'name' => 'Paul Éducateur',
            'email' => 'paul@socialcare.local',
            'password' => Hash::make('password'),
        ]);
        $educ->assignRole('educator');

        // 4. Créer un COMPTABLE (Pour tes tests de validation)
        $compta = User::create([
            'name' => 'Martine Compta',
            'email' => 'martine@socialcare.local',
            'password' => Hash::make('password'),
        ]);
        $compta->assignRole('accountant');

        // 5. Créer 10 ENFANTS fictifs
        Client::factory(10)->create();
    }
}