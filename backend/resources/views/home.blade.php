@extends('layouts.app')

@section('content')

<h2>
    @if(request('q'))
        Résultats pour : "{{ request('q') }}"
    @else
        Produits récents
    @endif
</h2>

<div class="product-grid">
    @forelse($products as $product)
        @include('components.product-card', ['product' => $product])
    @empty
        <p>Aucun produit trouvé.</p>
    @endforelse
</div>

<div class="pagination">
    {{ $products->links('vendor.pagination.default') }}
</div>

@endsection





