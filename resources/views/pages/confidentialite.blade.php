@extends('recettes.layout')

@section('title', 'Politique de confidentialité')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Politique de confidentialité (RGPD)</h1>
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <p class="lead">La protection de vos données personnelles est une priorité, même dans le cadre d'un projet étudiant.</p>

            <h5 class="fw-bold mt-4">1. Données collectées</h5>
            <p>Lors de la création d'un compte sur le "Livre de Recettes", nous collectons uniquement les données suivantes :</p>
            <ul>
                <li>Votre nom (ou pseudonyme)</li>
                <li>Votre adresse e-mail</li>
                <li>Votre mot de passe (qui est chiffré de manière irréversible via l'algorithme Bcrypt avant d'être stocké dans notre base de données).</li>
            </ul>

            <h5 class="fw-bold mt-4">2. Utilisation des données</h5>
            <p>Vos données sont strictement utilisées pour vous permettre de vous connecter à votre espace personnel, d'ajouter, modifier ou supprimer vos propres recettes. Elles ne seront jamais vendues, cédées ou utilisées à des fins publicitaires.</p>

            <h5 class="fw-bold mt-4">3. Utilisation des Cookies</h5>
            <p>Ce site n'utilise <strong>aucun cookie de traçage ou publicitaire</strong>. Nous utilisons uniquement un cookie technique de "Session" généré par le framework Laravel. Ce cookie est strictement nécessaire au fonctionnement du site (il permet de vous maintenir connecté lorsque vous naviguez de page en page). Conformément à la réglementation de la CNIL, ce type de cookie technique est exempté du recueil de consentement.</p>

            <h5 class="fw-bold mt-4">4. Vos droits</h5>
            <p>Vous disposez d'un droit d'accès, de modification et de suppression de vos données. L'administrateur du site (Lise Thuillier) peut supprimer votre compte et toutes les données associées sur simple demande lors de la présentation de ce projet.</p>
        </div>
    </div>
</div>
@endsection