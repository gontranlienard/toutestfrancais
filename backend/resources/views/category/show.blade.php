@extends('layouts.app')

@section('content')

<div class="category-header">
    <h1>{{ $category->name }}</h1>
    <p>{{ $products->total() }} produits trouvés</p>
</div>

<div class="products-grid">
    @forelse($products as $product)
        <div class="product-card">

            <div class="product-image">
                @if($product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->name }}">
                @endif
            </div>

            <div class="product-body">
                <h3>{{ $product->name }}</h3>

                @if($product->offers_min_price)
                    <p class="price">
                        À partir de
                        {{ number_format($product->offers_min_price, 2, ',', ' ') }} €
                    </p>
                @else
                    <p class="no-price">Prix indisponible</p>
                @endif

                <a href="#" class="btn">
                    Voir le prix
                </a>
            </div>

        </div>
    @empty
        <p>Aucun produit trouvé.</p>
    @endforelse
</div>

<div class="pagination-wrapper">
    {{ $products->links() }}
</div>

@endsection
