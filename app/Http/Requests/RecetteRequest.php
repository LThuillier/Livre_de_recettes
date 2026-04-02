<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RecetteRequest extends FormRequest
{
    /**
     * Seuls les utilisateurs connectés peuvent soumettre ce formulaire
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            // Champs de la recette
            'titre'              => 'required|string|max:50',
            'description'        => 'required|string|max:5000',
            'temps_preparation'  => 'required|integer|min:1',
            'portions'           => 'required|integer|min:1',
            'difficulte'         => 'required|in:facile,moyen,difficile',
            'regime_alimentaire' => 'required|in:normal,vegetarien,vegan,sans_gluten',

            // Ingrédients (optionnels mais si présents, chaque champ est requis)
            'ingredients'            => 'nullable|array',
            'ingredients.*.nom'      => 'required|string|max:50',
            'ingredients.*.quantite' => 'required|numeric|min:0',
            'ingredients.*.unite'    => 'required|string|max:50',// 'g', 'ml', 'unité' etc.
            'ingredients.*.nature'   => 'required|in:liquide,solide', // Nouveau champ pour différencier liquide/solide
        ];
    }

    /**
     * Messages d'erreur personnalisés en français
     */
    public function messages(): array
    {
        return [
            // Titre
            'titre.required'             => 'Le titre de la recette est obligatoire.',
            'titre.max'                  => 'Le titre ne peut pas dépasser 50 caractères.',

            // Description
            'description.required'       => 'La description est obligatoire.',

            // Temps de préparation
            'temps_preparation.required' => 'Le temps de préparation est obligatoire.',
            'temps_preparation.integer'  => 'Le temps de préparation doit être un nombre entier.',
            'temps_preparation.min'      => 'Le temps de préparation doit être d\'au moins 1 minute.',

            // Difficulté
            'difficulte.required'        => 'La difficulté est obligatoire.',
            'difficulte.in'              => 'La difficulté doit être : facile, moyen ou difficile.',

            // Régime alimentaire
            'regime_alimentaire.required' => 'Le régime alimentaire est obligatoire.',
            'regime_alimentaire.in'       => 'Le régime doit être : normal, végétarien, vegan ou sans gluten.',

            // Ingrédients
            'ingredients.array'              => 'Les ingrédients doivent être une liste.',
            'ingredients.*.nom.required'     => 'Le nom de chaque ingrédient est obligatoire.',
            'ingredients.*.nom.max'          => 'Le nom d\'un ingrédient ne peut pas dépasser 50 caractères.',
            'ingredients.*.quantite.required' => 'La quantité de chaque ingrédient est obligatoire.',
            'ingredients.*.quantite.numeric'  => 'La quantité doit être un nombre.',
            'ingredients.*.quantite.min'      => 'La quantité doit être positive.',
            'ingredients.*.unite.required'    => 'L\'unité de chaque ingrédient est obligatoire.',

            'ingredients.*.nature.required'   => 'Précisez si l\'ingrédient est liquide ou solide.',
            'ingredients.*.nature.in'         => 'La nature doit être liquide ou solide.',
        ];
    }
}
