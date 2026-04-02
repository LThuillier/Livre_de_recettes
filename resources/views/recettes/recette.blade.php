@extends('recettes.layout')

@section('title', $recette->titre)

@section('content')
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
    <h1 class="mb-0">{{ $recette->titre }}</h1>
    <div class="d-flex gap-2">
        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->id() === $recette->user_id))
            <a href="{{ route('recettes.edit', $recette) }}" class="btn btn-success text-white shadow-sm">Modifier</a>
        @endif
        
        <a href="{{ route('recettes.index') }}" class="btn btn-secondary shadow-sm">Retour</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        {{-- CARTE PRÉPARATION (Design de la Photo 1) --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <p class="card-text mb-4" style="line-height: 1.8;">
                    {!! nl2br(e($recette->description)) !!}
                </p>
                
                {{-- Les badges de préparation (Temps, Difficulté, Régime) --}}
                <div class="d-flex flex-wrap gap-3 border-top pt-4">
                    <span class="badge bg-white text-dark border px-4 py-2 fs-6 fw-bold">
                        {{ $recette->temps_formatte }}
                    </span>
                    <span class="badge bg-white text-dark border px-4 py-2 fs-6 fw-bold">
                        {{ ucfirst($recette->difficulte) }}
                    </span>
                    <span class="badge bg-white text-dark border px-4 py-2 fs-6 fw-bold">
                        {{ ucfirst(str_replace('_', ' ', $recette->regime_alimentaire)) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- CARTE INGRÉDIENTS (Design de la Photo 2) --}}
        <div class="card shadow-sm border-0">
            {{-- LE BANDEAU VERT : Ajout de bg-success et text-white --}}
            <div class="card-header bg-success text-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="mb-0 fw-bold">Liste des courses</h4>
                
                <div class="d-flex flex-wrap align-items-center bg-white rounded px-2 py-1 shadow-sm">
                    <label for="selectPortions" class="text-success fw-bold me-2 small mb-0">Pour</label>
                    {{-- LE SÉLECTEUR PORTIONS VERT : Ajout de border-success et text-success --}}
                    <select id="selectPortions" class="form-select form-select-sm border-success text-success fw-bold shadow-sm py-0" style="width: 65px; cursor: pointer;">
                        @for($i = 1; $i <= 15; $i++)
                            <option value="{{ $i }}" {{ $i == $recette->portions ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    <span class="text-success fw-bold ms-2 small">portions</span>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if($recette->ingredients->count() > 0)
                <ul class="list-group list-group-flush">
                    @foreach($recette->ingredients as $ingredient)
                    @php
                        $uniteOrigine = strtolower($ingredient->pivot->unite);
                        $estConvertible = in_array($uniteOrigine, ['g', 'kg', 'ml', 'cl', 'l']);
                    @endphp

                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center ingredient-item px-3 px-md-4 py-3 gap-2"
                        data-nom="{{ strtolower($ingredient->nom) }}"
                        data-qte-orig="{{ $ingredient->pivot->quantite }}"
                        data-unite-orig="{{ $uniteOrigine }}">
                        
                        <div class="text-break" style="flex: 1 1 auto;">
                            <span class="display-qte fs-5 fw-bold">{{ $ingredient->pivot->quantite }}</span> 
                            <span class="display-unite text-muted">{{ $ingredient->pivot->unite }}</span> 
                            de <strong class="ms-1">{{ ucfirst($ingredient->nom) }}</strong>
                        </div>

                        <div style="flex: 0 0 auto;">
                            @if($estConvertible)
                                {{-- LE SÉLECTEUR UNITÉ VERT : Ajout de border-success et text-success --}}
                                <select class="form-select form-select-sm unit-selector border-success text-success fw-bold shadow-sm" style="width: auto; cursor: pointer;">
                                    <option value="g"  {{ $uniteOrigine == 'g'  ? 'selected' : '' }}>g</option>
                                    <option value="kg" {{ $uniteOrigine == 'kg' ? 'selected' : '' }}>kg</option>
                                    <option value="ml" {{ $uniteOrigine == 'ml' ? 'selected' : '' }}>ml</option>
                                    <option value="cl" {{ $uniteOrigine == 'cl' ? 'selected' : '' }}>cl</option>
                                    <option value="l"  {{ $uniteOrigine == 'l'  ? 'selected' : '' }}>l</option>
                                </select>
                            @else
                                <span class="badge bg-light text-secondary border px-2 py-1">{{ $ingredient->pivot->unite }}</span>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted p-4 fst-italic mb-0">Aucun ingrédient ajouté pour cette recette.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const basePortions = {{ $recette->portions ?? 2 }}; 
        const selectPortions = document.getElementById('selectPortions');
        const items = document.querySelectorAll('.ingredient-item');
        const densites = { 'miel': 1.4, 'sirop': 1.3, 'huile': 0.9, 'lait': 1.03, 'eau': 1.0, 'creme': 0.95, 'crème': 0.95 };

        if(selectPortions) {
            selectPortions.addEventListener('change', recalculerIngredients);
        }

        items.forEach(item => {
            const selectUnite = item.querySelector('.unit-selector');
            if (selectUnite) {
                selectUnite.addEventListener('change', recalculerIngredients);
            }
        });

        function recalculerIngredients() {
            const currentPortions = parseInt(selectPortions.value, 10);
            const ratioPortions = currentPortions / basePortions;

            items.forEach(item => {
                const nom = item.getAttribute('data-nom');
                const origQte = parseFloat(item.getAttribute('data-qte-orig'));
                const origUnite = item.getAttribute('data-unite-orig');
                const selectUnite = item.querySelector('.unit-selector');
                const targetUnite = selectUnite ? selectUnite.value : origUnite;

                const spanQte = item.querySelector('.display-qte');
                const spanUnite = item.querySelector('.display-unite');

                let qteCalculee = origQte * ratioPortions;

                if (targetUnite !== origUnite) {
                    const origIsWeight = ['g', 'kg'].includes(origUnite);
                    const origIsVolume = ['ml', 'cl', 'l'].includes(origUnite);
                    const targetIsWeight = ['g', 'kg'].includes(targetUnite);
                    const targetIsVolume = ['ml', 'cl', 'l'].includes(targetUnite);

                    let densite = 1.0;
                    for (let cle in densites) {
                        if (nom.includes(cle)) {
                            densite = densites[cle];
                            break;
                        }
                    }

                    let baseGrams = 0;
                    if (origIsWeight) {
                        baseGrams = (origUnite === 'kg') ? qteCalculee * 1000 : qteCalculee;
                    } else if (origIsVolume) {
                        let baseMl = (origUnite === 'l') ? qteCalculee * 1000 : ((origUnite === 'cl') ? qteCalculee * 10 : qteCalculee);
                        baseGrams = baseMl * densite;
                    }

                    let result = 0;
                    if (targetIsWeight) {
                        result = (targetUnite === 'kg') ? baseGrams / 1000 : baseGrams;
                    } else if (targetIsVolume) {
                        let resultMl = baseGrams / densite;
                        result = (targetUnite === 'l') ? resultMl / 1000 : ((targetUnite === 'cl') ? resultMl / 10 : resultMl);
                    }
                    
                    qteCalculee = result;
                }

                qteCalculee = Math.round(qteCalculee * 100) / 100;
                spanQte.textContent = qteCalculee;
                spanUnite.textContent = targetUnite;
            });
        }
    });
</script>
@endsection