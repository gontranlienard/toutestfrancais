@extends('layouts.app')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">

    <h1 style="font-size:22px; font-weight:500;">
        {{ $brand->name }}
    </h1>

    <span style="color:#6B7280; font-size:14px;">
        {{ $products->total() }} produits trouvés
    </span>

</div>

@if($products->count())

    <div class="product-grid">
        @foreach($products as $product)
            @include('components.product-card', ['product' => $product])
        @endforeach
    </div>

    <div style="margin-top:30px;">
        {{ $products->links('vendor.pagination.default') }}
    </div>

@else
    <p>Aucun produit trouvé pour cette marque.</p>
@endif

@endsection