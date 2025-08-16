@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Résultats pour l'organisation</h1>

    @if($cities->isNotEmpty())
        @foreach($cities as $city)
            <div class="card mt-4">
                <div class="card-header">
                    {{ $city->city }}
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
                    <h6>IPs associées :</h6>
                    <ul class="list-group">
                        @foreach($city->ips as $ip)
                            <li class="list-group-item">{{ $ip->ip_address }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-warning mt-4">
            Aucune information trouvée pour cette organisation.
        </div>
    @endif
</div>
@endsection
