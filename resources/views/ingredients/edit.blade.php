@extends('recettes.layout')

@section('title', 'Modifier un ingrédient')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Modifier l'ingrédient</h1>
    <a href="{{ route('ingredients.index') }}" class="btn btn-secondary shadow-sm">Retour à la liste</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        {{-- L'action pointe vers ingredients.update avec l'ID de l'ingrédient --}}
        <form action="{{ route('ingredients.update', $ingredient->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Indispensable pour la modification dans Laravel --}}

            <div class="mb-3">
                <label for="nom" class="form-label fw-bold">Nom de l'ingrédient *</label>
                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $ingredient->nom) }}" required>
                @error('nom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="unite_mesure" class="form-label fw-bold">Unité par défaut</label>
                <select class="form-select @error('unite_mesure') is-invalid @enderror" id="unite_mesure" name="unite_mesure">
                    <option value="g" {{ old('unite_mesure', $ingredient->unite_mesure) == 'g' ? 'selected' : '' }}>Grammes (g)</option>
                    <option value="kg" {{ old('unite_mesure', $ingredient->unite_mesure) == 'kg' ? 'selected' : '' }}>Kilogrammes (kg)</option>
                    <option value="ml" {{ old('unite_mesure', $ingredient->unite_mesure) == 'ml' ? 'selected' : '' }}>Millilitres (ml)</option>
                    <option value="cl" {{ old('unite_mesure', $ingredient->unite_mesure) == 'cl' ? 'selected' : '' }}>Centilitres (cl)</option>
                    <option value="l" {{ old('unite_mesure', $ingredient->unite_mesure) == 'l' ? 'selected' : '' }}>Litres (l)</option>
                    <option value="pièce" {{ old('unite_mesure', $ingredient->unite_mesure) == 'pièce' ? 'selected' : '' }}>Pièce / Unité</option>
                    <option value="pincée" {{ old('unite_mesure', $ingredient->unite_mesure) == 'pincée' ? 'selected' : '' }}>Pincée</option>
                </select>
                @error('unite_mesure')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary text-white fw-bold shadow-sm">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection