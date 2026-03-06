<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecetteFactory extends Factory
{
    public function definition(): array
    {
        // Générer une description toujours supérieur a  5 caractères
        $description = $this->faker->paragraph();
        while (mb_strlen($description) < 6) {
            $description = $this->faker->paragraph();
        }

        // Calculer la longueur maximale du titre 2/ moitié de la description
        $maxTitreLen = (int) floor(mb_strlen($description) / 2);
        $maxTitreLen = max(1, $maxTitreLen); // au moins 1 caractère

        // Générer un titre valide
        $words = $this->faker->words(10, true); // phrase candidate
        $titre = mb_substr($words, 0, $maxTitreLen);

        // Si le titre est vide, fallback sur un mot simple
        if (mb_strlen(trim($titre)) === 0) {
            $titre = $this->faker->word();
            $titre = mb_substr($titre, 0, $maxTitreLen);
        }

        return [
            'titre' => $titre,
            'description' => $description,
            'temps_preparation' => $this->faker->numberBetween(1, 1200),
            'difficulte' => $this->faker->randomElement(['facile', 'moyenne', 'difficile']),
            'regime_alimentaire' => $this->faker->randomElement(['normal','Végétarien','Vegan', 'sans_gluten']),
        ];
    }
}
