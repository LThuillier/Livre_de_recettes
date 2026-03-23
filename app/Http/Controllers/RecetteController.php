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
            if ($user->hasRole('admin')) {
            $recettes = Recette::with('user')->get();
            } else {
            // Un utilisateur connecté voit les recettes publiques 
            // OU ses propres recettes (même privées)
            $recettes = Recette::where('est_public', true)
                               ->orWhere('user_id', $user->id)
                               ->get();
            }
        } else {
            $recettes = Recette::where('est_public', true)->get();// CHANGEMENT : Affichage conditionnel selon l'authentification
        }

        return view('recettes.index', compact('recettes'));
    }

    public function create()
    {
        // On utilise le middleware 'auth' dans les routes normalement, 
        // mais cette sécurité directe est une bonne pratique.
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
        // Utilisation de ton RecetteRequest pour la validation
        $validated = $request->validated();

        $recette = new Recette();
        $recette->titre              = $request->titre;
        $recette->description        = $request->description;
        $recette->temps_preparation  = $request->temps_preparation;
        $recette->difficulte         = $request->difficulte;
        $recette->regime_alimentaire = $request->regime_alimentaire;
        
        // CHANGEMENT : On lie la recette à l'utilisateur connecté
        $recette->user_id            = Auth::id();

        // CHANGEMENT : Utilisation de Spatie pour définir la visibilité
        // Si l'utilisateur est admin, la recette est publique par défaut
        $recette->est_public = Auth::user()->hasRole('admin');

        $recette->save();

        $this->syncIngredients($recette, $request->input('ingredients', []));

        return redirect()->route('recettes.index')
                         ->with('success', 'Recette créée avec succès !');
    }

    public function show(Recette $recette)
    {
        $recette->load('ingredients');
        return view('recettes.recette', compact('recette'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Recette $recette)
    {        // CHANGEMENT : Seuls les admins ou le propriétaire peuvent éditer
        if (!Auth::user()->hasRole('admin') && Auth::id() !== $recette->user_id) {// Sécurité renforcée
            return redirect()->route('recettes.index')->with('error', 'Accès non autorisé');// Ou abort(403) pour une réponse HTTP plus appropriée
        }

        $recette->load('ingredients');// Chargement des ingrédients pour pré-remplir le formulaire
        return view('recettes.edit', compact('recette'));
    }

    /**
     * Mettre à jour une recette
     */
    public function update(RecetteRequest $request, Recette $recette)
    {
        // CHANGEMENT : Même sécurité que pour l'édition
        if (!Auth::user()->hasRole('admin') && Auth::id() !== $recette->user_id) {
            return abort(403);
        }

        $recette->update($request->only([
            'titre', 'description', 'temps_preparation', 'difficulte', 'regime_alimentaire'
        ]));

        $this->syncIngredients($recette, $request->input('ingredients', []));

        return redirect()->route('recettes.recette', $recette)
                         ->with('success', 'Recette modifiée avec succès !');
    }

    /**
     * Supprimer une recette
     */
    public function destroy(Recette $recette)
    {
        // CHANGEMENT : Sécurité Admin ou Propriétaire
        if (!Auth::user()->hasRole('admin') && Auth::id() !== $recette->user_id) {
            return abort(403);
        }

        $recette->ingredients()->detach(); 
        $recette->delete();

        return redirect()->route('recettes.index')
                         ->with('success', 'Recette supprimée avec succès !');
    }

    /**
     * Méthode privée : synchronise les ingrédients
     */
    private function syncIngredients(Recette $recette, array $ingredientsData): void
    {
        $pivot = [];
        foreach ($ingredientsData as $data) {
            if (empty($data['nom'])) continue;

            $ingredient = Ingredient::firstOrCreate(
                ['nom' => $data['nom']],
                ['unite_mesure' => $data['unite'] ?? 'unité']
            );

            $pivot[$ingredient->id] = [
                'quantite' => $data['quantite'],
                'unite'    => $data['unite'],
            ];
        }
        $recette->ingredients()->sync($pivot);
    }
}