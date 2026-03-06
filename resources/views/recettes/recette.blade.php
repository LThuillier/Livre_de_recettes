@extends('recettes.layout')

@section('title', $recette->titre)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $recette->titre }}</h1>
    <div>
        <a href="{{ route('recettes.edit', $recette) }}" class="btn btn-success text-white">Modifier</a>
        <a href="{{ route('recettes.index') }}" class="btn btn-secondary">Retour</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <p><strong>Description :</strong> {{ $recette->description }}</p>
                <p><strong>Temps de préparation :</strong> {{ $recette->temps_preparation }} minutes</p>
                <p><strong>Difficulté :</strong> {{ ucfirst($recette->difficulte) }}</p>
                <p><strong>Régime alimentaire :</strong> {{ ucfirst($recette->regime_alimentaire) }}</p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>🛒 Liste des courses pour cette recette</h5>
            </div>
            <div class="card-body">
                @if($recette->ingredients->count() > 0)
                <ul class="list-group">
                    @foreach($recette->ingredients as $ingredient)
                    <li class="list-group-item">
                        {{ $ingredient->pivot->quantite }} {{ $ingredient->pivot->unite }} 
                        de {{ $ingredient->nom }}
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted">Aucun ingrédient ajouté pour cette recette.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection