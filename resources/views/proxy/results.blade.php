@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Résultats pour : {{ $results['target'] }}</h4>
                </div>
                <div class="card-body">
                    <p><strong>Adresse IP :</strong> {{ $results['ip'] }}</p>
                    <p><strong>WAF/Proxy détecté :</strong> {{ $results['waf'] }}</p>
                    <p><strong>Ports :</strong></p>
                    <ul>
                        @foreach($results['ports'] as $port)
                            <li>{{ $port['port'] }} ({{ $port['state'] }}): {{ $port['service'] }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('proxy.detector') }}" class="btn btn-primary">Nouvelle recherche</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
