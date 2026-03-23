@extends('recettes.layout')

@section('title', 'Toutes les recettes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Mes Recettes</h1>
        @auth
        <a href="{{ route('recettes.create') }}" class="btn btn-success">Nouvelle Recette</a>
        @endauth
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    @foreach($recettes as $recette)
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $recette->titre }}</h5>
                <p class="text-muted mb-2">
                    <i class="fas fa-user"></i> 
                        Posté par : <strong>{{ $recette->user ? $recette->user->name : 'Anonyme' }}</strong>
                </p>
                <p class="card-text">
                    <small class="text-muted">
                        ⏱️ {{ $recette->temps_preparation }} min | 
                        🎚️ {{ $recette->difficulte }} | 
                        🌱 {{ $recette->regime_alimentaire }}
                    </small>
                </p>
                <p class="card-text">{{ Str::limit($recette->description, 100) }}</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('recettes.recette', $recette) }}" class="btn btn-outline-primary btn-sm">Voir</a>

                    @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->id() === $recette->user_id)) // IGNORE : Affichage conditionnel des boutons de modification/suppression

                    <a href="{{ route('recettes.edit', $recette) }}" class="btn btn-outline-secondary btn-sm">Modifier</a>
                    <form action="{{ route('recettes.destroy', $recette) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Supprimer cette recette ?')">Supprimer</button>// IGNORE : Ajout de la confirmation de suppression
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection