@extends('layout')

@section('heading')
Crypto Price
@endsection

@section('content')


@if ($cryptos->isNotEmpty())
    <div class="container-fluid last-check">
        <p>As at {{ date('jS M, Y g:iA', strtotime($cryptos->last()->date)) }}. </p>
    </div>
    
    <div class="row crypto-row">
        @foreach ($cryptos as $crypto)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-content">
                        <h4> {{ $crypto->asset->name }} ({{ Str::upper($crypto->asset->code ) }})</h4>
                        <img class="asseticon" src="{{ $crypto->asset->icon }}" alt="">
                        <hr>
                        @if (number_format($crypto->amount_kes, 2) > 0)
                            <p>KES {{ number_format($crypto->amount_kes, 2) }}</p>
                        @else
                        <p><span>N/A</span></p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="data-display-error"><div class="alert alert-danger" role="alert">No data found. Please check back later.</div></div>
@endif	


@endsection