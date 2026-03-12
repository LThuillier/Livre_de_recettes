<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role; // Import important pour le rôle

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie l'ajout d'une recette avec ses ingrédients.
     */
    public function test_creation_recette_avec_ingredients(): void
    {
        // 1. On crée le rôle dans la base de test
        Role::create(['name' => 'admin']);

        // 2. On crée l'admin
        $admin = User::factory()->create(['email' => 'adminrecette@gmail.com']);
        $admin->assignRole('admin');

        $donnees = [
            'titre' => 'Pâtes Carbonara',
            'description' => 'La vraie recette italienne',
            'temps_preparation' => 15,
            'difficulte' => 'facile',
            'regime_alimentaire' => 'normal',
            'ingredients' => [
                ['nom' => 'Pâtes', 'quantite' => 500, 'unite' => 'g'],
                ['nom' => 'Lardons', 'quantite' => 200, 'unite' => 'g'],
            ]
        ];

        // 3. On exécute l'action
        $response = $this->actingAs($admin)->post('/recettes', $donnees);

        // 4. On vérifie
        $this->assertDatabaseHas('recettes', ['titre' => 'Pâtes Carbonara']);
        $response->assertRedirect('/recettes');
    }
}