<?php

use App\Http\Controllers\RecetteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IngredientController;

// Routes d'authentification
Auth::routes();

// Routes pour les recettes (CRUD personnalisé)
Route::get('/recettes', [RecetteController::class, 'index'])->name('recettes.index');// Route pour afficher la liste des recettes
Route::get('/recettes/create', [RecetteController::class, 'create'])->name('recettes.create');// Route pour afficher le formulaire de création d'une recette
Route::post('/recettes', [RecetteController::class, 'store'])->name('recettes.store');// Route pour enregistrer une nouvelle recette
Route::get('/recettes/{recette}', [RecetteController::class, 'show'])->name('recettes.recette'); // Route pour afficher les détails d'une recette
Route::get('/recettes/{recette}/edit', [RecetteController::class, 'edit'])->name('recettes.edit');// Route pour afficher le formulaire d'édition d'une recette
Route::put('/recettes/{recette}', [RecetteController::class, 'update'])->name('recettes.update');// Route pour mettre à jour une recette
Route::delete('/recettes/{recette}', [RecetteController::class, 'destroy'])->name('recettes.destroy');// Route pour supprimer une recette

// Page d'accueil
Route::get('/', function () {
    return redirect()->route('recettes.index');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin/dashboard', [AdminController::class, 'index'])->middleware('role:admin'); // Route protégée pour les administrateurs

Route::resource('ingredients', IngredientController::class);/// Routes pour la gestion autonome des ingrédients (CRUD complet)


Route::resource('recettes', RecetteController::class)->names([
    'show' => 'recettes.recette'
]);

Route::resource('ingredients', IngredientController::class);

// Dashboard Admin (Optionnel, protégé par le middleware de Spatie)
Route::get('/admin/dashboard', function() {
    return view('admin.dashboard');
})->middleware('role:admin')->name('admin.dashboard');
