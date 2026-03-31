@extends('layouts.app')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:10px;">

    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">

        <h1 style="font-size:22px; font-weight:500; margin:0;">
            {{ $category->name }}
        </h1>

        @if(isset($breadcrumb) && count($breadcrumb))
            <div class="breadcrumb">
                Vous êtes :
                @foreach($breadcrumb as $cat)
                    <a href="{{ route('category.show', implode('/', array_map(fn($c) => $c->slug, array_slice($breadcrumb, 0, $loop->index + 1)))) }}">
                        {{ $cat->name }}
                    </a>
                    @if(!$loop->last)
                        <span class="sep">›</span>
                    @endif
                @endforeach
            </div>
        @endif

    </div>

    <span style="color:#6B7280; font-size:14px;">
        {{ $products->total() }} produits trouvés
    </span>

</div>

<div class="product-grid">
    @foreach($products as $product)
        @include('components.product-card', ['product' => $product])
    @endforeach
</div>

<div style="margin-top:30px;">
    {{ $products->links('vendor.pagination.default') }}
</div>

@endsection