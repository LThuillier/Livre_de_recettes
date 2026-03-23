<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Nettoyer le cache des permissions (recommandé par Spatie)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Créer les permissions
        Permission::firstOrCreate(['name' => 'editer toutes les recettes']);
        Permission::firstOrCreate(['name' => 'supprimer toutes les recettes']);
        Permission::firstOrCreate(['name' => 'gerer ses propres recettes']);

        // 3. Créer les rôles et assigner les permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // L'admin a toutes les permissions
        $adminRole->givePermissionTo(['editer toutes les recettes', 'supprimer toutes les recettes', 'gerer ses propres recettes']);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        // L'utilisateur simple ne peut gérer que les siennes
        $userRole->givePermissionTo('gerer ses propres recettes');

        // Créer ou mettre à jour l'utilisateur Admin
        $admin = User::updateOrCreate(
            ['email' => 'adminrecette@gmail.com'],// Identifiant unique pour éviter les doublons
            [
                'name' => 'Admin',
                'password' => Hash::make('Administrateur1!'), // Assurez-vous de choisir un mot de passe sécurisé
            ]
        );

        // 5. Assigner le rôle à l'admin
        $admin->assignRole($adminRole);

        $this->command->info('Rôles, Permissions et Admin créés avec succès !');
    }
}