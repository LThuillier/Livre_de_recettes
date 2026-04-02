<?php

namespace App\Http\Controllers;

use App\Models\Recette;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RecetteRequest;

/**
 * Controller gérant la logique des Recettes, la sécurité Spatie, et la conversion des ingrédients.
 */
class RecetteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Logique de visibilité (Admin voit tout, User voit public + les siennes)
        if ($user) {
            if ($user->hasRole('admin')) {
                $recettes = Recette::with('user')->get();
            } else {
                $recettes = Recette::where('est_public', true)
                                   ->orWhere('user_id', $user->id)
                                   ->get();
            }
        } else {
            $recettes = Recette::where('est_public', true)->get();
        }

        return view('recettes.index', compact('recettes'));
    }

    public function create()
    {
        return view('recettes.create');
    }

    public function store(RecetteRequest $request)
    {
        // 1. On récupère les données propres et validées
        $validated = $request->validated();
        
        // 2. On crée la recette de manière très précise pour éviter les erreurs
        $recette = new Recette();
        $recette->titre              = $validated['titre'];
        $recette->description        = $validated['description'];
        $recette->temps_preparation  = $validated['temps_preparation'];
        $recette->portions           = $validated['portions'];
        $recette->difficulte         = $validated['difficulte'];
        $recette->regime_alimentaire = $validated['regime_alimentaire'];
        $recette->user_id            = Auth::id();
        $recette->est_public         = Auth::user()->hasRole('admin');
        
        $recette->save();

        // 3. On enregistre les ingrédients liés
        $ingredients = $request->input('ingredients', []);
        $this->syncIngredients($recette, $ingredients);

        return redirect()->route('recettes.index')
                         ->with('success', 'Recette créée !');
    }

    public function show(Recette $recette)
    {
        // On charge les ingrédients pour pouvoir les afficher sur la page
        $recette->load('ingredients');
        return view('recettes.recette', compact('recette'));
    }

    public function edit(Recette $recette)
    {
        if (!Auth::user()->hasRole('admin') && Auth::id() !== $recette->user_id) {
            return redirect()->route('recettes.index')->with('error', 'Accès non autorisé');
        }

        return view('recettes.edit', compact('recette'));
    }

    public function update(RecetteRequest $request, Recette $recette)
    {
        if (!Auth::user()->hasRole('admin') && Auth::id() !== $recette->user_id) {
            return abort(403);
        }

        $validated = $request->validated();

        // Mise à jour explicite pour éviter tout plantage de MassAssignment
        $recette->update([
            'titre'              => $validated['titre'],
            'description'        => $validated['description'],
            'temps_preparation'  => $validated['temps_preparation'],
            'portions'           => $validated['portions'],
            'difficulte'         => $validated['difficulte'],
            'regime_alimentaire' => $validated['regime_alimentaire'],
        ]);

        // On synchronise les ingrédients modifiés
        $ingredients = $request->input('ingredients', []);
        $this->syncIngredients($recette, $ingredients);

        return redirect()->route('recettes.recette', $recette)
                         ->with('success', 'Recette modifiée !');
    }

    public function destroy(Recette $recette)
    {
        if (!Auth::user()->hasRole('admin') && Auth::id() !== $recette->user_id) {
            return abort(403);
        }

        // On détache les liens en base avant de supprimer
        $recette->ingredients()->detach(); 
        $recette->delete();

        return redirect()->route('recettes.index')
                         ->with('success', 'Recette supprimée !');
    }

    /**
     * Méthode privée indispensable : synchronise les ingrédients et convertit les liquides
     */
    private function syncIngredients(Recette $recette, array $ingredientsData): void
    {
        $pivot = [];
        
        // Table des densités moyennes
        $densites = [
            'miel' => 1.4, 'sirop' => 1.3, 'huile' => 0.9, 'lait' => 1.03, 'eau' => 1.0, 'creme' => 0.95
        ];

        foreach ($ingredientsData as $data) {
            if (empty($data['nom'])) continue;

            $nom = strtolower(trim($data['nom']));
            
            // Sécurité : remplacement de la virgule par un point (ex: 0,5 -> 0.5)
            $quantiteStr = str_replace(',', '.', (string)$data['quantite']);
            $quantite = (float) $quantiteStr;
            
            $unite = strtolower(trim($data['unite']));
            $nature = $data['nature'] ?? 'solide';

            // Logique de conversion si c'est liquide
            if ($nature === 'liquide' && in_array($unite, ['cl', 'ml', 'l'])) {
                $coef = 1.0;
                foreach ($densites as $key => $val) {
                    if (str_contains($nom, $key)) { $coef = $val; break; }
                }
                
                $ml = ($unite === 'cl') ? $quantite * 10 : (($unite === 'l') ? $quantite * 1000 : $quantite);
                $quantite = $ml * $coef;
                $unite = 'g'; // On uniformise l'affichage en grammes
            }

            // CORRECTION CRUCIALE : On retire 'nature' d'ici car la colonne n'existe pas en DB !
            $ingredient = Ingredient::firstOrCreate(['nom' => $nom]);

            // On prépare les données pour la table pivot ingredient_recette
            $pivot[$ingredient->id] = [
                'quantite' => $quantite,
                'unite' => $unite,
            ];
        }
        
        // On met à jour la base de données
        $recette->ingredients()->sync($pivot);
    }
}