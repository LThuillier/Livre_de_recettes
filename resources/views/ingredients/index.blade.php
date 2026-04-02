@extends('recettes.layout') {{-- Vérifie que c'est bien ton layout habituel --}}

@section('title', 'Bibliothèque des Ingrédients')

@section('content')
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
    <h1>Bibliothèque des Ingrédients</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('recettes.index') }}" class="btn btn-secondary shadow-sm">Retour aux Recettes</a>
    
        <a href="{{ route('ingredients.create') }}" class="btn btn-primary shadow-sm">Ajouter un ingrédient</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nom de l'ingrédient</th>
                    <th>Unité par défaut</th>
                    <th>Nombre de recettes liées</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingredients as $ingredient)
                <tr>
                    <td class="ps-3">{{ $ingredient->id }}</td>
                    <td class="fw-bold">{{ $ingredient->nom }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $ingredient->unite_defaut ?? 'g' }}</span></td>
                    <td>
                        {{-- Vérifie le nom de ta variable pour le count, souvent recettes_count --}}
                        <span class="badge bg-info text-white rounded-pill px-3 py-2">
                            {{ $ingredient->recettes_count ?? 0 }} recette(s)
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">
                            {{-- Bouton Modifier --}}
                            <a href="{{ route('ingredients.edit', $ingredient) }}" class="btn btn-sm btn-outline-primary">
                                Modifier
                            </a>
                            
                            {{-- Bouton Supprimer (doit être un formulaire pour des raisons de sécurité dans Laravel) --}}
                            <form action="{{ route('ingredients.destroy', $ingredient) }}" method="POST" onsubmit="return confirm('Es-tu sûr de vouloir supprimer cet ingrédient ? Attention, cela pourrait impacter les recettes associées.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection