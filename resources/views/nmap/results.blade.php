@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Résultats du scan pour {{ $target }}</h4>
                </div>
                <div style="margin: 3%;" class="mt-3">
                        <a href="{{ route('nmap.index') }}" class="btn btn-primary">Nouveau Scan</a>
                    </div>
                <div class="card-body">
                    @php
                        $tcpPorts = array_filter($parsedResults['ports'], fn($p) => $p['protocol'] === 'tcp');
                        $udpPorts = array_filter($parsedResults['ports'], fn($p) => $p['protocol'] === 'udp');
                    @endphp

                    @if(count($tcpPorts) > 0)
                        <h5 class="mt-4">Ports TCP Détectés</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Port</th>
                                        <th>Protocole</th>
                                        <th>État</th>
                                        <th>Service</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tcpPorts as $port)
                                    <tr>
                                        <td>{{ $port['port'] }}</td>
                                        <td>{{ $port['protocol'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $port['state'] == 'open' ? 'success' : 'danger' }}">
                                                {{ $port['state'] === 'open' ? 'ouvert' : $port['state'] }}
                                            </span>
                                        </td>
                                        <td>{{ $port['service'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(count($udpPorts) > 0)
                        <h5 class="mt-4">Ports UDP Détectés</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Port</th>
                                        <th>Protocole</th>
                                        <th>État</th>
                                        <th>Service</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($udpPorts as $port)
                                    <tr>
                                        <td>{{ $port['port'] }}</td>
                                        <td>{{ $port['protocol'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $port['state'] == 'open' ? 'success' : 'danger' }}">
                                                {{ $port['state'] === 'open' ? 'ouvert' : $port['state'] }}
                                            </span>
                                        </td>
                                        <td>{{ $port['service'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(!empty($parsedResults['os']['details']))
                        <h5 class="mt-4">Système d'exploitation détecté</h5>
                        <p>{{ $parsedResults['os']['details'] }}</p>
                    @endif

                    <h5 class="mt-4">Sortie brute de Nmap</h5>
                    <div class="bg-dark text-light p-3 rounded" style="background-color: #000 !important;">
                        <pre style="white-space: pre-wrap; color: white; background: none;">{{ $rawOutput }}</pre>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
