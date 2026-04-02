<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recette;
use Illuminate\Http\Request;

/**
 * Controller dédié à la gestion autonome des Ingrédients.
 */
class IngredientController extends Controller
{
    // Affiche la liste globale de tous les ingrédients
    public function index()
    {
        $ingredients = Ingredient::withCount('recettes')->get();
        return view('ingredients.index', compact('ingredients'));
    }

    // NOUVEAU : Affiche le formulaire d'ajout
    public function create()
    {
        return view('ingredients.create');
    }

    // NOUVEAU : Enregistre l'ingrédient dans la base de données
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'unite_mesure' => 'nullable|string|max:50'
        ]);

        Ingredient::create($request->only('nom', 'unite_mesure'));

        return redirect()->route('ingredients.index')->with('success', 'Ingrédient ajouté avec succès !');
    }

    // Gère la liaison entre un ingrédient et une recette
    public function attachToRecette(Request $request, Recette $recette)
    {
        $request->validate([
            'nom' => 'required|string',
            'quantite' => 'required|numeric',
            'unite' => 'required|string'
        ]);

        $ingredient = Ingredient::firstOrCreate(
            ['nom' => $request->nom],
            ['unite_mesure' => $request->unite]
        );

        $recette->ingredients()->attach($ingredient->id, [
            'quantite' => $request->quantite,
            'unite' => $request->unite
        ]);

        return back()->with('success', 'Ingrédient ajouté à la recette !');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'unite_mesure' => 'required|string|max:50'
        ]);

        $ingredient->update($request->only('nom', 'unite_mesure'));

        return redirect()->route('ingredients.index')->with('success', 'Ingrédient mis à jour !');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->recettes()->detach(); // Détache les relations avec les recettes
        $ingredient->delete();

        return redirect()->route('ingredients.index')->with('success', 'Ingrédient supprimé !');
    }   
}