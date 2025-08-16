@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Résultats WHOIS</div>

                <div class="card-body">
                    <div  style="margin: 3%;" class="mt-3">
                        <a href="{{ route('whois.index') }}" class="btn btn-primary">Nouvelle recherche</a>
                    </div>

                    <table class="table">
                        <tbody>
                            @foreach($whoisData as $key => $value)
                                <tr>
                                    <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                    <td>
                                        @if(is_array($value))
                                            <ul class="list-unstyled">
                                                @foreach($value as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
