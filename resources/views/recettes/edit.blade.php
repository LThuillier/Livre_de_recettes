@extends('layouts.app')

@section('title', 'Modifier ' . $recette->titre)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4">Modifier "{{ $recette->titre }}"</h1>
            <a href="{{ route('recettes.recette', $recette) }}" class="btn btn-outline-secondary btn-sm">← Retour</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Modifier la recette</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('recettes.update', $recette) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Infos générales --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Titre *</label>
                        <input type="text" class="form-control @error('titre') is-invalid @enderror"
                               name="titre" value="{{ old('titre', $recette->titre) }}" required>
                        @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description', $recette->description) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Temps de préparation (min) *</label>
                            <input type="number" class="form-control" name="temps_preparation"
                                   value="{{ old('temps_preparation', $recette->temps_preparation) }}" required min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Difficulté *</label>
                            <select class="form-select" name="difficulte" required>
                                @foreach(['facile', 'moyen', 'difficile'] as $d)
                                    <option value="{{ $d }}"
                                        {{ old('difficulte', $recette->difficulte) == $d ? 'selected' : '' }}>
                                        {{ ucfirst($d) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Régime alimentaire</label>
                            <select class="form-select" name="regime_alimentaire">
                                @foreach(['normal' => 'Normal', 'vegetarien' => 'Végétarien', 'vegan' => 'Vegan', 'sans_gluten' => 'Sans gluten'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('regime_alimentaire', $recette->regime_alimentaire) == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
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
                        {{-- Ingrédients existants pré-remplis --}}
                        @foreach($recette->ingredients as $i => $ingredient)
                        <div class="ingredient-row card card-body mb-2 py-2 px-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small mb-1">Nom *</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="ingredients[{{ $i }}][nom]"
                                           value="{{ old("ingredients.$i.nom", $ingredient->nom) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Quantité *</label>
                                    <input type="number" step="0.1" class="form-control form-control-sm"
                                           name="ingredients[{{ $i }}][quantite]"
                                           value="{{ old("ingredients.$i.quantite", $ingredient->pivot->quantite) }}" required min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Unité *</label>
                                    <select class="form-select form-select-sm" name="ingredients[{{ $i }}][unite]">
                                        @foreach(['g', 'kg', 'ml', 'l', 'tsp', 'tbsp', 'pièce', 'pincée'] as $unite)
                                            <option value="{{ $unite }}"
                                                {{ old("ingredients.$i.unite", $ingredient->pivot->unite) == $unite ? 'selected' : '' }}>
                                                {{ $unite }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm supprimerIngredient" title="Supprimer">✕</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <p id="aucunIngredient" class="text-muted fst-italic small" style="{{ $recette->ingredients->count() > 0 ? 'display:none' : '' }}">
                        Aucun ingrédient ajouté. Cliquez sur le bouton pour en ajouter.
                    </p>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success text-white">Enregistrer les modifications</button>
                        <a href="{{ route('recettes.recette', $recette) }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Template d'une ligne ingrédient vide (caché) --}}
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
    // L'index de départ = nombre d'ingrédients existants pour éviter les collisions
    let ingredientIndex = {{ $recette->ingredients->count() }};
    const liste    = document.getElementById('ingredientsList');
    const aucun    = document.getElementById('aucunIngredient');
    const template = document.getElementById('ingredientTemplate');

    function updateAucun() {
        aucun.style.display = liste.children.length === 0 ? 'block' : 'none';
    }

    // Gérer les boutons supprimer des ingrédients déjà chargés
    document.querySelectorAll('.supprimerIngredient').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.ingredient-row').remove();
            updateAucun();
        });
    });

    document.getElementById('ajouterIngredient').addEventListener('click', () => {
        const clone = template.content.cloneNode(true);
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__INDEX__', ingredientIndex);
        });
        clone.querySelector('.supprimerIngredient').addEventListener('click', function () {
            this.closest('.ingredient-row').remove();
            updateAucun();
        });
        liste.appendChild(clone);
        ingredientIndex++;
        updateAucun();
    });
</script>
@endsection