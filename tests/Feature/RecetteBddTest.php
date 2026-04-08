<?php

namespace Tests\Feature;

use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RecetteBddTest extends TestCase
{
    use RefreshDatabase;

    public function test_utilisateur_standard_voit_les_recettes_publiques_et_les_siennes(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Recette::factory()->create([
            'titre' => 'Recette publique',
            'user_id' => $otherUser->id,
            'est_public' => true,
        ]);

        Recette::factory()->create([
            'titre' => 'Privee autre utilisateur',
            'user_id' => $otherUser->id,
            'est_public' => false,
        ]);

        Recette::factory()->create([
            'titre' => 'Ma recette privee',
            'user_id' => $user->id,
            'est_public' => false,
        ]);

        $response = $this->actingAs($user)->get('/recettes');

        $response->assertOk();
        $response->assertSee('Recette publique');
        $response->assertSee('Ma recette privee');
        $response->assertDontSee('Privee autre utilisateur');
    }

    public function test_admin_voit_toutes_les_recettes(): void
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $otherUser = User::factory()->create();

        Recette::factory()->create([
            'titre' => 'Recette publique admin',
            'user_id' => $otherUser->id,
            'est_public' => true,
        ]);

        Recette::factory()->create([
            'titre' => 'Recette privee admin',
            'user_id' => $otherUser->id,
            'est_public' => false,
        ]);

        $response = $this->actingAs($admin)->get('/recettes');

        $response->assertOk();
        $response->assertSee('Recette publique admin');
        $response->assertSee('Recette privee admin');
    }

    public function test_creation_convertit_un_ingredient_liquide_en_grammes(): void
    {
        $user = User::factory()->create();

        $payload = [
            'titre' => 'Sauce au miel',
            'description' => 'Sauce rapide pour salade',
            'temps_preparation' => 10,
            'portions' => 2,
            'difficulte' => 'facile',
            'regime_alimentaire' => 'vegetarien',
            'ingredients' => [
                [
                    'nom' => 'Miel',
                    'quantite' => 10,
                    'unite' => 'cl',
                    'nature' => 'liquide',
                ],
            ],
        ];

        $response = $this->actingAs($user)->post('/recettes', $payload);

        $response->assertRedirect('/recettes');

        $recette = Recette::query()->where('titre', 'Sauce au miel')->firstOrFail();
        $miel = $recette->ingredients()->where('nom', 'miel')->first();

        $this->assertNotNull($miel);
        $this->assertSame('g', $miel->pivot->unite);
        $this->assertEqualsWithDelta(140.0, (float) $miel->pivot->quantite, 0.01);
    }
}
