<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
    // Appel du seeder RecetteSeeder
        $this->call(RecetteSeeder::class);
        //crerer un utilisateur
        User::factory()->create([ // Crée un utilisateur avec des données spécifiques
            'name' => 'Test User', // Remplacez par le nom souhaité
            'email' => 'test@example.com', // Remplacez par l'email souhaité
        ]);
    }
}
