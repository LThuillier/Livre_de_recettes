<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngredientRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // On autorise par défaut, la sécurité par rôles est gérée par le middleware ou le controller
        return true;
    }

    /**
     * Règles de validation pour les ingrédients.
     */
    public function rules(): array
    {
        return [
            'nom'      => 'required|string|max:255',
            'quantite' => 'required|numeric|min:0.01',
            'unite'    => 'required|string|max:50',
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'nom.required'      => 'Le nom de l\'ingrédient est obligatoire.',
            'quantite.required' => 'La quantité est nécessaire.',
            'quantite.numeric'  => 'La quantité doit être un nombre.',
            'unite.required'    => 'L\'unité (g, ml, unité...) est requise.',
        ];
    }
}