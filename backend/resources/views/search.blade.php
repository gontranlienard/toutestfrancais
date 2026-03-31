@extends('layouts.app')

@section('title', 'Recherche "' . request('q') . '"')

@section('content')

<h2>J'ai trouvé {{ $products->total() }} produits.</h2>
<p></p>
<div class="product-grid">

    @foreach($products as $product)
    <div class="product-card">

        <div class="product-image">
            <a href="{{ route('product.show', $product->slug) }}">
                <img 
                    src="{{ $product->image }}" 
                    alt="{{ $product->name }}"
                >
            </a>
        </div>

        <div class="product-info">

            <h3 class="product-title">
                {{ $product->name }}
            </h3>

            <div class="product-brand-row">
                <a class="brand-link" href="#">
                    {{ $product->brand->name ?? '' }}
                </a>
            </div>

            <div class="price-block">
                <span class="price-label">Dès</span>
                <span class="current-price">
                    {{ number_format($product->offers_min_price ?? 0, 2, ',', ' ') }} €
                </span>
            </div>

            <a 
                class="btn-product"
                href="{{ route('product.show', $product->slug) }}"
            >
                Voir le produit
            </a>

        </div>

    </div>
    @endforeach

</div>

<div style="margin-top:30px;">
    {{ $products->links('vendor.pagination.default') }}
</div>

@endsection