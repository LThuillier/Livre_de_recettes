<?php

use App\Http\Controllers\RecetteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Routes d'authentification
Auth::routes();

// Routes pour les recettes (CRUD personnalisé)
Route::get('/recettes', [RecetteController::class, 'index'])->name('recettes.index');
Route::get('/recettes/create', [RecetteController::class, 'create'])->name('recettes.create');
Route::post('/recettes', [RecetteController::class, 'store'])->name('recettes.store');
Route::get('/recettes/{recette}', [RecetteController::class, 'show'])->name('recettes.recette'); // Changé ici
Route::get('/recettes/{recette}/edit', [RecetteController::class, 'edit'])->name('recettes.edit');
Route::put('/recettes/{recette}', [RecetteController::class, 'update'])->name('recettes.update');
Route::delete('/recettes/{recette}', [RecetteController::class, 'destroy'])->name('recettes.destroy');

// Page d'accueil
Route::get('/', function () {
    return redirect()->route('recettes.index');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

