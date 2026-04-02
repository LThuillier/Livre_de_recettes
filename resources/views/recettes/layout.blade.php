<!DOCTYPE html>

<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title') - Livre de Recettes</title>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://www.google.com/search?q=https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
<div class="container">
<a class="navbar-brand fw-bold" href="{{ route('recettes.index') }}">
<i class="fas fa-utensils me-2"></i>Livre de Recettes
</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Liens à gauche -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('recettes.index') }}">Recettes</a>
                </li>
                @hasrole('admin')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('ingredients.index') }}">
                        <i class="fas fa-list me-1"></i>Bibliothèque Ingrédients
                    </a>
                </li>
                @endhasrole
            </ul>
            
            <!-- Liens à droite (Auth) -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Bonjour <strong>{{ Auth::user()->name }}</strong> !
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- Contenu principal -->
<div class="container mt-4 mb-5">
    @yield('content')
</div>

<!-- Footer -->
<footer class="py-4 bg-light border-top text-center">
    <div class="container">
        <p class="text-muted mb-0">© 2025 Livre de Recettes - Projet BTS SIO</p>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle (nécessaire pour le dropdown et le menu mobile) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>