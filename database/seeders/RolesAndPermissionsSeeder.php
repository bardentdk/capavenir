<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialiser le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Création des rôles
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleEduc = Role::create(['name' => 'educator']);
        $roleAccountant = Role::create(['name' => 'accountant']); // Comptabilité

        // (Optionnel) Création de permissions spécifiques si besoin plus tard
        // Permission::create(['name' => 'validate interventions']);

        // $roleAccountant->givePermissionTo('validate interventions');
    }
}