@extends('recettes.layout')

@section('title', 'Créer une recette')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4">Nouvelle Recette</h1>
            <a href="{{ route('recettes.index') }}" class="btn btn-outline-secondary btn-sm">← Retour</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Créer la recette</h5>
            </div>
            <div class="card-body">

                {{-- Affichage des erreurs pour savoir pourquoi l'enregistrement bloque --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Attention :</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('recettes.store') }}" method="POST">
                    @csrf
                    {{-- ATTENTION: Pas de @method('PUT') ici, c'est une création ! --}}

                    {{-- Infos générales --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Titre *</label>
                        <input type="text" class="form-control @error('titre') is-invalid @enderror"
                               name="titre" value="{{ old('titre') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="description" rows="3" required>{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Temps (min) *</label>
                            <input type="number" class="form-control" name="temps_preparation"
                                   value="{{ old('temps_preparation', $recette->temps_preparation ?? '') }}" required min="1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Portions *</label>
                            <input type="number" class="form-control" name="portions" placeholder="ex: 4 ou 1"
                                   value="{{ old('portions', $recette->portions ?? 2) }}" required min="1">
                            <small class="text-muted" style="font-size: 0.7em;">Personnes ou Pièces (ex: 1 tarte)</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Difficulté *</label>
                            <select class="form-select" name="difficulte" required>
                                <option value="facile"    {{ old('difficulte', $recette->difficulte ?? '') == 'facile'    ? 'selected' : '' }}>Facile</option>
                                <option value="moyen"     {{ old('difficulte', $recette->difficulte ?? '') == 'moyen'     ? 'selected' : '' }}>Moyen</option>
                                <option value="difficile" {{ old('difficulte', $recette->difficulte ?? '') == 'difficile' ? 'selected' : '' }}>Difficile</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Régime</label>
                            <select class="form-select" name="regime_alimentaire">
                                <option value="normal"      {{ old('regime_alimentaire', $recette->regime_alimentaire ?? '') == 'normal'      ? 'selected' : '' }}>Normal</option>
                                <option value="vegetarien"  {{ old('regime_alimentaire', $recette->regime_alimentaire ?? '') == 'vegetarien'  ? 'selected' : '' }}>Végétarien</option>
                                <option value="vegan"       {{ old('regime_alimentaire', $recette->regime_alimentaire ?? '') == 'vegan'       ? 'selected' : '' }}>Vegan</option>
                                <option value="sans_gluten" {{ old('regime_alimentaire', $recette->regime_alimentaire ?? '') == 'sans_gluten' ? 'selected' : '' }}>Sans gluten</option>
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

                    {{-- LA LISTE DES INGREDIENTS DOIT ETRE A L'INTERIEUR DU FORMULAIRE --}}
                    <div id="ingredientsList">
                        @if(old('ingredients'))
                            @foreach(old('ingredients') as $idx => $ing)
                                <div class="ingredient-row card card-body mb-2 py-2 px-3">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label small mb-1">Nom *</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="ingredients[{{ $idx }}][nom]" value="{{ $ing['nom'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small mb-1">Quantité *</label>
                                            <input type="number" step="0.1" class="form-control form-control-sm"
                                                   name="ingredients[{{ $idx }}][quantite]" value="{{ $ing['quantite'] ?? '' }}" required min="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small mb-1">Unité *</label>
                                            <select class="form-select form-select-sm" name="ingredients[{{ $idx }}][unite]">
                                                @foreach(['g', 'kg', 'ml', 'cl', 'l', 'pièce', 'pincée'] as $u)
                                                    <option value="{{ $u }}" {{ (isset($ing['unite']) && $ing['unite'] == $u) ? 'selected' : '' }}>{{ $u }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small mb-1">Nature *</label>
                                            <select class="form-select form-select-sm" name="ingredients[{{ $idx }}][nature]">
                                                <option value="solide" {{ (isset($ing['nature']) && $ing['nature'] == 'solide') ? 'selected' : '' }}>Solide</option>
                                                <option value="liquide" {{ (isset($ing['nature']) && $ing['nature'] == 'liquide') ? 'selected' : '' }}>Liquide</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm supprimerIngredient" title="Supprimer">✕</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
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

{{-- Le template caché DOIT être en dehors du formulaire --}}
<template id="ingredientTemplate">
    <div class="ingredient-row card card-body mb-2 py-2 px-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Nom *</label>
                <input type="text" class="form-control form-control-sm"
                       name="ingredients[__INDEX__][nom]" placeholder="ex: Farine" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Quantité *</label>
                <input type="number" step="0.1" class="form-control form-control-sm"
                       name="ingredients[__INDEX__][quantite]" placeholder="ex: 200" required min="0">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Unité *</label>
                <select class="form-select form-select-sm" name="ingredients[__INDEX__][unite]">
                    <option value="g">g</option><option value="kg">kg</option><option value="ml">ml</option>
                    <option value="cl">cl</option><option value="l">l</option><option value="pièce">pièce(s)</option><option value="pincée">pincée(s)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Nature *</label>
                <select class="form-select form-select-sm" name="ingredients[__INDEX__][nature]">
                    <option value="solide">Solide</option>
                    <option value="liquide">Liquide</option>
                </select>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm supprimerIngredient" title="Supprimer">✕</button>
            </div>
        </div>
    </div>
</template>

<script>
    let ingredientIndex = parseInt("{{ is_array(old('ingredients')) ? count(old('ingredients')) : 0 }}", 10) || 0;
    
    const liste     = document.getElementById('ingredientsList');
    const aucun     = document.getElementById('aucunIngredient');
    const template  = document.getElementById('ingredientTemplate');

    function updateAucun() {
        aucun.style.display = liste.children.length === 0 ? 'block' : 'none';
    }

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

    updateAucun();
</script>
@endsection