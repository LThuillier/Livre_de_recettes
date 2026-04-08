<?php

namespace Tests\Unit;

use App\Models\Recette;
use PHPUnit\Framework\TestCase;

class RecetteTddTest extends TestCase
{
    public function test_temps_formatte_retourne_des_minutes_si_inferieur_a_60(): void
    {
        $recette = new Recette(['temps_preparation' => 45]);

        $this->assertSame('45 min', $recette->temps_formatte);
    }

    public function test_temps_formatte_retourne_des_heures_pour_un_multiple_de_60(): void
    {
        $recette = new Recette(['temps_preparation' => 120]);

        $this->assertSame('2h', $recette->temps_formatte);
    }

    public function test_temps_formatte_retourne_heures_et_minutes_si_necessaire(): void
    {
        $recette = new Recette(['temps_preparation' => 135]);

        $this->assertSame('2h 15min', $recette->temps_formatte);
    }
}
