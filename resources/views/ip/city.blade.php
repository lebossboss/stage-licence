@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Résultats pour la ville de {{ $city->city }}</h1>

    <div class="card mt-4">
        <div class="card-header">
            Informations WHOIS
        </div>
        <div class="card-body">
            <p class="card-text">
                <strong>Plage IP:</strong> {{ $city->netrange }}<br>
                <strong>CIDR:</strong> {{ $city->cidr }}<br>
                <strong>Nom du réseau:</strong> {{ $city->netname }}<br>
                <strong>Organisation:</strong> {{ $city->organization }}<br>
                <strong>Admin:</strong> {{ $city->admin_name }} ({{ $city->admin_email }})<br>
                <strong>Technique:</strong> {{ $city->tech_name }} ({{ $city->tech_email }})
            </p>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            IPs de la ville
        </div>
        <div class="card-body">
            <ul class="list-group">
                @foreach($city->ips as $ip)
                    <li class="list-group-item">{{ $ip->ip_address }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
