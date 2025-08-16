@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Résultats pour l'adresse IP</h1>

    @if($ip)
        <div class="card mt-4">
            <div class="card-header">
                {{ $ip->ip_address }}
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ $ip->city->city }}</h5>
                <p class="card-text">
                    <strong>Plage IP:</strong> {{ $ip->city->netrange }}<br>
                    <strong>CIDR:</strong> {{ $ip->city->cidr }}<br>
                    <strong>Nom du réseau:</strong> {{ $ip->city->netname }}<br>
                    <strong>Organisation:</strong> {{ $ip->city->organization }}<br>
                    <strong>Admin:</strong> {{ $ip->city->admin_name }} ({{ $ip->city->admin_email }})<br>
                    <strong>Technique:</strong> {{ $ip->city->tech_name }} ({{ $ip->city->tech_email }})
                </p>
            </div>
        </div>
    @else
        <div class="alert alert-warning mt-4">
            Aucune information trouvée pour cette adresse IP.
        </div>
    @endif
</div>
@endsection
