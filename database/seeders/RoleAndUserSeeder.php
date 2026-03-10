<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer les rôles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        // 2. Créer l'utilisateur Admin
        $admin = User::updateOrCreate(
            ['email' => 'adminrecette@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Administrateur1!'), // Remets ton mot de passe
            ]
        );

        // 3. Assigner le rôle
        $admin->assignRole($adminRole);
    }
}