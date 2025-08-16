@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-md-12 text-center">
            <h1 class="display-4 mb-3" style="color: #007bff;">Dydy solutions</h1>
             <p class="lead">Votre suite complète d'outils d'analyse réseau</p>
            <div class="text-content-and-buttons">

                <p class="centrer">
                <ul class="nav">
                    <li class="nav-item">
                        <a style="border: 2px solid #007bff;" class="nav-link inscription-button {{ request()->routeIs('register.index') ? 'active' : '' }}" href="{{ route('register.index') }}">
                            <i class="fas fa-user-plus me-1"></i> Inscription
                        </a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link connexion-button" href="{{ route('login') }}" role="button">
                                <i class="fas fa-user-plus me-1"></i> Connexion
                            </a>
                        </li>
                </ul>
                </p>
            </div>
            <p >Des outils professionnels pour l'analyse de domaines, d'adresses IP et la sécurité réseau</p>
            <hr class="my-4">
        </div>
    </div>

    <!-- Services Section -->
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-search fa-3x mb-3 text-primary"></i>
                    <h3 class="card-title">Service WHOIS</h3>
                    <p class="card-text">Obtenez des informations détaillées sur n'importe quel nom de domaine, propriétaire et dates d'enregistrement.</p>
                    <a href="{{ route('whois.index') }}" class="btn btn-outline-primary mt-3">Découvrir</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-network-wired fa-3x mb-3 text-primary"></i>
                    <h3 class="card-title">Mon IP</h3>
                    <p class="card-text">Identifiez votre adresse IP publique et obtenez des informations géographiques détaillées.</p>
                    <a href="{{ route('ip.index') }}" class="btn btn-outline-primary mt-3">Vérifier</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-3x mb-3 text-primary"></i>
                    <h3 class="card-title">Nmap</h3>
                    <p class="card-text">Analysez les ports et services réseau pour évaluer la sécurité de votre infrastructure.</p>
                    <br>
                    <a href="{{ route('nmap.index') }}" class="btn btn-outline-primary mt-3">Scanner</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-info-circle fa-3x mb-3 text-primary"></i>
                    <h3 class="card-title">Support</h3>
                    <p class="card-text">Besoin d'aide ? Consultez la section Aide ou contactez notre équipe pour obtenir assistance et informations.</p>

                    <button class="btn btn-outline-primary mt-3" data-bs-toggle="modal" data-bs-target="#helpModal"> Aide</button>
                    <button class="btn btn-outline-primary mt-3" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="p-5 bg-light rounded">
                <h2 class="text-center mb-4" style="color: #007bff;"><strong>Pourquoi choisir Dydy solutions ?</strong></h2>
                <div class="row">
                    <div class="col-md-4 text-center">
                        <i class="fas fa-bolt fa-2x mb-3 text-primary"></i>
                        <h4>Rapide et Efficace</h4>
                        <p>Résultats instantanés pour tous vos besoins d'analyse réseau</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-lock fa-2x mb-3 text-primary"></i>
                        <h4>Sécurisé</h4>
                        <p>Vos données sont protégées selon notre politique de confidentialité</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-tools fa-2x mb-3 text-primary"></i>
                        <h4>Outils Professionnels</h4>
                        <p>Suite complète d'outils pour les professionnels du réseau</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legal Links -->
    <div class="row">
        <div class="col-12 text-center">
            <div class="legal-links">
                <button class="btn btn-link text-primary" data-bs-toggle="modal" data-bs-target="#legalModal">Mentions légales</button>
                <span class="mx-2">|</span>
                <button class="btn btn-link text-primary" data-bs-toggle="modal" data-bs-target="#privacyModal">Confidentialité</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for the page -->
<style>
    .text-content-and-buttons {
        display: flex;
        flex-direction: column;
        align-items: center; /* To maintain horizontal centering */
        width: 100%; /* Ensure it takes full width to center content */
    }

    .text-content-and-buttons > .lead {
        order: 1 !important;
        margin-bottom: 20px !important; /* Add space below the lead text */
    }

    .text-content-and-buttons > .centrer {
        order: 2 !important;
        margin-top: 20px !important; /* Adjust this value as needed to move the buttons down */
    }
    .card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
    }

    .lead {
        font-size: 1.5rem;
        color: #4a5568;
    }

    .display-4 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 1.5rem;
    }

    .btn-outline-primary {
        border-width: 2px;
        font-weight: 500;
        padding: 0.5rem 1.5rem;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 123, 255, 0.15);
    }

    h2 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 2rem;
    }

    h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .fas {
        color: #007bff;
    }

    .bg-light {
        background-color: #f8fafc !important;
    }

    .legal-links {
        margin-top: 2rem;
    }

    .btn-link {
        text-decoration: none;
        font-weight: 500;
    }

    .btn-link:hover {
        text-decoration: underline;
    }
</style>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
