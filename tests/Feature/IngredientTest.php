<?php

namespace Tests\Feature;

use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_peut_creer_une_recette_avec_ingredients(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'titre' => 'Pates Carbonara',
            'description' => 'La vraie recette italienne',
            'temps_preparation' => 15,
            'portions' => 4,
            'difficulte' => 'facile',
            'regime_alimentaire' => 'normal',
            'ingredients' => [
                ['nom' => 'Pates', 'quantite' => 500, 'unite' => 'g', 'nature' => 'solide'],
                ['nom' => 'Lardons', 'quantite' => 200, 'unite' => 'g', 'nature' => 'solide'],
            ],
        ];

        $response = $this->actingAs($admin)->post('/recettes', $payload);

        $response->assertRedirect('/recettes');

        $this->assertDatabaseHas('recettes', [
            'titre' => 'Pates Carbonara',
            'user_id' => $admin->id,
            'est_public' => true,
            'portions' => 4,
        ]);

        $this->assertDatabaseHas('ingredients', ['nom' => 'pates']);
        $this->assertDatabaseHas('ingredients', ['nom' => 'lardons']);

        $recette = Recette::where('titre', 'Pates Carbonara')->firstOrFail();
        $this->assertCount(2, $recette->ingredients);
    }
}
