@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h5 class="card-title text-primary">Qu'est-ce que le scanner Nmap ?</h5>
                    <p class="card-text text-justify">
                        Nmap (Network Mapper) est un outil de sécurité open source utilisé pour la découverte de réseaux et l'audit de sécurité.
                        Il permet de découvrir des hôtes et des services sur un réseau informatique en envoyant des paquets et en analysant les réponses.
                        Cet outil peut fournir des informations sur les ports ouverts, les services en cours d'exécution et le système d'exploitation d'une cible.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Scanner Nmap</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('nmap.scan') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="target" class="form-label">Adresse IP ou nom de domaine à scanner</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="target" name="target"
                                       placeholder="Exemple: 192.168.1.1 ou exemple.com" required>
                                <button type="submit" class="btn btn-primary">Effectuer le scan</button>
                            </div>
                            @error('target')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
