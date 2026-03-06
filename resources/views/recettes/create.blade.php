@extends('layouts.app')

@section('title', 'Créer une recette')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Nouvelle Recette</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('recettes.store') }}" method="POST">
                    @csrf

                    {{-- Infos générales --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Titre *</label>
                        <input type="text" class="form-control @error('titre') is-invalid @enderror"
                               name="titre" value="{{ old('titre') }}" required>
                        @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Temps de préparation (min) *</label>
                            <input type="number" class="form-control" name="temps_preparation"
                                   value="{{ old('temps_preparation') }}" required min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Difficulté *</label>
                            <select class="form-select" name="difficulte" required>
                                <option value="facile"    {{ old('difficulte') == 'facile'    ? 'selected' : '' }}>Facile</option>
                                <option value="moyen"     {{ old('difficulte') == 'moyen'     ? 'selected' : '' }}>Moyen</option>
                                <option value="difficile" {{ old('difficulte') == 'difficile' ? 'selected' : '' }}>Difficile</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Régime alimentaire</label>
                            <select class="form-select" name="regime_alimentaire">
                                <option value="normal"      {{ old('regime_alimentaire') == 'normal'      ? 'selected' : '' }}>Normal</option>
                                <option value="vegetarien"  {{ old('regime_alimentaire') == 'vegetarien'  ? 'selected' : '' }}>Végétarien</option>
                                <option value="vegan"       {{ old('regime_alimentaire') == 'vegan'       ? 'selected' : '' }}>Vegan</option>
                                <option value="sans_gluten" {{ old('regime_alimentaire') == 'sans_gluten' ? 'selected' : '' }}>Sans gluten</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    {{-- Section ingrédients --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">🛒 Ingrédients</h6>
                        <button type="button" class="btn btn-outline-success btn-sm" id="ajouterIngredient">
                            + Ajouter un ingrédient
                        </button>
                    </div>

                    <div id="ingredientsList">
                        {{-- Les lignes d'ingrédients sont injectées ici --}}
                    </div>

                    <p id="aucunIngredient" class="text-muted fst-italic small">
                        Aucun ingrédient ajouté. Cliquez sur le bouton pour en ajouter.
                    </p>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success">Enregistrer la recette</button>
                        <a href="{{ route('recettes.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Template d'une ligne ingrédient (caché) --}}
<template id="ingredientTemplate">
    <div class="ingredient-row card card-body mb-2 py-2 px-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-1">Nom *</label>
                <input type="text" class="form-control form-control-sm"
                       name="ingredients[__INDEX__][nom]" placeholder="ex: Farine" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Quantité *</label>
                <input type="number" step="0.1" class="form-control form-control-sm"
                       name="ingredients[__INDEX__][quantite]" placeholder="ex: 200" required min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Unité *</label>
                <select class="form-select form-select-sm" name="ingredients[__INDEX__][unite]">
                    <option value="g">g</option>
                    <option value="kg">kg</option>
                    <option value="ml">ml</option>
                    <option value="l">l</option>
                    <option value="tsp">c. à café</option>
                    <option value="tbsp">c. à soupe</option>
                    <option value="pièce">pièce(s)</option>
                    <option value="pincée">pincée(s)</option>
                </select>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm supprimerIngredient" title="Supprimer">✕</button>
            </div>
        </div>
    </div>
</template>

<script>
    let ingredientIndex = 0;
    const liste     = document.getElementById('ingredientsList');
    const aucun     = document.getElementById('aucunIngredient');
    const template  = document.getElementById('ingredientTemplate');

    function updateAucun() {
        aucun.style.display = liste.children.length === 0 ? 'block' : 'none';
    }

    document.getElementById('ajouterIngredient').addEventListener('click', () => {
        const clone = template.content.cloneNode(true);
        // Remplacer __INDEX__ par l'index courant
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__INDEX__', ingredientIndex);
        });
        // Bouton supprimer
        clone.querySelector('.supprimerIngredient').addEventListener('click', function () {
            this.closest('.ingredient-row').remove();
            updateAucun();
        });
        liste.appendChild(clone);
        ingredientIndex++;
        updateAucun();
    });

    updateAucun();
</script>
@endsection