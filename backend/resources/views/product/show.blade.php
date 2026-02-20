@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        {{-- IMAGE --}}
        <div class="col-md-5">

            @if($product->image)
                <img src="{{ $product->image }}"
                     class="img-fluid"
                     style="max-height:450px;object-fit:contain;">
            @else
                <div style="height:400px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;">
                    Image indisponible
                </div>
            @endif

        </div>

        {{-- INFOS --}}
        <div class="col-md-7">

            <h1>{{ $product->name }}</h1>

            @if($product->brand)
                <p><strong>Marque :</strong> {{ $product->brand }}</p>
            @endif

            @if($product->offers->count())
                <h3 class="text-danger">
                    À partir de {{ number_format($product->offers->min('price'), 2, ',', ' ') }} €
                </h3>
            @endif

            <hr>

            <h4>Offres disponibles</h4>

            @forelse($product->offers as $offer)

                <div class="card mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>
                            <strong>{{ $offer->site->name ?? 'Site inconnu' }}</strong><br>
                            Prix : {{ number_format($offer->price, 2, ',', ' ') }} €
                        </div>

                        <a href="{{ $product->url }}"
                           target="_blank"
                           class="btn btn-success">
                            Voir sur le site
                        </a>

                    </div>
                </div>

            @empty
                <p>Aucune offre disponible.</p>
            @endforelse

            <a href="{{ route('home') }}" class="btn btn-secondary mt-3">
                ← Retour au comparateur
            </a>

        </div>

    </div>

</div>

@endsection
