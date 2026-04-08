<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use PHPUnit\Framework\TestCase;

class IngredientTddTest extends TestCase
{
    public function test_est_liquide_retourne_true_pour_un_nom_liquide(): void
    {
        $ingredient = new Ingredient(['nom' => 'Huile d olive']);

        $this->assertTrue($ingredient->estLiquide());
    }

    public function test_est_liquide_retourne_false_pour_un_nom_solide(): void
    {
        $ingredient = new Ingredient(['nom' => 'Farine']);

        $this->assertFalse($ingredient->estLiquide());
    }
}
