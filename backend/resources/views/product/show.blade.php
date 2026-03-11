@extends('layouts.app')

@section('content')

<div class="product-page">

    {{-- HEADER --}}
    <div class="product-header">
        <h1>{{ $product->name }}</h1>

        @if($product->brand)
            <a href="{{ route('brand.show', $product->brand->slug) }}" class="brand-link">
                {{ $product->brand->name }}
            </a>
        @endif
    </div>

    {{-- LAYOUT 2 COLONNES --}}
    <div class="product-layout">

        {{-- IMAGE --}}
        <div class="product-image-box">
            <img src="{{ $product->image }}" alt="{{ $product->name }}">
        </div>

        {{-- OFFRES --}}
        <div class="offers-section">

    <h2>Offres disponibles</h2>

    @forelse($offers as $offer)

        <div class="offer-row {{ $loop->first ? 'best-offer' : '' }}">

            <div class="offer-left">
                <span class="offer-site">{{ $offer->site->name }}</span>
                @if($loop->first)
                    <span class="badge-best">Meilleur prix</span>
                @endif
            </div>

            <div class="offer-price">
                {{ number_format($offer->price, 2, ',', ' ') }} €
            </div>

            <div class="offer-action">
                <a href="{{ $offer->url }}"
                   target="_blank"
                   rel="nofollow noopener"
                   class="btn-offer">
                    Voir l'offre
                </a>
            </div>

        </div>

    @empty
        <p>Aucune offre disponible.</p>
    @endforelse

</div>

    </div>

</div>

@endsection