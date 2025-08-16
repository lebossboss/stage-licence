@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-question-circle fa-3x text-primary mb-3"></i>
                    <h5 class="card-title text-primary">Qu'est-ce que le service WHOIS ?</h5>
                    <p class="card-text text-justify">
                        Le service WHOIS est un protocole qui permet d'interroger les bases de données des registres de noms de domaine pour obtenir des informations sur un nom de domaine ou une adresse IP.
                        Vous pouvez utiliser cet outil pour trouver des informations telles que le propriétaire d'un nom de domaine, les dates d'enregistrement et d'expiration, les serveurs de noms, etc.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4>Recherche WHOIS</h4></div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('whois.lookup') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="domain" class="form-label">Nom de domaine ou adresse IP</label>
                            <input type="text" class="form-control" id="domain" name="domain"
                                   placeholder="Entrez un nom de domaine (ex: example.com) ou une adresse IP (ex: 8.8.8.8)"
                                   value="{{ old('domain') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
