<?php

namespace App\Http\Controllers;

use App\Models\Recette;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RecetteRequest;

class RecetteController extends Controller
{
    /**
     * Afficher la liste des recettes
     */
    public function index()
    {
        $user = Auth::user();

        if ($user) {
            // Utilisateur connecté : ses recettes + les recettes publiques
            $recettes = Recette::where('est_public', true)
                               ->orWhere('user_id', $user->id)
                               ->get();
        } else {
            // Invité : uniquement les recettes publiques
            $recettes = Recette::where('est_public', true)->get();
        }

        return view('recettes.index', compact('recettes'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('recettes.create');
    }

    /**
     * Enregistrer une nouvelle recette
     */
    public function store(RecetteRequest $request)
    {
        $request->validate([
            'titre'              => 'required|string|max:255',
            'description'        => 'required|string',
            'temps_preparation'  => 'required|integer|min:1',
            'difficulte'         => 'required|in:facile,moyen,difficile',
            'regime_alimentaire' => 'required|in:normal,vegetarien,vegan,sans_gluten',
            // Validation des ingrédients (optionnels)
            'ingredients'            => 'nullable|array',
            'ingredients.*.nom'      => 'required_with:ingredients|string|max:255',
            'ingredients.*.quantite' => 'required_with:ingredients|numeric|min:0',
            'ingredients.*.unite'    => 'required_with:ingredients|string|max:50',
        ]);

        // Création de la recette
        $recette = new Recette();
        $recette->titre              = $request->titre;
        $recette->description        = $request->description;
        $recette->temps_preparation  = $request->temps_preparation;
        $recette->difficulte         = $request->difficulte;
        $recette->regime_alimentaire = $request->regime_alimentaire;
        $recette->user_id            = Auth::id();

        // L'admin crée des recettes publiques, les autres des recettes privées
        $recette->est_public = (Auth::user()->email === 'adminrecette@gmail.com');

        $recette->save();

        // Sauvegarde des ingrédients
        $this->syncIngredients($recette, $request->input('ingredients', []));

        return redirect()->route('recettes.index')
                         ->with('success', 'Recette créée avec succès !');
    }

    /**
     * Afficher une recette spécifique
     */
    public function show(Recette $recette)
    {
        $recette->load('ingredients');
        return view('recettes.recette', compact('recette'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Recette $recette)
    {
        if (Auth::id() !== $recette->user_id) {
            return redirect()->route('recettes.index')->with('error', 'Accès non autorisé');
        }

        $recette->load('ingredients');
        return view('recettes.edit', compact('recette'));
    }

    /**
     * Mettre à jour une recette
     */
    public function update(RecetteRequest $request, Recette $recette)
    {
        if (Auth::id() !== $recette->user_id) {
            return abort(403);
        }

        $request->validate([
            'titre'              => 'required|string|max:255',
            'description'        => 'required|string',
            'temps_preparation'  => 'required|integer|min:1',
            'difficulte'         => 'required|in:facile,moyen,difficile',
            'regime_alimentaire' => 'required|in:normal,vegetarien,vegan,sans_gluten',
            // Validation des ingrédients (optionnels)
            'ingredients'            => 'nullable|array',
            'ingredients.*.nom'      => 'required_with:ingredients|string|max:255',
            'ingredients.*.quantite' => 'required_with:ingredients|numeric|min:0',
            'ingredients.*.unite'    => 'required_with:ingredients|string|max:50',
        ]);

        $recette->update($request->only([
            'titre', 'description', 'temps_preparation', 'difficulte', 'regime_alimentaire'
        ]));

        // Mise à jour des ingrédients (remplace tout)
        $this->syncIngredients($recette, $request->input('ingredients', []));

        return redirect()->route('recettes.recette', $recette)
                         ->with('success', 'Recette modifiée avec succès !');
    }

    /**
     * Supprimer une recette
     */
    public function destroy(Recette $recette)
    {
        if (Auth::id() !== $recette->user_id) {
            return abort(403);
        }

        $recette->ingredients()->detach(); // Nettoyer la table pivot avant suppression
        $recette->delete();

        return redirect()->route('recettes.index')
                         ->with('success', 'Recette supprimée avec succès !');
    }

    /* 
    * Méthode privée : synchronise les ingrédients d'une recette
    */
    private function syncIngredients(Recette $recette, array $ingredientsData): void
    {
        $pivot = [];

        foreach ($ingredientsData as $data) {
            if (empty($data['nom'])) continue;

            // Chercher l'ingrédient par nom ou le créer s'il n'existe pas
            $ingredient = Ingredient::firstOrCreate(
                ['nom' => $data['nom']],
                ['unite_mesure' => $data['unite']]
            );

            $pivot[$ingredient->id] = [
                'quantite' => $data['quantite'],
                'unite'    => $data['unite'],
            ];
        }

        // sync() supprime les anciens liens et insère les nouveaux
        $recette->ingredients()->sync($pivot);
    }
}
