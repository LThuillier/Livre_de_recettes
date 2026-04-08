# LivreDeRecettes - Projet BTS SIO SLAM

Projet web Laravel de gestion de recettes (site, authentification, CRUD recettes/ingredients, gestion des roles, protection anti brute-force, tests BDD/TDD).

## Objectif du README
Ce document donne a l examinateur toutes les etapes pour lancer et verifier le projet rapidement.

## Lancement rapide
```bash
cp .env.example .env
composer install
php artisan key:generate
# configurer la base dans .env (MySQL ou SQLite)
php artisan migrate --seed
php artisan db:seed --class=RoleAndUserSeeder
npm install
npm run build
php artisan serve
```

Sous Windows/WAMP (PowerShell), utiliser:
```powershell
Copy-Item .env.example .env -Force
composer install
php artisan key:generate
# Configurer DB_CONNECTION / DB_DATABASE / DB_USERNAME / DB_PASSWORD dans .env
php artisan migrate --seed
php artisan db:seed --class=RoleAndUserSeeder
npm install
npm run build
php artisan serve
```

Option SQLite locale (rapide pour demo):
```powershell
New-Item -ItemType File -Path .\database\database.sqlite -Force
# puis dans .env:
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
```

## Ce que fait la preparation
- installe les dependances PHP/JS
- initialise la cle application
- cree les tables via migrations
- cree un utilisateur de demo
- cree les roles/permissions et un compte admin

## URLs utiles
- Site (liste recettes): `http://127.0.0.1:8000/recettes`
- Racine (redirection): `http://127.0.0.1:8000/`
- Connexion: `http://127.0.0.1:8000/login`
- Inscription: `http://127.0.0.1:8000/register`
- Ingredients: `http://127.0.0.1:8000/ingredients`
- Dashboard admin: `http://127.0.0.1:8000/admin/dashboard`
- Mentions legales: `http://127.0.0.1:8000/mentions-legales`
- Politique de confidentialite: `http://127.0.0.1:8000/politique-confidentialite`

## Comptes de demonstration
- `test@example.com` / `password` : utilisateur demo (cree par `DatabaseSeeder`)
- `adminrecette@gmail.com` / `Administrateur1!` : compte admin (cree par `RoleAndUserSeeder`)

## Donnees fictives generees
- 1 utilisateur demo (`test@example.com`)
- roles `admin` et `user`
- permissions de gestion des recettes
- compte admin preconfigure

Remarque: aucune recette n est prechargee par defaut. Les recettes de demo sont a creer via l interface.

Regenerer les donnees:
```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=RoleAndUserSeeder
```

## Parcours de verification conseille (5 a 10 min)
1. Ouvrir `/recettes` en invite et verifier que seules les recettes publiques sont visibles.
2. Se connecter avec `test@example.com`.
3. Creer une recette avec ingredients (inclure un ingredient liquide en `cl/ml/l`).
4. Verifier la conversion automatique des ingredients liquides en grammes.
5. Se connecter en admin et verifier la visibilite globale des recettes.
6. Tester la protection anti brute-force sur `/login`.
7. Lancer les tests automatiques pour prouver la non regression.

## Tests automatiques
```bash
php artisan test
```

Sous Windows (si Xdebug provoque des warnings):
```powershell
$env:XDEBUG_MODE='off'; php artisan test
```

Etat actuel attendu:
- 16 tests passes
- 45 assertions

## Protection anti brute-force (Fail2Ban applicatif)
Protection applicative integree (active automatiquement):
- apres `FAIL2BAN_MAX_ATTEMPTS` echecs de connexion depuis une meme IP, l IP est bloquee temporairement
- blocage applique meme si l utilisateur n existe pas
- deblocage automatique a la fin de la duree de ban
- remise a zero du compteur apres connexion reussie

Variables `.env`:
- `FAIL2BAN_ENABLED=true`
- `FAIL2BAN_MAX_ATTEMPTS=5`
- `FAIL2BAN_FIND_TIME_MINUTES=10`
- `FAIL2BAN_BAN_MINUTES=3`

Demonstration rapide:
1. Aller sur `http://127.0.0.1:8000/login`
2. Envoyer 5 fois un mauvais mot de passe
3. Verifier le message de blocage (erreur de type throttle)
4. Reessayer depuis la meme IP avec le bon mot de passe: acces refuse jusqu a expiration du ban

## Fichiers importants
- `routes/web.php`
- `app/Http/Controllers/RecetteController.php`
- `app/Http/Controllers/IngredientController.php`
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Services/Security/Fail2BanService.php`
- `app/Http/Middleware/Fail2BanMiddleware.php`
- `config/fail2ban.php`
- `database/seeders/DatabaseSeeder.php`
- `database/seeders/RoleAndUserSeeder.php`
- `tests/Feature/Fail2BanTest.php`
- `tests/Feature/RecetteBddTest.php`
- `tests/Unit/RecetteTddTest.php`

## Documentation complementaire
- `Dossier_E5 Laravel.docx`
- `documentation_larave.docx`
- `Diagramme de classe.png`
- `Diagramme de sequence.png`
- `diagram.puml`
- `sequence.puml`
