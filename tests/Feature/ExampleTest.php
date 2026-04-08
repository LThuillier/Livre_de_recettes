<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_accueil_redirige_vers_la_liste_des_recettes(): void
    {
        $this->get('/')->assertRedirect('/recettes');
    }
}
