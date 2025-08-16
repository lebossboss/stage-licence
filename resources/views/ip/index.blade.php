@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">
                       <div style="font-size: 2rem"> <i class="fas fa-network-wired me-1"></i> </div><br>
                        Qu'est-ce qu'un Scanner de réseau ?
                    </h5>
                    <p class="card-text text-justify">
                        Un scanner de réseau est un outil qui analyse les dispositifs connectés à un réseau.
                        Il identifie les adresses IP actives, les ports ouverts et les services disponibles.
                        Exemples : Nmap, Angry IP Scanner, Wireshark (pour l'analyse avancée).
                        Utilisations : sécurité, dépannage réseau ou cartographie d'infrastructure.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            {!! $localIpView !!}
            <br>
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Votre Adresse IP public sur internet</h4>
                </div>
                <div class="card-body">
                    @if(isset($ipInfo['error']))
                        <div class="alert alert-danger">
                            {{ $ipInfo['error'] }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>IP Publique</th>
                                        <td>{{ $ipInfo['query'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pays</th>
                                        <td>{{ $ipInfo['country'] ?? 'N/A' }} ({{ $ipInfo['countryCode'] ?? 'N/A' }})</td>
                                    </tr>
                                    <tr>
                                        <th>Région</th>
                                        <td>{{ $ipInfo['regionName'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ville</th>
                                        <td>{{ $ipInfo['city'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Code Postal</th>
                                        <td>{{ $ipInfo['zip'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>FAI</th>
                                        <td>{{ $ipInfo['isp'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Organisation</th>
                                        <td>{{ $ipInfo['org'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>AS</th>
                                        <td>{{ $ipInfo['as'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fuseau horaire</th>
                                        <td>{{ $ipInfo['timezone'] ?? 'N/A' }}</td>
                                    </tr>
                                    @if(isset($ipInfo['lat']) && isset($ipInfo['lon']))
                                    <tr>
                                        <th>Coordonnées</th>
                                        <td>{{ $ipInfo['lat'] }}, {{ $ipInfo['lon'] }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h3 style="text-align: center; color: #007bff !important;   font-weight: 700; font-size: 2.5rem;">Recherche avancée</h3>
        <br>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Par ville</div>
                    <div class="card-body">
                        <form action="{{ route('city-ips.by-city') }}" method="GET">
                            <div class="form-group">
                                <input type="text" name="city" class="form-control" placeholder="Nom de la ville">
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Rechercher</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Par IP/Plage</div>
                    <div class="card-body">
                        <form action="{{ route('city-ips.by-ip') }}" method="GET" class="mb-3">
                            <div class="form-group">
                                <input type="text" name="ip" class="form-control" placeholder="Adresse IP">
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Rechercher IP</button>
                        </form>
                        <form action="{{ route('city-ips.by-range') }}" method="GET">
                            <div class="form-group">
                                <input type="text" name="range" class="form-control" placeholder="Plage IP">
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Rechercher plage</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Par contact</div>
                    <div class="card-body">
                        <form action="{{ route('city-ips.by-contact') }}" method="GET">
                            <div class="form-group">
                                <input type="text" name="contact" class="form-control" placeholder="Nom ou email">
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Rechercher</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">Par organisation</div>
                    <div class="card-body">
                        <form action="{{ route('city-ips.by-organization') }}" method="GET">
                            <div class="form-group">
                                <input type="text" name="organization" class="form-control" placeholder="Nom organisation">
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Rechercher</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
