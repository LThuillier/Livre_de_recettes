<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Recette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecetteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que les invités ne voient que les recettes publiques.
     * * @return void
     */
    public function test_invite_ne_voit_que_les_recettes_publiques(): void
    {
        Recette::factory()->create(['titre' => 'Recette Publique', 'est_public' => true]);
        Recette::factory()->create(['titre' => 'Recette Privée', 'est_public' => false]);

        $response = $this->get('/recettes');

        $response->assertStatus(200);
        $response->assertSee('Recette Publique');
        $response->assertDontSee('Recette Privée');
    }

    /**
     * Vérifie qu'un utilisateur ne peut pas éditer la recette d'un autre.
     * * @return void
     */
    public function test_utilisateur_ne_peut_pas_editer_recette_autrui(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $recette = Recette::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->get("/recettes/{$recette->id}/edit");

        $response->assertRedirect('/recettes');
        $response->assertSessionHas('error', 'Accès non autorisé');
    }
}