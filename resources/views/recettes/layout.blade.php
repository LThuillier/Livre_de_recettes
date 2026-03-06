<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Livre de Recettes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="{{ route('recettes.index') }}">Livre de Recettes</a>
            
            <div class="navbar-nav ms-auto">
                @auth
                    <span class="navbar-text me-3">Bonjour {{ Auth::user()->name }}!</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Déconnexion</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                    <a class="nav-link" href="{{ route('register') }}">Inscription</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

    <footer class="mt-5 py-3 bg-light text-center">
        <div class="container">
            <p class="mb-0">© 2025 Livre de Recettes - BTS SIO</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>