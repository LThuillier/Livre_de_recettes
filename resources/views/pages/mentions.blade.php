@extends('recettes.layout')

@section('title', 'Mentions Légales')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Mentions Légales</h1>
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="fw-bold">1. Présentation du site</h5>
            <p>Le site "Livre de Recettes" est un projet à but strictement pédagogique réalisé dans le cadre de l'épreuve E6 du Brevet de Technicien Supérieur "Services Informatiques aux Organisations" (BTS SIO).</p>

            <h5 class="fw-bold mt-4">2. Éditeur du site</h5>
            <p>Ce site a été conçu et développé par :<br>
            <strong>Lise THUILLIER</strong><br>
            Étudiante en BTS SIO (Option SLAM)</p>

            <h5 class="fw-bold mt-4">3. Hébergement</h5>
            <p>Ce projet est hébergé sur une machine virtuelle (Serveur Ubuntu) fournie par l'établissement scolaire dans le cadre de l'examen.</p>

            <h5 class="fw-bold mt-4">4. Propriété intellectuelle</h5>
            <p>Le code source de ce projet (Laravel) est la propriété de son autrice. Les images et recettes utilisées sur le site le sont à titre d'illustration pour un projet étudiant non commercial.</p>
        </div>
    </div>
</div>
@endsection