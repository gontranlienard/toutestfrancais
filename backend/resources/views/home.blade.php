@extends('layouts.app')

@section('content')

<h2>
    @if(request('q'))
        Résultats pour : "{{ request('q') }}"
    @else
       <h2>
    Je compare {{ number_format($countCompared, 0, ',', ' ') }} produits.
</h2>
    @endif
</h2>
<p></p>
<div class="product-grid">
    @forelse($products as $product)
        @include('components.product-card', ['product' => $product])
    @empty
        <p>Aucun produit trouvé.</p>
    @endforelse
</div>

<div class="pagination">
    {{ $products->links() }}
</div>

@endsection





